<?php

namespace App\Admin\Controllers;

use App\Jobs\RunSingleRPAJob;
use App\Models\Rpa;
use App\Models\RpaProperty;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RpaController extends Controller {

    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content) {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Destroy command.
     *
     * @param
     * @return

    public function Delete($id, $data) {
     * $forach = explode(',', $data);
     * foreach ($forach as $eli) {
     * \App\Models\RpaCommand::destroy($eli);
     * }
     * }
     *      */

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content) {
        if (is_numeric($id)) {
            if (strlen($id) > 9) {
                return view('operacion.404');
            } else {
                return $content
                    ->header('Detail')
                    ->description('description')
                    ->body($this->detail($id));
            }
        } else {
            return view('operacion.404');
        }
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content) {
        if (strlen($id) > 9) {
            return view('operacion.404');
        } else {
            if (is_numeric($id)) {
                $exist = array();
                foreach (DB::table('rpa')->select('name')->where('id', '=', $id)->get() as $data) {
                    $exist[] = $data->name;
                }

                if (count($exist) > 0) {
                    return $content
                        ->header('Edit')
                        ->description('description')
                        ->body($this->formedit($id)->edit($id));
                } else {
                    return view('operacion.404');
                }
            } else {
                return view('operacion.404');
            }
        }
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content) {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $grid = new Grid(new Rpa);
        $grid->id('ID');
        $grid->name('Name');
        $grid->header(function () {
            foreach (DB::table('url')->select('link')->where('name', '=', 'bot_show')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            return view('button.data', $data);
        });

        $grid->column('Tipo rpa')->display(function () {
            $categories = "";
            foreach (DB::table('rpa_type')->select('name')->where('id', '=', $this->id_rpa_type)->get() as $data) {
                $categories = $data->name;
            }
            return ucwords($categories);
        });
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->column('Total comandos')->display(function () {
            $categoriese = DB::table('rpa_command')
                ->where('id_rpa', '=', $this->id)
                ->count();
            return $categoriese;
        });
        $grid->actions(function ($actions) {
//    $actions->disableEdit();
        });

        $grid->filter(function (Grid\Filter $filter) {
            $categoriesq = array();
            foreach (DB::table('rpa_type')->select('name', 'id')->get() as $cat) {
                $categoriesq[$cat->id] = $cat->name;
            }
            $filter->like('id_rpa_type', 'Tipo de rpa')->select($categoriesq);
            $filter->like('name');
        });
        $grid->status('Estado');
        $grid->model()->orderBy('id', 'desc');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id) {

        $show = new Show(Rpa::findOrFail($id));
        $show->id('ID')->as(function () {
            $sup = $this->id;
            return $sup;
        });
        $show->name('Name');
        $show->status('Estado');
        $categories = array();
        $show->id_rpa_type("Tipo de rpa")->as(function () {
            foreach (DB::table('rpa_type')->select('name')->where('id', '=', $this->id_rpa_type)->get() as $data) {
                $categories = $data->name;
            }
            return ucwords($categories);
        });

        $show->created_at('Created');
        $show->updated_at('Updated');

        $show->rpaCommand('Rpa commands', function ($line) use ($id) {
            $line->id('ID');
            /* $line->column('Rpa')->display(function () {

              foreach (DB::table('rpa')->select('name')->where('id', '=', $this->id_rpa)->get() as $data) {
              $categories = $data->name;
              }
              return ucwords($categories);
              }); */
            $line->header(function () use ($id) {
                foreach (DB::table('url')->select('link')->where('name', '=', 'bot_detail')->get() as $uri) {
                    $uril = $uri->link;
                }
                $data['link'] = $uril;
                $rpa['id'] = $id;
                return view('button.data', $data) . view('rpa.ejecutarydetener', $rpa);
            });
            $line->step('Step');
            $line->command('Command')->display(function () {
                $cmd = htmlspecialchars($this->command);

                if (strlen($cmd) < 20) {
                    return strlen(trim($cmd)) > 0 ? $cmd : '""';
                } else {
                    $cmdCut = substr($cmd, 0, 20) . "...";
                    if (strlen($cmd) < 150) {
                        return '<span title="'.$cmd.'">' . $cmdCut . "</span>";
                    } else {
                        $cmdCut2 = substr($cmd, 0, 150) . "...";
                        return '<span title="'.$cmdCut2.'">' . $cmdCut . "</span>";

                    }
                }
            });
            $line->type('Tipo');
            $line->column('Properties')->display(function () {
                $props = "[ ]";
                if ($this->properties && $this->properties != 'null' && $this->properties != null) {
                    $propsJson = json_decode($this->properties);
                    $props = join(";", $propsJson);
                }
                $props = htmlspecialchars($props);

                if (strlen($props) < 20) {
                    return strlen(trim($props)) > 0 ? $props : "[ ]";
                } else {
                    $propsCut = substr($props, 0, 20) . "...";
                    if (strlen($props) < 150) {
                        return '<span title="'.$props.'">' . $propsCut . "</span>";
                    } else {
                        $propsCut2 = substr($props, 0, 150) . "...";
                        return '<span title="'.$propsCut2.'">' . $propsCut . "</span>";

                    }
                }
            });
            $line->nextYes('Next yes');
//$line->nextNo('Next no');
            $line->model()->orderBy('orden', 'asc');
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form(new Rpa);
        foreach (DB::table('rpa_orquestacion')->select('name', 'id')->get() as $orq) {
            $orquestacion[$orq->id] = $orq->name;
        }
        $data = array();

        foreach (DB::table('rpa_type')->select('name', 'id')->get() as $cat) {
            $data[$cat->id] = $cat->name;
        }
        $h = "";
        $i = 0;
        foreach ($data as $key => $value) {
            if ($i == 0) {
                $h .= '<option value="0" disabled selected>Seleccione un valor</option>';
            }
            $h .= '<option value="' . $key . '">' . $value . '</option>';

            $i = 39;
        }
        $datos['type'] = $h;
        $form->html(view('rpa.rpacreate', $datos));
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();
        $form->disableSubmit();
        $form->disableReset();

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
            $tools->disableList();
            foreach (DB::table('url')->select('link')->where('name', '=', 'bot_create')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            $tools->add(view('button.data', $data));
        });
        return $form;
    }

    protected function formedit($id) {
        $h = "";
        $c = "";
        $js = "";
        $dd = "";
        $dl = "";
        $ht = "";
        $us = "";
        $pss = "";
        $csv = "";
        $name = "";
        $time = "";
        $sitema_destino = "";
        $form = new Form(new Rpa);

        foreach (DB::table('rpa_orquestacion')->select('name', 'id')->get() as $orq) {
            $orquestacion[$orq->id] = $orq->name;
        }

        foreach (DB::table('rpa_type')->select('name', 'id')->get() as $cat) {
            $data[$cat->id] = $cat->name;
        }


        foreach (DB::table('rpa')->select('id_operacionmultiplex', 'name', 'sitema_destino', 'id_rpa_type')->where('id', '=', $id)->get() as $orq) {
            $name = $orq->name;
            $multiplex = $orq->id_operacionmultiplex;
            $sitema_destino = $orq->sitema_destino;
            $id_rpa_type = $orq->id_rpa_type;
        }

        foreach ($data as $key => $value) {
            if ($key != $id_rpa_type) {
                $h .= '<option value="' . $key . '">' . $value . '</option>';
            } else {
                $h .= '<option selected value="' . $key . '">' . $value . '</option>';
            }
        }

        if ($multiplex == 1) {
            $c = '<input type="checkbox" id="multiplex" checked>Si lo selecciona este se encargara de ejecutar operaciones repetitivas';
        } else {
            $c = '<input type="checkbox" id="multiplex" >Si lo selecciona este se encargara de ejecutar operaciones repetitivas';
        }
        if ($multiplex == 1) {
            if ($id_rpa_type == 1 || $id_rpa_type == 4) {
                $js = '$("#hosti").css("display", "block");
        $("#useri").css("display", "block");
        $("#downloadi").css("display", "none");
        $("#driveri").css("display", "none");
        $("#passwordi").css("display", "block");
        $("#cargcsv").css("display", "block");';
            } else if ($id_rpa_type == 2) {
                $js = '$("#hosti").css("display", "none");
        $("#useri").css("display", "none");
        $("#downloadi").css("display", "block");
        $("#driveri").css("display", "block");
        $("#passwordi").css("display", "none");
        $("#cargcsv").css("display", "block");';
            } else if ($id_rpa_type == 3) {
                $js = '$("#hosti").css("display", "none");
        $("#useri").css("display", "none");
        $("#downloadi").css("display", "none");
        $("#driveri").css("display", "block");
        $("#passwordi").css("display", "none");
        $("#cargcsv").css("display", "block");';
            }
        } else {

            if ($id_rpa_type == 1 || $id_rpa_type == 4) {
                $js = '$("#hosti").css("display", "block");
        $("#useri").css("display", "block");
        $("#downloadi").css("display", "none");
        $("#driveri").css("display", "none");
        $("#passwordi").css("display", "block");
        $("#cargcsv").css("display", "none");';
            } else if ($id_rpa_type == 2) {
                $js = '$("#hosti").css("display", "none");
        $("#useri").css("display", "none");
        $("#downloadi").css("display", "block");
        $("#driveri").css("display", "block");
        $("#passwordi").css("display", "none");
        $("#cargcsv").css("display", "none");';
            } else if ($id_rpa_type == 3) {
                $js = '$("#hosti").css("display", "none");
        $("#useri").css("display", "none");
        $("#downloadi").css("display", "none");
        $("#driveri").css("display", "block");
        $("#passwordi").css("display", "none");
        $("#cargcsv").css("display", "none");';
            }
        }
        if ($multiplex == 1) {
            if ($id_rpa_type == 1 || $id_rpa_type == 4) {
                foreach (DB::table('rpa_property')
                             ->select('name', 'value')
                             ->where('id_rpa', '=', $id)
                             ->get() as $cat) {
                    $dat[$cat->name] = $cat->value;
                }
                $pss = $dat['password'];
                $us = $dat['user'];
                $ht = $dat['host'];
                $csv = $dat['pathcsv'];
                $time = $dat['timeSleep'];
            } else if ($id_rpa_type == 2) {
                foreach (DB::table('rpa_property')
                             ->select('name', 'value')
                             ->where('id_rpa', '=', $id)
                             ->get() as $cat) {
                    $dat[$cat->name] = $cat->value;
                }
                $dl = $dat['driverLocation'];
                $dd = $dat['downloadLocation'];
                $csv = $dat['pathcsv'];
                $time = $dat['timeSleep'];
            } else if ($id_rpa_type == 3) {
                foreach (DB::table('rpa_property')
                             ->select('name', 'value')
                             ->where('id_rpa', '=', $id)
                             ->get() as $cat) {
                    $dat[$cat->name] = $cat->value;
                }
                $dl = $dat['driverLocation'];
                $csv = $dat['pathcsv'];
                $time = $dat['timeSleep'];
            }
        } else {
            if ($id_rpa_type == 1 || $id_rpa_type == 4) {
                foreach (DB::table('rpa_property')
                             ->select('name', 'value')
                             ->where('id_rpa', '=', $id)
                             ->get() as $cat) {
                    $dat[$cat->name] = $cat->value;
                }
                $pss = $dat['password'];
                $us = $dat['user'];
                $ht = $dat['host'];
                $time = $dat['timeSleep'];
            } else if ($id_rpa_type == 2) {
                foreach (DB::table('rpa_property')
                             ->select('name', 'value')
                             ->where('id_rpa', '=', $id)
                             ->get() as $cat) {
                    $dat[$cat->name] = $cat->value;
                }
                $dl = $dat['driverLocation'];
                $dd = $dat['downloadLocation'];
                $time = $dat['timeSleep'];
            } else if ($id_rpa_type == 3) {
                foreach (DB::table('rpa_property')
                             ->select('name', 'value')
                             ->where('id_rpa', '=', $id)
                             ->get() as $cat) {
                    $dat[$cat->name] = $cat->value;
                }
                $dl = $dat['driverLocation'];
                $time = $dat['timeSleep'];
            }
        }

        $datos['js'] = $js;
        $datos['check'] = $c;
        $datos['type'] = $h;
        $datos['host'] = $ht;
        $datos['user'] = $us;
        $datos['driver'] = $dl;
        $datos['download'] = $dd;
        $datos['pass'] = $pss;
        $datos['csv'] = $csv;
        $datos['time'] = $time;
        $datos['id'] = $id;
        $datos['name'] = $name;
        $datos['sis'] = $sitema_destino;
        $form->html(view('rpa.rpaedit', $datos));
        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();
        $form->disableSubmit();
        $form->disableReset();

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
            $tools->disableList();
            foreach (DB::table('url')->select('link')->where('name', '=', 'bot_create')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            $tools->add(view('button.data', $data));
        });
        return $form;
    }

    public function callDataDb(Request $request) {
        try {
            $this->dispatch(new RunSingleRPAJob($request->all()));
        } catch (\Exception $e) {

            Log::info($e);
        }
    }

    public function InsertRpa(Request $request) {
        $value;
        try {
            $data = new Rpa();
            $data1 = new RpaProperty();
            $data2 = new RpaProperty();
            $data3 = new RpaProperty();
            $data4 = new RpaProperty();
            $data5 = new RpaProperty();
            $data0 = new RpaProperty();
            $validacionp = DB::table('rpa')->select('name')->where('name', '=', $request['name'])->get();
            if (count($validacionp) == 0) {
                $validacion = DB::table('type_commands')->select('tipo')->where('tipo', '=', $request['type'])->get();
                if (!empty($validacion)) {

                    if ($request['multiplex'] == 2) {
                        $data->name = $request['name'];
                        $data->sitema_destino = $request['sis'];
                        $data->id_rpa_type = $request['type'];
                        $data->id_operacionmultiplex = $request['multiplex'];
                        $data->save();
                        $data0->id_rpa = $data->id;
                        $data0->name = "timeSleep";
                        if (!is_numeric($request['sleep']) || $request['sleep'] == "") {
                            $data0->value = 1000;
                        } else {
                            $data0->value = $request['sleep'];
                        }
                        $data0->save();
                        if ($request['type'] == 1 || $request['type'] == 4) {
                            $data1->id_rpa = $data->id;
                            $data1->name = "host";
                            $data1->value = $request['host'];
                            $data1->save();

                            $data2->id_rpa = $data->id;
                            $data2->name = "user";
                            $data2->value = $request['user'];
                            $data2->save();

                            $data3->id_rpa = $data->id;
                            $data3->name = "password";
                            $data3->value = $request['pass'];
                            $data3->save();

                            $data4->id_rpa = $data->id;
                            $data4->name = "maxIterations";
                            $data4->value = "1000";
                            $data4->save();

                            $value = "bien";
                        } else if ($request['type'] == 2) {
                            $data1->id_rpa = $data->id;
                            $data1->name = "downloadLocation";
                            $data1->value = $request['download'];
                            $data1->save();

                            $data2->id_rpa = $data->id;
                            $data2->name = "driverLocation";
                            $data2->value = $request['driver'];
                            $data2->save();

                            $data3->id_rpa = $data->id;
                            $data3->name = "maxIterations";
                            $data3->value = "1000";
                            $data3->save();
                            $value = "bien";
                        } else if ($request['type'] == 3) {
                            $data2->id_rpa = $data->id;
                            $data2->name = "driverLocation";
                            $data2->value = $request['driver'];
                            $data2->save();

                            $data3->id_rpa = $data->id;
                            $data3->name = "maxIterations";
                            $data3->value = "1000";
                            $data3->save();
                            $value = "bien";
                        } else {
                            $value = 'fallo';
                        }
                    } else {
                        if ($request['csv'] == "") {
                            $value = "faltaarchivocesv";
                        } else {
                            $data->name = $request['name'];
                            $data->sitema_destino = $request['sis'];
                            $data->id_rpa_type = $request['type'];
                            $data->id_operacionmultiplex = $request['multiplex'];
                            $data->save();
                            $data0->id_rpa = $data->id;
                            $data0->name = "timeSleep";
                            if (!is_numeric($request['sleep']) || $request['sleep'] == "") {
                                $data0->value = 1000;
                            } else {
                                $data0->value = $request['sleep'];
                            }
                            $data0->save();
                            if ($request['type'] == 1 || $request['type'] == 4) {
                                $data1->id_rpa = $data->id;
                                $data1->name = "host";
                                $data1->value = $request['host'];
                                $data1->save();

                                $data2->id_rpa = $data->id;
                                $data2->name = "user";
                                $data2->value = $request['user'];
                                $data2->save();

                                $data3->id_rpa = $data->id;
                                $data3->name = "password";
                                $data3->value = $request['pass'];
                                $data3->save();

                                $data4->id_rpa = $data->id;
                                $data4->name = "maxIterations";
                                $data4->value = "1000";
                                $data4->save();

                                $data5->id_rpa = $data->id;
                                $data5->name = "pathcsv";
                                $data5->value = $request['csv'];
                                $data5->save();


                                $value = "bien";
                            } else if ($request['type'] == 2) {
                                $data1->id_rpa = $data->id;
                                $data1->name = "downloadLocation";
                                $data1->value = $request['download'];
                                $data1->save();

                                $data2->id_rpa = $data->id;
                                $data2->name = "driverLocation";
                                $data2->value = $request['driver'];
                                $data2->save();

                                $data3->id_rpa = $data->id;
                                $data3->name = "maxIterations";
                                $data3->value = "1000";
                                $data3->save();

                                $data5->id_rpa = $data->id;
                                $data5->name = "pathcsv";
                                $data5->value = $request['csv'];
                                $data5->save();


                                $value = "bien";
                            } else if ($request['type'] == 3) {
                                $data2->id_rpa = $data->id;
                                $data2->name = "driverLocation";
                                $data2->value = $request['driver'];
                                $data2->save();

                                $data3->id_rpa = $data->id;
                                $data3->name = "maxIterations";
                                $data3->value = "1000";
                                $data3->save();

                                $data5->id_rpa = $data->id;
                                $data5->name = "pathcsv";
                                $data5->value = $request['csv'];
                                $data5->save();


                                $value = "bien";
                            } else {
                                $value = 'fallo';
                            }
                        }
                    }
                } else {
                    $value = 'typenoexiste';
                }
            } else {
                $value = 'existe';
            }
        } catch (\Exception $e) {
            dd($e);
            $value = "fallo";
        }
        return $value;
    }

    public function UpdateRpa(Request $request) {
        $value;
        try {
            $data1 = new RpaProperty();
            $data2 = new RpaProperty();
            $data3 = new RpaProperty();
            $data4 = new RpaProperty();
            $data5 = new RpaProperty();
            $data0 = new RpaProperty();

            $validacionp = DB::table('rpa')
                ->select('name')
                ->where('name', '=', $request['name'])
                ->where('id', '!=', $request['id'])
                ->get();
// dd($validacionp);

            if (count($validacionp) == 0) {
                $validacion = DB::table('type_commands')->select('tipo')->where('tipo', '=', $request['type'])->get();
                if (!empty($validacion)) {
                    if ($request['multiplex'] == 2) {
                        $data = Rpa::find($request['id']);
                        $data->sitema_destino = $request['sis'];
                        $data->id_rpa_type = $request['type'];
                        $data->id_operacionmultiplex = $request['multiplex'];
                        $data->save();
                        RpaProperty::where('id_rpa', '=', $request['id'])->delete();
                        $data0->id_rpa = $data->id;
                        $data0->name = "timeSleep";
                        if (!is_numeric($request['sleep']) || $request['sleep'] == "") {
                            $data0->value = 1000;
                        } else {
                            $data0->value = $request['sleep'];
                        }
                        $data0->save();

                        if ($request['type'] == 1 || $request['type'] == 4) {
                            $data1->id_rpa = $data->id;
                            $data1->name = "host";
                            $data1->value = $request['host'];
                            $data1->save();

                            $data2->id_rpa = $data->id;
                            $data2->name = "user";
                            $data2->value = $request['user'];
                            $data2->save();

                            $data3->id_rpa = $data->id;
                            $data3->name = "password";
                            $data3->value = $request['pass'];
                            $data3->save();

                            $data4->id_rpa = $data->id;
                            $data4->name = "maxIterations";
                            $data4->value = "1000";
                            $data4->save();

                            $value = "bien";
                        } else if ($request['type'] == 2) {
                            $data1->id_rpa = $data->id;
                            $data1->name = "downloadLocation";
                            $data1->value = $request['download'];
                            $data1->save();

                            $data2->id_rpa = $data->id;
                            $data2->name = "driverLocation";
                            $data2->value = $request['driver'];
                            $data2->save();

                            $data3->id_rpa = $data->id;
                            $data3->name = "maxIterations";
                            $data3->value = "1000";
                            $data3->save();
                            $value = "bien";
                        } else if ($request['type'] == 3) {
                            $data2->id_rpa = $data->id;
                            $data2->name = "driverLocation";
                            $data2->value = $request['driver'];
                            $data2->save();

                            $data3->id_rpa = $data->id;
                            $data3->name = "maxIterations";
                            $data3->value = "1000";
                            $data3->save();
                            $value = "bien";
                        } else {
                            $value = 'fallo';
                        }
                    } else {
                        if ($request['csv'] == "") {
                            $value = "faltaarchivocesv";
                        } else {
                            $data = Rpa::find($request['id']);
                            $data->sitema_destino = $request['sis'];
                            $data->id_rpa_type = $request['type'];
                            $data->id_operacionmultiplex = $request['multiplex'];
                            $data->save();
                            RpaProperty::where('id_rpa', '=', $request['id'])->delete();

                            $data0->id_rpa = $data->id;
                            $data0->name = "timeSleep";
                            if (!is_numeric($request['sleep']) || $request['sleep'] == "") {
                                $data0->value = 1000;
                            } else {
                                $data0->value = $request['sleep'];
                            }
                            $data0->save();

                            if ($request['type'] == 1 || $request['type'] == 4) {
                                $data1->id_rpa = $data->id;
                                $data1->name = "host";
                                $data1->value = $request['host'];
                                $data1->save();

                                $data2->id_rpa = $data->id;
                                $data2->name = "user";
                                $data2->value = $request['user'];
                                $data2->save();

                                $data3->id_rpa = $data->id;
                                $data3->name = "password";
                                $data3->value = $request['pass'];
                                $data3->save();

                                $data4->id_rpa = $data->id;
                                $data4->name = "maxIterations";
                                $data4->value = "1000";
                                $data4->save();

                                $data5->id_rpa = $data->id;
                                $data5->name = "pathcsv";
                                $data5->value = $request['csv'];
                                $data5->save();


                                $value = "bien";
                            } else if ($request['type'] == 2) {
                                $data1->id_rpa = $data->id;
                                $data1->name = "downloadLocation";
                                $data1->value = $request['download'];
                                $data1->save();

                                $data2->id_rpa = $data->id;
                                $data2->name = "driverLocation";
                                $data2->value = $request['driver'];
                                $data2->save();

                                $data3->id_rpa = $data->id;
                                $data3->name = "maxIterations";
                                $data3->value = "1000";
                                $data3->save();

                                $data5->id_rpa = $data->id;
                                $data5->name = "pathcsv";
                                $data5->value = $request['csv'];
                                $data5->save();


                                $value = "bien";
                            } else if ($request['type'] == 3) {
                                $data2->id_rpa = $data->id;
                                $data2->name = "driverLocation";
                                $data2->value = $request['driver'];
                                $data2->save();

                                $data3->id_rpa = $data->id;
                                $data3->name = "maxIterations";
                                $data3->value = "1000";
                                $data3->save();

                                $data5->id_rpa = $data->id;
                                $data5->name = "pathcsv";
                                $data5->value = $request['csv'];
                                $data5->save();


                                $value = "bien";
                            } else {
                                $value = 'fallo';
                            }
                        }
                    }
                } else {
                    $value = 'typenoexiste';
                }
            } else {
                $value = 'existe';
            }
        } catch (\Exception $e) {
            Log::error($e);
            $value = "fallo";
        }
        return $value;
    }

    public function detener(Request $request) {
        $rpa = Rpa::findOrFail($request['id']);
        if ($rpa->status == "EJECUTANDOSE...") {
            $rpa->status = "DETENIDO";
            $rpa->update();
        }
    }

}
