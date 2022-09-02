<?php

namespace App\Admin\Controllers;

use App\Models\RpaEndpoint;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use DB;

class RpaEndpointController extends Controller {

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
                foreach (DB::table('rpa_endpoint')->select('endpoint')->where('id', '=', $id)->get() as $data) {
                    $exist[] = $data->endpoint;
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
        $grid = new Grid(new RpaEndpoint);
        $grid->id('ID');
        $grid->endpoint('Endpoint');
        $grid->column('Tipo rpa')->display(function () {
            foreach (DB::table('rpa_type')->select('name')->where('id', '=', $this->tipo_rpa)->get() as $data) {
                $categories = $data->name;
            }
            return ucwords($categories);
        });

        $grid->header(function () {
            foreach (DB::table('url')->select('link')->where('name', '=', 'end_show')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            return view('button.data', $data);
        });
        $grid->description('Description');
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
        $show = new Show(RpaEndpoint::findOrFail($id));
        $show->id('ID');
        $show->tipo_rpa('Tipo rpa');
        $show->endpoint('Endpoint');
        $show->description('Description');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form(new RpaEndpoint);
        $dato = array();
        foreach (DB::table("rpa_type")->select("name", "id")->get() as $data) {

            $valordelrpa = DB::table("rpa_endpoint")->where('tipo_rpa', '=', $data->id)->get();
            if (count($valordelrpa) == 0) {
                $dato[$data->id] = $data->name;
            }
        }
        $form->select('tipo_rpa')->options($dato)->rules('required');
        $form->text('description');
        $form->url('endpoint')->rules('required');
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            foreach (DB::table('url')->select('link')->where('name', '=', 'end_create')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            $tools->add(view('button.data', $data));
        });
        return $form;
    }

    protected function formedit($id) {
        $form = new Form(new RpaEndpoint);
        $dato = array();
        $idtiporpa;
        foreach (DB::table("rpa_endpoint")->select('tipo_rpa')->where('id', '=', $id)->get() as $d) {
            $idtiporpa = $d->tipo_rpa;
        }

        foreach (DB::table("rpa_type")->select("name", "id")->get() as $data) {
            /*
              Al editar un endpoint no permite seleccionar el tipo q ya tenia

             * */
            $valordelrpa = DB::table("rpa_endpoint")->where('tipo_rpa', '=', $data->id)->get();
            if ($data->id == $idtiporpa || count($valordelrpa) == 0) {
                $dato[$data->id] = $data->name;
            }
        }
        $form->select('tipo_rpa')->options($dato)->rules('required');
        $form->text('description');
        $form->url('endpoint')->rules('required');
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            foreach (DB::table('url')->select('link')->where('name', '=', 'end_create')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            $tools->add(view('button.data', $data));
        });
        return $form;
    }

}
