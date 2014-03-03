<?php
class Menu {
	public $sourceXml;
	
	function __construct($sourceXmlPath = "") {
		$this->sourceXml = simplexml_load_file($sourceXmlPath);
		if(!$this->sourceXml) {
			ExceptionController::throwException("Controller","ERROR_MENU_XML_NOT_VALID");
		}
	}
	
	public function run() {
		$htmlout = "<ul id='topnav'>";
		foreach($this->sourceXml->menu as $menu) {
			foreach($menu->attributes() as $key=>$val) {
				$$key = strval($val);
			}
			$htmlout .= "<li><a href='".PATH.$url."'>".$name."</a>";
			$children = array();
			foreach($menu->submenu as $subMenu) {
				foreach($subMenu->attributes() as $key=>$val) {
					$$key = strval($val);
				}
				$children[] = array("submenuname"=>$submenuname,"submenuurl"=>$submenuurl);
			}
			if(count($children)>0) {
				$htmlout .= "<span><a href='".PATH.$children[0]["submenuurl"]."'>".$children[0]["submenuname"]."</a>";
				for($i=1;$i<count($children);$i++) {
					$htmlout .= " | <a href='".PATH.$children[$i]["submenuurl"]."'>".$children[$i]["submenuname"]."</a>";
				}
				$htmlout .= "</span>";
			}
			$htmlout .= "</li>";
		}
		$htmlout .= "</ul>";
		echo $htmlout;
	}
}