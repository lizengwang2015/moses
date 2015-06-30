<?php

class Controller_Index extends Moses_Core_Controller{
	
	public function indexAction(){
		
		//$m = new Moses_Core_Cache_Memcache(array('servers'=>array(array('host'=>'127.0.0.1','port'=>11211))));
		//$m->set('name', 'lizengwang');
		
		//$m = new Moses_Core_Cache_Memcached(array('servers'=>array(array('host'=>'127.0.0.1','port'=>11211))));
		//$m->set('name', 'lizengwang');
		
		//$m = new Moses_Core_Cache(C('M_CACHE'));
		//$m->set('name','zhangsan');
		
		//$name = $m->get('name');
		$name = 'bbbbbbbb';
		
		$this->assign('name',$name);
		$this->display();
	}
	
}

?>