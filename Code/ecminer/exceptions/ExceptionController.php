<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExceptionManager
 *
 * @author kotuz
 */
class ExceptionController {
    //put your code here

    public static $exceptionArray = array(
        "DB"=>"DBException",
        "Model"=>"ModelException",
    	"Runner"=>"RunnerException",
    	"View"=>"ViewException",
    	"Core"=>"CoreException",
    	"Controller"=>"ControllerException"
    );

    public static function throwException($type,$message) {
        if(!array_key_exists($type,self::$exceptionArray)) {
            throw new Exception("Exception type ".$type." is not defined!");
        }
        $exceptionName = self::$exceptionArray[$type];
        include_once($exceptionName.".php");
        throw new $exceptionName($message);
    }

}
?>
