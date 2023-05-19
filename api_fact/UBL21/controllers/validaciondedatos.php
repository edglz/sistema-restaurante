<?php
error_reporting(E_ALL ^ E_NOTICE);
class Validaciondedatos {

	public function check_is_ajax() {
		return  isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';	
	}
	

	public function validar_data_comprobante($data) {
	
		return $data;
	}

	public function replace_invalid_caracters($cadena) {
	    $cadena = str_replace("'", "", $cadena);
	    $cadena = str_replace("#", "", $cadena);
	    $cadena = str_replace("$", "", $cadena);
	    $cadena = str_replace("%", "", $cadena);
	    $cadena = str_replace("&", "", $cadena);
	    $cadena = str_replace("'", "", $cadena);
	    $cadena = str_replace("(", "", $cadena);
	    $cadena = str_replace(")", "", $cadena);
	    $cadena = str_replace("*", "", $cadena);
	    $cadena = str_replace("+", "", $cadena);
	    $cadena = str_replace("-", "", $cadena);
	    $cadena = str_replace(".", "", $cadena);
	    $cadena = str_replace("/", "", $cadena);
	    $cadena = str_replace("<", "", $cadena);
	    $cadena = str_replace("=", "", $cadena);
	    $cadena = str_replace(">", "", $cadena);
	    $cadena = str_replace("?", "", $cadena);
	    $cadena = str_replace("@", "", $cadena);
	    $cadena = str_replace("[", "", $cadena);
	    $cadena = str_replace("\\", "", $cadena);
	    $cadena = str_replace("]", "", $cadena);
	    $cadena = str_replace("^", "", $cadena);
	    $cadena = str_replace("_", "", $cadena);
	    $cadena = str_replace("`", "", $cadena);
	    $cadena = str_replace("{", "", $cadena);
	    $cadena = str_replace("|", "", $cadena);
	    $cadena = str_replace("}", "", $cadena);
	    $cadena = str_replace("~", "", $cadena);
	    $cadena = str_replace("¡", "", $cadena);
	    $cadena = str_replace("¢", "", $cadena);
	    $cadena = str_replace("£", "", $cadena);
	    $cadena = str_replace("¤", "", $cadena);
	    $cadena = str_replace("¥", "", $cadena);
	    $cadena = str_replace("¦", "", $cadena);
	    $cadena = str_replace("§", "", $cadena);
	    $cadena = str_replace("¨", "", $cadena);
	    $cadena = str_replace("©", "", $cadena);
	    $cadena = str_replace("ª", "", $cadena);
	    $cadena = str_replace("«", "", $cadena);
	    $cadena = str_replace("¬", "", $cadena);
	    $cadena = str_replace("®", "", $cadena);
	    $cadena = str_replace("°", "", $cadena);
	    $cadena = str_replace("±", "", $cadena);
	    $cadena = str_replace("²", "", $cadena);
	    $cadena = str_replace("³", "", $cadena);
	    $cadena = str_replace("´", "", $cadena);
	    $cadena = str_replace("µ", "", $cadena);
	    $cadena = str_replace("¶", "", $cadena);
	    $cadena = str_replace("·", "", $cadena);
	    $cadena = str_replace("¸", "", $cadena);
	    $cadena = str_replace("¹", "", $cadena);
	    $cadena = str_replace("º", "", $cadena);
	    $cadena = str_replace("»", "", $cadena);
	    $cadena = str_replace("¼", "", $cadena);
	    $cadena = str_replace("½", "", $cadena);
	    $cadena = str_replace("¾", "", $cadena);
	    $cadena = str_replace("¿", "", $cadena);
	    $cadena = str_replace("À", "A", $cadena);
	    $cadena = str_replace("Á", "A", $cadena);
	    $cadena = str_replace("Â", "A", $cadena);
	    $cadena = str_replace("Ã", "A", $cadena);
	    $cadena = str_replace("Ä", "A", $cadena);
	    $cadena = str_replace("Å", "A", $cadena);
	    $cadena = str_replace("Æ", "", $cadena);
	    $cadena = str_replace("Ç", "", $cadena);
	    $cadena = str_replace("È", "E", $cadena);
	    $cadena = str_replace("É", "E", $cadena);
	    $cadena = str_replace("Ê", "E", $cadena);
	    $cadena = str_replace("Ë", "E", $cadena);
	    $cadena = str_replace("Ì", "I", $cadena);
	    $cadena = str_replace("Í", "I", $cadena);
	    $cadena = str_replace("Î", "I", $cadena);
	    $cadena = str_replace("Ï", "I", $cadena);
	    $cadena = str_replace("Ð", "", $cadena);
	    $cadena = str_replace("Ñ", "N", $cadena);
	    $cadena = str_replace("Ò", "O", $cadena);
	    $cadena = str_replace("Ó", "O", $cadena);
	    $cadena = str_replace("Ô", "O", $cadena);
	    $cadena = str_replace("Õ", "O", $cadena);
	    $cadena = str_replace("Ö", "O", $cadena);
	    $cadena = str_replace("×", "", $cadena);
	    $cadena = str_replace("Ø", "", $cadena);
	    $cadena = str_replace("Ù", "U", $cadena);
	    $cadena = str_replace("Ú", "U", $cadena);
	    $cadena = str_replace("Û", "U", $cadena);
	    $cadena = str_replace("Ü", "U", $cadena);
	    $cadena = str_replace("Ý", "Y", $cadena);
	    $cadena = str_replace("Þ", "", $cadena);
	    $cadena = str_replace("ß", "", $cadena);
	    $cadena = str_replace("à", "a", $cadena);
	    $cadena = str_replace("á", "a", $cadena);
	    $cadena = str_replace("â", "a", $cadena);
	    $cadena = str_replace("ã", "a", $cadena);
	    $cadena = str_replace("ä", "a", $cadena);
	    $cadena = str_replace("å", "a", $cadena);
	    $cadena = str_replace("æ", "", $cadena);
	    $cadena = str_replace("ç", "", $cadena);
	    $cadena = str_replace("è", "e", $cadena);
	    $cadena = str_replace("é", "e", $cadena);
	    $cadena = str_replace("ê", "e", $cadena);
	    $cadena = str_replace("ë", "e", $cadena);
	    $cadena = str_replace("ì", "i", $cadena);
	    $cadena = str_replace("í", "i", $cadena);
	    $cadena = str_replace("î", "i", $cadena);
	    $cadena = str_replace("ï", "i", $cadena);
	    $cadena = str_replace("ð", "o", $cadena);
	    $cadena = str_replace("ñ", "n", $cadena);
	    $cadena = str_replace("ò", "o", $cadena);
	    $cadena = str_replace("ó", "o", $cadena);
	    $cadena = str_replace("ô", "o", $cadena);
	    $cadena = str_replace("õ", "o", $cadena);
	    $cadena = str_replace("ö", "o", $cadena);
	    $cadena = str_replace("÷", "", $cadena);
	    $cadena = str_replace("ø", "", $cadena);
	    $cadena = str_replace("ù", "u", $cadena);
	    $cadena = str_replace("ú", "u", $cadena);
	    $cadena = str_replace("û", "u", $cadena);
	    $cadena = str_replace("ü", "u", $cadena);
	    $cadena = str_replace("ý", "y", $cadena);
	    $cadena = str_replace("þ", "", $cadena);
	    $cadena = str_replace("ÿ", "", $cadena);
	    $cadena = str_replace("Œ", "", $cadena);
	    $cadena = str_replace("œ", "", $cadena);
	    $cadena = str_replace("Š", "", $cadena);
	    $cadena = str_replace("š", "", $cadena);
	    $cadena = str_replace("Ÿ", "", $cadena);
	    $cadena = str_replace("ƒ", "", $cadena);
	    $cadena = str_replace("–", "", $cadena);
	    $cadena = str_replace("—", "", $cadena);
	    $cadena = str_replace("‘", "", $cadena);
	    $cadena = str_replace("’", "", $cadena);
	    $cadena = str_replace("‚", "", $cadena);
	    $cadena = str_replace("“", "", $cadena);
	    $cadena = str_replace("”", "", $cadena);
	    $cadena = str_replace("„", "", $cadena);
	    $cadena = str_replace("†", "", $cadena);
	    $cadena = str_replace("‡", "", $cadena);
	    $cadena = str_replace("•", "", $cadena);
	    $cadena = str_replace("…", "", $cadena);
	    $cadena = str_replace("‰", "", $cadena);
	    $cadena = str_replace("€", "", $cadena);
	    $cadena = str_replace("™", "", $cadena);

	    return $cadena;
	}
}
?>