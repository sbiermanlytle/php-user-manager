<?php
class Msg{
	private $msg;
	private $type;
	function Msg($msg,$type=0){
		$this->msg = $msg;
		$this->type = $type;
	}
	function getMsg(){
		switch ($this->type) {
			case 1: return '<p class="error">'.$this->msg.'</p>';
			case 2: return '<p class="success">'.$this->msg.'</p>';
			default: return '<p>'.$this->msg.'</p>';
		}
	}
}
?>