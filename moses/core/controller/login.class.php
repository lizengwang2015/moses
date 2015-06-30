<?php
/**
 *  
 * 登录控制器基类
 * 
 * 增加了登录判断
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

abstract class Moses_Core_Controller_Login extends Moses_Core_Controller{
	
	/**
	 * 初始化登录判断
	 * 
	 * @return void
	 * 
	 */
	public function __construct(){
	
		parent::__construct();
	
		$isLogin = $this->isLogin();
		if($isLogin !== true){
			$this->redirect($isLogin);
		}
	
	}
	
	/**
	 * 判断用户是否登录
	 *
	 * @return mixed  boolean:strict true
	 *                string:login url 
	 *
	 */
	abstract protected function isLogin();
	
	
}
