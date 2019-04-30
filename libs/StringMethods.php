<?php
namespace pietras;
class StringMethods {
	/**
	* Zamienia polskie znaki diakrytyczne na łacińskie. 
	*/
	public static function plToLatin($string) {
		$string = preg_replace('/Ę/', 'E', $string);
		$string = preg_replace('/ę/', 'e', $string);
		$string = preg_replace('/Ó/', 'O', $string);
		$string = preg_replace('/ó/', 'o', $string);
		$string = preg_replace('/Ą/', 'A', $string);
		$string = preg_replace('/ą/', 'a', $string);
		$string = preg_replace('/Ś/', 'S', $string);
		$string = preg_replace('/ś/', 's', $string);
		$string = preg_replace('/Ł/', 'L', $string);
		$string = preg_replace('/ł/', 'l', $string);
		$string = preg_replace('/Ż/', 'Z', $string);
		$string = preg_replace('/ż/', 'z', $string);
		$string = preg_replace('/Ź/', 'Z', $string);
		$string = preg_replace('/ź/', 'z', $string);
		$string = preg_replace('/Ć/', 'C', $string);
		$string = preg_replace('/ć/', 'c', $string);
		$string = preg_replace('/Ń/', 'N', $string);
		$string = preg_replace('/ń/', 'n', $string);

		return $string;
	}

	/**
	* Zamienia polskie znaki diakrytyczne na łacińskie.
	* Dodatkowo zamienia wszystkie litery na smallcase.
	*/
	public static function smallPlToLatin($string) {
		$string = self::plToLatin($string);
		$string = strtolower($string);
		return $string;		
	}

	/**
	* Zamienia odpowiednie znaki na encje HTML.
	* Także cudzysłowy.
	*/
	public static function safeString($string) {
		return htmlentities($string, ENT_QUOTES | ENT_HTML401);
	}
}
