<?php
class ViewException extends Exception {
	public static $ERROR_ADMIN_MENU_NOT_LOADED = "Yönetici menüsü yüklenemedi";
	public static $ERROR_ROW_ADDED_TO_TABLE = "Tabloya satır eklendi";
	public static $ERROR_COLUMN_IS_NOT_VALID_OBJECT = "Sütun geçerli bir nesne değil";
	public static $ERROR_THERE_IS_NO_COLUMN = "Tabloda herhangi bir sütun yok";
	public static $ERROR_ROW_IS_NOT_VALID_OBJECT = "Satır geçerli bir nesne değil";
	public static $ERROR_COLUMN_COUNT_DOESNT_MATCH = "Sütun sayıları eşleşmiyor";
	public static $ERROR_COLUMN_ARRAY_IS_NOT_VALID = "Sütun dizisi geçerli değil";
	public static $ERROR_FIELD_MASKS_DONT_MATCH = "Veri alanları uyuşmuyor";
	public static $ERROR_TABLE_COULDNT_LOAD_FROM_SESSION = "Tablo oturum bilgilerinden yüklenemedi";
}