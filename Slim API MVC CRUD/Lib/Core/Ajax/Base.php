<?php

namespace Lib\Core\Ajax;

class Base {
	public static function display() {
		header('Content-Type: application/json');
		$j = json_encode(\Core\Output::i()->getBody()->content);
		if (!$j) echo '{}';
		else echo $j;
	
	}
}