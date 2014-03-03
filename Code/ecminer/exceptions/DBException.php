<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBException
 *
 * @author kotuz
 */
class DBException extends Exception {
    //put your code here

    public static $ERROR_IN_QUERY = "Sorgu hatası";


    function __construct($message) {
        parent::__construct(self::$$message);
    }

}
?>
