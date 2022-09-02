<?php

namespace App\Admin\Controllers;

use App\Models\RpaCommand;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use DB;
use Illuminate\Http\Request;

class RpaCommandController extends Controller {

    use HasResourceActions;

    var $rp;
    var $rps;
    var $comm;
    var $categories;

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

    public function showRpacommand($rpa, $command, Content $content) {
        $this->rps = $rpa;
        $this->comm = $command;
        if (is_numeric($command)) {
            if (strlen($command) > 9) {
                return view('operacion.404');
            } else {
                return $content
                                ->header('Detail')
                                ->description('description')
                                ->body($this->detail($command));
            }
        } else {
            return view('operacion.404');
        }
    }

    public function Delete($id, $data) {
        $forach = explode(',', $data);
        foreach ($forach as $eli) {
            \App\Models\RpaCommand::destroy($eli);
        }
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content) {
        return $content
                        ->header('Detail')
                        ->description('description')
                        ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, $rpa, Content $content) {
        if (strlen($id) > 9) {
            return view('operacion.404');
        } else {
            if (is_numeric($id)) {
                $exist = array();
                foreach (DB::table('rpa_command')->select('step')->where('id', '=', $rpa)->where('id_rpa', '=', $id)->get() as $data) {
                    $exist[] = $data->step;
                }
                if (count($exist) > 0) {
                    return $content
                                    ->header('Edit')
                                    ->description('description')
                                    ->body($this->formedit($id, $rpa)->edit($rpa));
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
    public function create($id, Content $content) {
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
                                    ->header('Create')
                                    ->description('description')
                                    ->body($this->form($id));
                } else {
                    return view('operacion.404');
                }
            } else {
                return view('operacion.404');
            }
        }
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $grid = new Grid(new RpaCommand);
        $grid->id('ID');
        $grid->id_rpa('Id rpa');
        $grid->step('Step');
        $grid->command('Command');
        $grid->type('Type');
        $grid->column('properties')->display(function () {
            $valor = str_replace("[", "", $this->properties);
            $valor1 = str_replace("]", "", $valor);
            $valor3 = str_replace("\\\\", "\\", $valor1);
            return $valor3;
        });
        $grid->nextYes('Next yes');
        $grid->nextNo('Next no');
        $grid->model()->orderBy('id', 'desc');
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
        });
        $grid->filter(function (Grid\Filter $filter) {
            foreach (DB::table('rpa')->select('name', 'id')->get() as $cat) {
                $categories[$cat->id] = $cat->name;
            }
            $filter->like('id_rpa')->select($categories);
            $filter->like('step');
            $filter->like('command');
            $filter->like('type');
            $filter->like('properties');
            $filter->like('nextYes');
            $filter->like('nextNo');
        });
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
        $show = new Show(RpaCommand::findOrFail($id));
        $show->step('Step');
        $show->command('Command');
        $show->type('Type');
        $show->properties()->as(function () {
            $valor = str_replace("[", "", $this->properties);
            $valor1 = str_replace("]", "", $valor);
            $valor2 = str_replace("\"", "", $valor1);
            $valor3 = str_replace("\\\\", "\\", $valor2);
            return $valor3;
        });


        $show->id_rpa("Tipo de rpa")->as(function () {
            foreach (DB::table('rpa')->select('name')->where('id', '=', $this->id_rpa)->get() as $data) {
                $this->categories = $data->name;
            }

            return $this->categories;
        });
        $show->nextYes('Next yes');
        $show->nextNo('Next no');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id) {
        $form = new Form(new RpaCommand);
        foreach (DB::table('rpa')->select('id_rpa_type')->where('id', '=', $id)->get() as $cat) {
            $this->rpa = $cat->id_rpa_type;
        }
        $type_rpa = array();
        foreach (DB::table('type_rpa_command')->select('type_command')->where('type_rpa', '=', $this->rpa)->get() as $cat) {
            $type_rpa[$cat->type_command] = $cat->type_command;
        }
        $typec = array();
        foreach ($type_rpa as $r) {
            foreach (DB::table('type_commands')->select('tipo', 'id')->where('id', '=', $r)->get() as $cat) {
                $typec[$cat->tipo] = $cat->tipo;
            }
        }
        $tabledata = DB::table('rpa_command')
                ->select('id', 'orden', 'step', 'command', 'type', 'properties', 'nextYes', 'nextNo')
                ->where('id_rpa', '=', $id)
                ->orderBy('orden', 'desc')
                ->get();


        $data['table'] = $tabledata;
        $data['datos'] = $typec;
        $data['id'] = $id;
        $form->html(view("operacion.create", $data));

        $form->disableEditingCheck();
        $form->disableCreatingCheck();
        $form->disableViewCheck();
        $form->disableSubmit();
        $form->disableReset();

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
            $tools->disableList();
            foreach (DB::table('url')->select('link')->where('name', '=', 'com_create')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            $tools->add(view('button.data', $data));
        });
        return $form;
    }

    protected function formedit($id, $rpa) {
        $form = new Form(new RpaCommand);
        foreach (DB::table('rpa')->select('id_rpa_type')->where('id', '=', $id)->get() as $cat) {
            $this->rpa = $cat->id_rpa_type;
        }
        foreach (DB::table('type_rpa_command')->select('type_command')->where('type_rpa', '=', $this->rpa)->get() as $cat) {
            $type_rpa[$cat->type_command] = $cat->type_command;
        }
        foreach ($type_rpa as $r) {
            foreach (DB::table('type_commands')->select('tipo', 'id')->where('id', '=', $r)->get() as $cat) {
                $typec[$cat->tipo] = $cat->tipo;
            }
        }


        $rpaCommand = RpaCommand::findOrFail($rpa);

        $data['typec'] = $typec;
        $data['data'] = $rpaCommand;
        $data['id'] = $id;
        $data['rpa'] = $rpa;
        $form->html(view("operacion.edit", $data));
        $form->disableEditingCheck();

        $form->disableCreatingCheck();

        $form->disableViewCheck();
        $form->disableSubmit();
        $form->disableReset();

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
            $tools->disableList();
            foreach (DB::table('url')->select('link')->where('name', '=', 'com_create')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            $tools->add(view('button.data', $data));
        });
        return $form;
    }

    public function InserData(Request $request) {
        $valor;
        try {
            $data = new RpaCommand();

            $validacion = DB::table('type_commands')->select('tipo')->where('tipo', '=', $request['type'])->get();
            if (count($validacion) != 0) {
                $data->id_rpa = $request['id_rpa'];
                $data->step = $request['step'];
                $data->command = $request['command'];
                $data->type = $request['type'];

                if (!empty($request['properties']) && $request['properties'] != "") {
                    $data->properties = json_encode($request['properties']);
                } else {
                    $data->properties = '[0]';
                }
                if ($data->properties == "\"[]\"") {
                    $data->properties = '[0]';
                }
                $data->orden = $request['orden'];
                $data->nextYes = $request['nextYes'];
                $data->nextNo = $request['nextNo'];
                $data->save();
                $valor = "bien";
            } else {
                $valor = "fallo";
            }
        } catch (\Exception $e) {
            $valor = "fallo";
        }
        return $valor;
    }

    public function UpdateData(Request $request) {
        $valor;
        try {
            $data = RpaCommand::find($request['id']);
            $validacion = DB::table('type_commands')->select('tipo')->where('tipo', '=', $request['type'])->get();
            if (count($validacion) != 0) {
                $data->step = $request['step'];
                $data->command = $request['command'];
                $data->type = $request['type'];
                if (!empty($request['properties']) && $request['properties'] != "") {
                    $data->properties = json_encode($request['properties']);
                } else {
                    $data->properties = '[0]';
                }

                if ($data->properties == "\"[]\"") {
                    $data->properties = '[0]';
                }

                $data->orden = $request['orden'];
                $data->nextYes = $request['nextYes'];
                $data->nextNo = $request['nextNo'];
                $data->save();


                $valor = "bien";
            } else {
                $valor = "fallo";
            }
        } catch (\Exception $e) {
            $valor = "fallo";
        }
        return $valor;
    }

    public function DeleteCommand(Request $request) {
        try {
            RpaCommand::where('id', '=', $request['id'])->delete();
            echo "<script type='text/javascript'>
     Swal.fire(
                                    'Envio correcto',
                                    'Sus datos fueron se eliminados satisfactoriamente',
                                    'success'
                                    );
                                    window.history.back();
                            </script>";
        } catch (\Exception $e) {
            echo "<script type='text/javascript'>
                               Swal.fire({
                                type: 'error',
                                title: 'Oops...',
                                text: 'Al parecer no se puede eliminar este elemento'
                                    });
                                    window.history.back();
                            </script>";
        }
    }

}
