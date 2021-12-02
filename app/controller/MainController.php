<?php

namespace app\controller;


class MainController extends AppController
{
    public function actionIndex()
    {
        $welcome = 'Welcome to MyFramework!';
        return $this->render(['welcome' => $welcome]);
    }
    public function actionAbout()
    {
        $about = "So far, nothing is known about us, but we will fix it soon!";
        return $this->render(['about' => $about]);
    }


}