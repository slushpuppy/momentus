<?php
namespace Lib\Core;

class Output {
	private $head,$body;
	private static $_i;
	
	public function __construct() {}
	
	public static function i() {
		if (self::$_i == NULL) {
			self::$_i = new self;
			self::$_i->head = new \stdClass;
			self::$_i->head->css = new \stdClass;
			self::$_i->head->css->files = [];
			self::$_i->head->css->raw = '';
			
			self::$_i->head->js = new \stdClass;
			self::$_i->head->js->files = [];
			self::$_i->head->js->raw = '';
			self::$_i->head->baseUrl = \Config::$base_url;
			self::$_i->head->title = \Config::$base_title;
			
			self::$_i->body  = new \stdClass;
			self::$_i->setContent('');
			//self::$_i->setUserProfile('');
			return self::$_i;
		} else return self::$_i;
	}

	public function addJsFile($url) {
		$this->head->js->files[] = $url;
	}
	public function addCssFile($url) {
		$this->head->css->files[] = $url;
	}

	public function addInlineCss($css) {
		$this->head->css->raw = $css;
	}
	public function addInlineJs($js) {
		$this->head->js->raw = $js;
	}
	public function setUserProfile($user) {
		$this->body->userProfile = $user;
	}
	public function setContent($content) {
		$this->body->content = $content;
	}
	public function setTitle($title) {
		$this->head->title = $title;
	}
	public function setAlertDanger($error) {
		$this->body->alert["danger"][] = $alert;
	}
	public function setAlertInfo($alert) {
		$this->body->alert["info"][] = $alert;
	}
	public function setAlertWarning($alert) {
		$this->body->alert["warning"][] = $alert;
	}
	public function setAlertSuccess($alert) {
		$this->body->alert["success"][] = $alert;
	}
	public function getHead() {
		return $this->head;
	}
	public function getBody() {
		return $this->body;		
	}
}