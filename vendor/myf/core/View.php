<?php
namespace myf\core;


class View
{

    public function addView($viewContent, $route)
    {
        $viewPath = $_SERVER['DOCUMENT_ROOT'] . '/app/view/' . $route . '.php';
        if (file_exists($viewPath)){
            ob_start();
            extract($viewContent);
            require $viewPath;
            return ob_get_clean();
        }
        echo 'View does not exist';
    }

    public function addLayout($contentFromView, $layoutContent, $layout, $title)
    {
        $layPath = $_SERVER['DOCUMENT_ROOT'] . '/app/view/layout/' . $layout . '.php';
        if (file_exists($layPath)){
            ob_start();
            require $layPath;
            return ob_get_clean();
        }else{
            echo 'Layout does not exist';
        }
    }

}