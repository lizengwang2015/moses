<?php
/**
* 
* 核心应用启动文件
* 
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

class Moses_Core_App{

	/**
	 * 项目开始
	 */
	public static function start(){
		
		// 定义当前请求的系统常量
		define('M_IS_GET',$_SERVER['REQUEST_METHOD'] == 'GET' ? true : false); 	
		define('M_IS_POST',$_SERVER['REQUEST_METHOD'] == 'POST' ? true : false);
		define('M_IS_AJAX',((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false); 		
				
		Moses_Lib_Hook::exec('before_app_filter');
		
		//全局变量过滤
		self::filter();

		Moses_Lib_Hook::exec('before_app_dispatch');
		
		//路径分发
		self::dispatch();	
		
		Moses_Lib_Util::runtime('controller_start');
		
		Moses_Lib_Hook::exec('before_app_exec');
		
		//方法执行
		self::exec();
		
	}
	
	/**
	 * 全局变量过滤
	 * @return void
	 */
	public static function filter(){
		
		if (is_array($_SERVER)) {
			foreach ($_SERVER as $k => $v) {
				if (isset($_SERVER[$k])) {
					$_SERVER[$k] = str_replace(array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e'), '', $v);
				}
			}
		}
		
		unset($_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);
		
		$needFilterGlobals = array(&$_GET,&$_POST,&$_COOKIE,&$_FILES,&$_REQUEST);		
		foreach($needFilterGlobals as &$node){			
			$node =	Moses_Lib_Filter::filter('html', $node);
			$node =	Moses_Lib_Filter::filter('slashes', $node);
		}				
	}
	
	/**
	 * 路径分发
	 * @return void
	 */
	public static function dispatch(){
						
		//路径不为空
		if(array_key_exists('PATH_INFO', $_SERVER)){
			
			$pathStr = strtolower(trim($_SERVER['PATH_INFO'],'/'));							
			if($pathStr != ''){
				
				//获取URL后缀
				define('M_URL_EXT', strtolower(pathinfo($pathStr,PATHINFO_EXTENSION)));
					
				//解析PATH
				$pathStr = str_replace('.'.M_URL_EXT, '', $pathStr);				
				$path = explode('/',$pathStr);
				
			}else{
				
				$path = array();
				
			}
			
		}else{
			
			$path = array();
			
		}
		
		//寻址module、controler、action
		$moduleList = C('M_MODULE_LIST'); 
		if(count($path) == 1){
			//module或者controller
			
			define('M_CURRENT_ACTION','index');
			
			$item = array_pop($path);
								
			if(in_array(strtolower($item),$moduleList) != false){

				//module						
				define('M_CURRENT_CONTROLLER','index');
				define('M_CURRENT_MODULE',$item);
				
			}else{
				
				//controller						
				define('M_CURRENT_CONTROLLER',$item);						
			}	
			
		}else if(count($path) == 2){
			//module+controller或者controller+action
			
			$itemFirst = array_pop($path);
			$itemSecond = array_pop($path);
			
			if(in_array(strtolower($itemSecond), $moduleList) != false){
				
				//module+controller
				define('M_CURRENT_ACTION','index');
				define('M_CURRENT_CONTROLLER',$itemFirst);
				define('M_CURRENT_MODULE',$itemSecond);						
				
			}else{
			
				//controller+action
				define('M_CURRENT_ACTION',$itemFirst);
				define('M_CURRENT_CONTROLLER',$itemSecond);
			}
			
			
		}else if(count($path) == 3){
			
			//module+controller+action
			define('M_CURRENT_ACTION',array_pop($path));
			define('M_CURRENT_CONTROLLER',array_pop($path));
			define('M_CURRENT_MODULE',array_pop($path));
			
		}else{
			
			define('M_CURRENT_ACTION','index');
			define('M_CURRENT_CONTROLLER','index');
		}						
			
	}	

	/**
	 * 方法执行
	 * @return 
	 */
	public static function exec(){
						
		//获取controller类名称
		if(defined('M_CURRENT_MODULE')){
						
			$controllerFile = M_ROOT.M_DS.'controller'.M_DS.M_CURRENT_MODULE.M_DS.M_CURRENT_CONTROLLER.M_CLASS_EXT;
			
			if(file_exists($controllerFile)){
				
				$controllerClassName = 'Controller_'.ucfirst(M_CURRENT_MODULE).'_'.ucfirst(M_CURRENT_CONTROLLER);
				
			}else{
				
				E('controller not found:'.M_CURRENT_CONTROLLER);				
			}
			
		}else{
			
			$controllerClassName = 'Controller_'.ucfirst(M_CURRENT_CONTROLLER);
		}
	 	
		//执行方法
		try{

			//实例化controller类
			$controllerInstance =  new $controllerClassName();
			
			//找到执行action方法
			$actionName = M_CURRENT_ACTION.M_ACTION_EXT;	
			$action = new ReflectionMethod($controllerInstance,$actionName);
			
			//执行过滤
			if($action->isPublic() && !$action->isStatic()){
					
				Moses_Lib_Hook::exec('before_controller_exec');
				
				//执行方法
				$action->invoke($controllerInstance);
					
			}
			
		}catch(ReflectionException $re){
			
			//如果是反射问题，直接调用__call
			$action = new ReflectionMethod($controllerInstance,'__call');
			$action->invokeArgs($controllerInstance,array(M_CURRENT_ACTION,''));
			
		}catch(Exception $e){							
			
			//如果是内部异常，直接抛出			
			throw $e;
		}
		
	}
	
	
	/**
	 * 获取GET路径值
	 * @param unknown $VvarName
	 * @param unknown $vDefaultValue
	 * @return string
	 */
	private static function getVars($VvarName,$vDefaultValue){
	
		$value = (!empty($_GET[$VvarName]) ? $_GET[$VvarName] : $vDefaultValue);
		unset($_GET[$VvarName]);
	
		return strip_tags(strtolower($value));
	}
	
}
