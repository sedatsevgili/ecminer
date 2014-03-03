<?php
class View {
	public $cssClass;
	public $id;
	
	function __construct($id, $cssClass) {
		$this->id = $id;
		$this->cssClass = $cssClass;
	}
}