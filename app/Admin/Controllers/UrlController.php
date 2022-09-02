<?php

namespace App\Admin\Controllers;

use App\Models\Url;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UrlController extends Controller {

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
    public function edit($id, Content $content) {
        return $content
                        ->header('Edit')
                        ->description('description')
                        ->body($this->formedit()->edit($id));
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
        $grid = new Grid(new Url);
        $grid->id('ID');
        $grid->name('Name');
        $grid->link('Link');
        $grid->model()->orderBy('id', 'asc');
        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id) {
        $show = new Show(Url::findOrFail($id));
        $show->id('ID');
        $show->name('Name');
        $show->link('Link');
        $show->panel()->tools(function ($tools) {
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
        $form = new Form(new Url);
        $form->text('name', 'Name')->rules('required|unique:url,name');
        $form->text('link', 'Link')->rules('required');
        return $form;
    }
    protected function formedit() {
        $form = new Form(new Url);
        $form->text('link', 'Link')->rules('required');
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }

}
