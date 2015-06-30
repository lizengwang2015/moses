<?php

class Controller_Test1_Index extends Moses_Core_Controller_Login{

	public function indexAction(){
		$this->assign('name','hello world test1 with default login');
		$this->display();
	}
		
	protected function isLogin(){
		return true;
	}	
}
?>