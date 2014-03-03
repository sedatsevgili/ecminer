<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModelException
 *
 * @author kotuz
 */
class ModelException extends Exception{
    //put your code here

    public static $ERROR_EMPTY_ID = "Model id bilgisi boş";
    public static $ERROR_USER_NOT_LOADED = "Yönetici bilgileri yüklenemedi";
    public static $ERROR_USERNAME_EXISTS = "Lütfen farklı bir kullanıcı adı giriniz";
    public static $ERROR_EMAIL_EXISTS = "Lütfen farklı bir email adresi giriniz";
    public static $ERROR_CLASSIFIER_NAME_EXISTS = "Lütfen farklı bir sınıflandırıcı ismi giriniz";
    public static $ERROR_CLUSTERER_NAME_EXISTS = "Lütfen farklı bir kümelendirici ismi giriniz";
    public static $ERROR_IMPORT_FIELD_NAME_EXISTS = "Lütfen farklı bir veri alanı ismi giriniz";
    public static $ERROR_IMPORT_TYPE_NAME_EXISTS = "Lütfen farklı bir veri tipi ismi giriniz";
    public static $ERROR_ACCOUNT_DOESNT_EXIST = "Lütfen geçerli bir hesap seçiniz";
    public static $ERROR_SITE_ADDRESS_EXISTS = "Lütfen farklı bir site adresi giriniz";
    public static $ERROR_CLASSIFIER_FILE_NAME_EXISTS = "Lütfen farklı isimde bir sınıflandırıcı dosyası giriniz";
    public static $ERROR_FILE_NOT_UPLOADED = "Dosya yüklenemedi";
    public static $ERROR_CLUSTERER_FILE_NAME_EXISTS = "Lütfen farklı isimde bir kümelendirici dosyası giriniz";
    public static $ERROR_FILE_NOT_DELETED = "Dosya silinemedi";
    public static $ERROR_ACCOUNT_NOT_LOADED = "Kullanıcı adı veya şifre hatası";
    public static $ERROR_IMPORT_FILE_NOT_UPLOADED = "Veri dosyası yüklenemedi";
    public static $ERROR_IMPORT_FILE_NOT_COPIED = "Veri dosyası kopyalanamadı";
    public static $ERROR_IMPORT_FILE_NOT_DELETED = "Veri dosyası silinemedi";

    function __construct($message) {
        parent::__construct(self::$$message);
    }
}
?>
