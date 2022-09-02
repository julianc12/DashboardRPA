<?php

namespace App\Admin\Controllers;

use App\Models\OrqRpa;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use DB;

class OrqRpaController extends Controller {

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
                foreach (DB::table('orq_rpa')->select('idrpa')->where('id', '=', $id)->get() as $data) {
                    $exist[] = $data->idrpa;
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
        $grid = new Grid(new OrqRpa);
        $grid->id('ID');
        $grid->column('Rpa')->display(function () {
            foreach (DB::table('rpa')->select('name')->where('id', '=', $this->idrpa)->get() as $data) {
                $categories = $data->name;
            }
            return ucwords($categories);
        });
        $grid->header(function () {
            foreach (DB::table('url')->select('link')->where('name', '=', 'orqrpa_show')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            return view('button.data', $data);
        });
        $grid->column('Orquestacion')->display(function () {
            foreach (DB::table('rpa_orquestacion')->select('name')->where('id', '=', $this->idorquestacion)->get() as $data) {
                $categories = $data->name;
            }
            return ucwords($categories);
        });
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
        $show = new Show(OrqRpa::findOrFail($id));

        $show->id('ID');
        $show->idrpa("Rpa")->as(function () {
            foreach (DB::table('rpa')->select('name')->where('id', '=', $this->idrpa)->get() as $data) {
                $categories = $data->name;
            }
            return ucwords($categories);
        });


        $show->idorquestacion("Orquestacion")->as(function () {
            foreach (DB::table('rpa_orquestacion')->select('name')->where('id', '=', $this->idorquestacion)->get() as $data) {
                $categories = $data->name;
            }
            return ucwords($categories);
        });

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
        $form = new Form(new OrqRpa);
        foreach (DB::table('rpa_orquestacion')->select('id', 'name')->get() as $da) {
            $selecto[$da->id] = $da->name;
        }
        foreach (DB::table('rpa')->select('id', 'name')->get() as $da) {
            $selectr[$da->id] = $da->name;
        }
        $form->select('idorquestacion', 'Orquestacion')->options($selecto)->rules('required');
        $form->select('idrpa', 'Rpa')->options($selectr)->rules('required');
        $form->tools(function (Form\Tools $tools) {
            $tools->disableList();
            foreach (DB::table('url')->select('link')->where('name', '=', 'orqrpa_create')->get() as $uri) {
                $uril = $uri->link;
            }
            $data['link'] = $uril;
            $tools->add(view('button.data', $data));
        });
        return $form;
    }

}
