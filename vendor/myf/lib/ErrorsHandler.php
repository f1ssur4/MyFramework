<?php
namespace myf\lib;
use app\controller\AppController;
use Exception;



class PageNotFoundException extends Exception {
    public function __construct($message = "", $code = 404)
    {

        parent::__construct($message, $code);
    }
}



class ErrorsHandler{


    public function __construct()
    {
        if (DEBUG == 1){
            error_reporting(E_ALL);
        }else{
            error_reporting(0);
        }
        set_error_handler([$this, 'errorsHandler']);
        ob_start();
        register_shutdown_function([$this, 'FatalErrorsHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
    }


    public function errorsHandler($errno, $errstr, $errfile, $errline)
    {
        $this->errorsLog($errstr, $errfile, $errline);
        $this->errorsDisplay($errno, $errstr, $errfile, $errline);

        return true;
    }


    public function errorsDisplay($errno, $errstr, $errfile, $errline, $response = 503)
    {
        http_response_code($response);
//        var_dump($response);die;
        if ($response == 404){
            require 'app/view/error/404.html';
            die;
        }
        if (DEBUG == 1){
            require __DIR__ . '/../../../app/view/error/dev.html';
            die;
        }else{
            require __DIR__ . 'app/view/error/prod.html';
            die;
        }
    }


    public function errorsLog($errorMessage, $errorFile, $errorLine)
    {
        error_log("(" . date('Y:m:d H:i:s') . ")" . " Error text: {$errorMessage} | In file: {$errorFile} | On line: {$errorLine} \n========================================================================================================================================\n", 3, __DIR__ . '\..\..\..\app\tmp\errLog');
    }


    public function FatalErrorsHandler()
    {
        $error = error_get_last();
        if (!empty($error) && $error['type'] & ( E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR )){
            $this->errorsLog($error['message'], $error['file'], $error['line']);
            ob_get_clean();
            $this->errorsDisplay($error['type'], $error['message'], $error['file'], $error['line']);
        }else{
            ob_get_flush();
        }
    }


    public function exceptionHandler($e)
    {
        $this->errorsLog($e->getMessage(), $e->getFile(), $e->getLine());
        $this->errorsDisplay('Exception', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
    }
}
?>