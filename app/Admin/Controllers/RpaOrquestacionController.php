<?php

namespace App\Admin\Controllers;

use App\Models\RpaOrquestacion;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use DB;
use Illuminate\Http\Request;

class RpaOrquestacionController extends Controller {

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
                foreach (DB::table('rpa_orquestacion')->select('name')->where('id', '=', $id)->get() as $data) {
                    $exist[] = $data->name;
                }
                if (count($exist) > 0) {
                    return $content
                                    ->header('Edit')
                                    ->description('description')
                                    ->body($this->form($id)->edit($id));
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
        $grid = new Grid(new RpaOrquestacion);
        $grid->header(function () {

            foreach (DB::table('url')->select('link')->where('name', '=', 'orq_show')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            return view('button.data', $data);
        });
        $grid->id('ID');
        $grid->name('Name');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        return $grid;
    }
    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id) {
        $show = new Show(RpaOrquestacion::findOrFail($id));

        $show->id('ID');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->rpa('Rpa', function ($line) use($id) {
            $line->header(function () use ($id) {
                foreach (DB::table('url')->select('link')->where('name', '=', 'orq_detail')->get() as $uri) {
                    $uril = $uri->link;
                }
                $data['link'] = $uril;
                $data['id'] = $id;
                return view('button.data', $data).view('orq.orquestacion', $data) ;
            });
            $line->id('ID');
            $line->column('Tipo rpa')->display(function () {
                $categories = "";
                foreach (DB::table('rpa')->select('name')->where('id', '=', $this->idrpa)->get() as $data) {
                    $categories = $data->name;
                }
                return ucwords($categories);
            });
            $line->created_at('Created at');
            $line->updated_at('Updated at');
            $line->column('Total comandos')->display(function () {
                $categoriese = DB::table('orq_rpa')
                        ->where('idorquestacion', '=', $this->id)
                        ->count();
                return $categoriese;
            });

            $line->disableActions();
            $line->disableCreateButton();
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form(new RpaOrquestacion);
        $form->text('name', 'Name')->rules('required');

        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            foreach (DB::table('url')->select('link')->where('name', '=', 'orq_create')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            $tools->add(view('button.data', $data));
        });
        return $form;
    }

    public function callOrq(Request $request) {
        $datacarg = 0;
        try {

            foreach (DB::table('orq_rpa')->select('idrpa')->where('idorquestacion', '=', $request['idorq'])->get() as $element) {
                $datose[$element->idrpa] = $element->idrpa;
            }

            foreach ($datose as $da) {
                foreach (DB::table('rpa')->select('id_rpa_type')->where('id', '=', $da)->get() as $dat) {
                    $categories = $dat->id_rpa_type;
                }
                $valor = $categories;

                foreach (DB::table('rpa')->select('name', 'sitema_destino', 'id_operacionmultiplex')->where('id', '=', $da)->get() as $dat) {
                    $cat = $dat->name;
                    $sysdes = $dat->sitema_destino;
                    $mul = $dat->id_operacionmultiplex;
                }

                foreach (DB::table('rpa_property')->select('name', 'value')->where('id_rpa', '=', $da)->get() as $dat) {
                    $filepath[$dat->name] = $dat->value;
                }
                $request['rpaName'] = $cat;
                $request['rpa_type'] = $valor;

                if (!empty($sysdes)) {
                    $request['systemDestination'] = $sysdes;
                } else {
                    $request['systemDestination'] = "vacio";
                }
                $valorcomun = array();
                $data = new \App\Http\Controllers\API\RpaController();

                if ($mul == 1) {
                    if (file_exists($filepath['pathcsv'])) {

                        if (($fichero = fopen($filepath['pathcsv'], "r")) !== FALSE) {
                            while (($datos = fgetcsv($fichero, 1000)) !== FALSE) {
                                $valorcomun[$datacarg] = $datos[0];
                                $datacarg++;
                            }
                        }

                        foreach ($valorcomun as $valorc) {
                            $parametro = strpos($valorc, ';');
                            if ($parametro == false) {
                                $request['parametro'] = $valorc . ';';
                            } else {
                                $request['parametro'] = $valorc;
                            }
                            $request['mul'] = 'si';
                            $request['orq'] = 'si';
                            if ($valor == 1) {
                                $data->transportedFile($request);
                            } else {
                                $data->callRpa($request);
                            }
                        }
                    } else {
                        $tran = new \App\Models\Transaction();
                        $tran->rpa = $request['rpaName'];
                        $tran->sistemadestino = $request['systemDestination'];
                        $tran->uuid = uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid() . "-" . uniqid();
                        $tran->estado = "RPA_CSV_FAIL";
                        $tran->save();
                    }
                } else {
                    $request['mul'] = 'no';
                    $request['orq'] = 'si';
                    if ($valor == 1) {
                        $data->transportedFile($request);
                    } else {
                        $data->callRpa($request);
                    }
                }
            }
        } catch (\Exception $e) {
            
        } finally {
            return redirect("/admin/orquestacion");
        }
    }

}
