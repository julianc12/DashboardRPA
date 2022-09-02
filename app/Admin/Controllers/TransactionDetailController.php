<?php

namespace App\Admin\Controllers;

use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TransactionDetailController extends Controller {

    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content) {
//              return $content
//                        ->header('Index')
//                        ->description('description')
//                        ->body($this->grid());
        return view("operacion.404");
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, $data, Content $content) {
            if (is_numeric($data)) {
                if(strlen($data) > 9){
                    return view('operacion.404');
                }else{
            return $content
                            ->header('Detail')
                            ->description('description')
                            ->body($this->detail($data));
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
        return view('operacion.404');
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content) {
         return view('operacion.404');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $grid = new Grid(new TransactionDetail);
        $grid->disableCreateButton();
        $grid->id('ID');
        $grid->command('comando');
        $grid->status()->display(function ($status) {
            return $status == 'ok' ? '<span class="label label-success">' . $status . '</span>' : '<span class="label label-danger">' . $status . '</span>';
        });
        $grid->updated_at('Updated at');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($data) {
        $show = new Show(TransactionDetail::findOrFail($data));

        $show->id('ID');
        $show->step('Step');
        $show->type('Tipo');
        $show->properties()->as(function () {
            $valor = str_replace("[", "", $this->properties);
            $valor1 = str_replace("]", "", $valor);
            $valor2 = str_replace("\"", "", $valor1);
            $valor3 = str_replace("\\\\", "\\", $valor2);
            $valor4 = str_replace("\/", "/", $valor3);
            return $valor4;
        });
        $show->command('Command');
        $show->status('Status');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->panel()
                ->tools(function ($tools) {
                    $tools->disableEdit();
                    $tools->disableDelete();
                });
        

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form(new TransactionDetail);

        $form->display('ID');
        $form->display('step');
        $form->display('command');
        $form->display('type');
        $form->display('properties');
        $form->display('status');
        $form->display('output');
        //$form->display('Created at');
        $form->display('Updated at');

        return $form;
    }

}
