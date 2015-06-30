<?php
/**
 *  
 * 控制器基类
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */

abstract class Moses_Core_Controller{
	
	//View类
	private $_view = null;
		
	//默认返回数据
	protected $_returnArray = array('code'=>0,'data'=>null,'msg'=>'');
	
	/**
	 * 初始化view层
	 * @return void
	 */
	public function __construct(){
			
		$this->_view = new Moses_Core_View();	
		
		Moses_Lib_Hook::add('after_view_display','Moses_Core_Hook_Vdebug');		
	}
	
	/**
	 * 注册参数
	 * @param string $vKey
	 * @param mixed $vValue
	 */
	protected function assign($vKey,$vValue){
		$this->_view->assign($vKey,$vValue);
	}
	
	/**
	 * 显示模板
	 * @param string $vTpl
	 */
	protected function display($vTpl = ''){
	
		Moses_Lib_Hook::exec('before_view_display',$this->_view);		
		Moses_Lib_Util::runtime('view_start');
	
		$this->_view->display($vTpl);
	
		Moses_Lib_Util::runtime('view_end');		
		Moses_Lib_Hook::exec('after_view_display',$this->_view);
	
	}
	
	/**
	 * 设置ajaxReturn返回
	 * @return void
	 */
	protected function returnJson(){
	
		$this->_returnArray['code'] = intval($this->_returnArray['code']);
		echo json_encode($this->_returnArray);
		exit;
	}
	
	/**
	 * 基于两套
	 * @param unknown $vBaseInfo
	 * @param unknown $vParams
	 * @return string
	 */
	protected function buildUrl($vBaseInfo,$vParams){
		return '';	
	}
	
	/**
	 * 页面跳转
	 * @param string $vUrl
	 * @param number $vTime
	 */
	protected function redirect($vUrl,$vTime = 0){
		if(!headers_sent()){
			$vTime === 0 ? header("Location: ".$vUrl) : header("refresh:" . $vTime . ";url=" .$vUrl. "");
		}else{
			exit("<meta http-equiv='Refresh' content='" . $vTime . ";URL=" .$vUrl. "'>");
		}
	}
	
	/**
	 * 空方法捕捉
	 * @return void
	 */
	public function __call($vMethod,$vArgs){
		
		if(method_exists($this,'_404')) {
	
			// 如果定义了_empty操作 则调用
			$this->_404($vMethod,$vArgs);
	
		}if(file_exists($this->_view->parseTpl())){
	
			//检查是否存在默认模版 如果有直接输出模版
			$this->display();
	
		}else{
	
			E('action not found:'.$vMethod);
		}
	
		exit;
	}
	
	/**
	 * 基本404页面
	 *
	 */
	public function _404($vMethod,$vArgs){
	
		exit('404');
	}
	
	
}
