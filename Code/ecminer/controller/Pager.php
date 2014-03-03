<?php
class Pager {
	public $tableId;
	public $order;
	public $rowCount;
	public $offset;
	public $limit;
	public $pageCount;
	public $path;
	
	function __construct($tableId,$order,$rowCount,$offset = 0,$limit = 10, $path = "") {
		$this->tableId = $tableId;
		$this->order = $order;
		$this->rowCount = $rowCount;
		$this->offset = $offset;
		$this->limit = $limit;
		if($limit != 0) {
			$this->pageCount = intval($rowCount/$limit) == $rowCount/$limit ? $rowCount/$limit : intval($rowCount/$limit)+1;
		} else {
			$this->pageCount = 1;
		}
		
		$this->path = $path == "" ? PATH : $path;
	}
	
	public function run() {
		if($this->limit!=0) {
			$currentPage = intval($this->offset/$this->limit)+1;
		} else {
			$currentPage = 1;
		}
		$html = "";
		if($currentPage == 1) {
			$html .= " <img src='".$this->path."images/prev.gif' width='16' height='16' border='0' style='vertical-align: bottom;'/>";
		} else {
			$html .= " <a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".($currentPage-2).",".$this->limit."\",\"".$this->path."\")'><img src='".$this->path."images/prev.gif' style='vertical-align: bottom;' width='16' height='16' border='0' /></a>";
		}
		if($this->pageCount>5) {
			if($currentPage<=4) {
				for($i=1;$i<=4;$i++) {
					$html .= "<a ".($i==$currentPage ? "style='font-weight: bold;'" : "")." href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".$this->limit*($i-1).",".$this->limit."\",\"".$this->path."\")'>".$i."</a>";
				}
				if($currentPage == 4) {
					$html .= "<a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".($this->limit*4).",".$this->limit."\",\"".$this->path."\")'>5</a>";
				}
				$html .= " .. ";
				$html .= "<a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".$this->limit*($this->pageCount-1).",".$this->limit."\",\"".$this->path."\")'>".$this->pageCount."</a>";
			} elseif($currentPage < $this->pageCount-3) {
				for($i=1;$i<=5;$i++) {
					$html .= "<a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".$this->limit*($i-1).",".$this->limit."\",\"".$this->path."\")'>".$i."</a>";
				}
				$html .= " .. ";
				for($i=$currentPage-2;$i<=$currentPage+2;$i++) {
					$html .= "<a ".($i==$currentPage ? "style='font-weight: bold;'" : "")." href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".$this->limit*($i-1).",".$this->limit."\",\"".$this->path."\")'>".$i."</a>";
				}
				$html .= " .. ";
				for($i=$this->pageCount-3;$i<=$this->pageCount;$i++) {
					$html .= "<a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".$this->limit*($i-1).",".$this->limit."\",\"".$this->path."\")'>".$i."</a>";
				}
			} else {
				$html .= "<a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"0,".$this->limit."\",\"".$this->path."\")'>1</a>";
				$html .= " .. ";
				if($currentPage == 5) {
					$html .= "<a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".($this->limit*3).",".$this->limit."\",\"".$this->path."\")'>4</a>";
				}
				for($i=$this->pageCount-3;$i<=$this->pageCount;$i++) {
					$html .= "<a ".($i==$currentPage ? "style='font-weight: bold;'" : "")." href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".$this->limit*($i-1).",".$this->limit."\",\"".$this->path."\")'>".$i."</a>";
				}
			}
		} else {
			for($i=1;$i<=$this->pageCount;$i++) {
				$html .= "<a ".($i==$currentPage ? "style='font-weight: bold;'" : "")." href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".$this->limit*($i-1).",".$this->limit."\",\"".$this->path."\")'>".$i."</a>";
			}
		}
		if($currentPage == $this->pageCount) {
			$html .= " <img src='".$this->path."images/next.gif' width='16' height='16' border='0' style='vertical-align: bottom;'/>";
		} else {
			$html .= " <a href='javascript: void(0);' onclick='ajaxTable(\"".$this->tableId."\",\"".htmlspecialchars($this->order,ENT_QUOTES,"UTF-8")."\",\"".($currentPage*$this->limit).",".$this->limit."\",\"".$this->path."\")'><img src='".$this->path."images/next.gif' style='vertical-align: bottom;' width='16' height='16' border='0' /></a>";
		}
		return $html;
	}
}