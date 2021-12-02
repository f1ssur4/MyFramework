<?php
namespace myf\core;
use myf\core\View;

abstract class Controller
{
    public $controller;
    public $action;
    public $route;
    public $title = 'myframework';
    public $layout = 'default';
    public $view;
    public $layoutContent;

    protected function loadContentInView($controller, $action)
    {
        $this->view = new View();
        $this->route = $controller . '/' . $action;
    }


    public function render($viewContent = [])
    {
        $this->loadContentInView($this->controller, $this->action);

        return $this->view->addLayout($this->view->addView($viewContent, $this->route), $this->layoutContent, $this->layout, $this->title);
    }

}