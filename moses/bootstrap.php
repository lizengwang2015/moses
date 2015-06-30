<?php
/**
* 
* 入口启动文件
* 
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

//全局配置
date_default_timezone_set('PRC');
!defined('M_DEBUG') && define('M_DEBUG', false);
define('M_VERSION','1.0.3');

//定义文件相关核心常量
define('M_CLASS_EXT','.class.php');
define('M_TPL_EXT','.tpl.php');
define('M_CONFIG_EXT','.inc.php');
define('M_ACTION_EXT','Action');
define('M_DS',DIRECTORY_SEPARATOR);
define('M_PS',PATH_SEPARATOR);

//定义路径相关核心常量
define('M_ROOT',realpath(dirname(dirname(__FILE__))));
define('M_CONF_PATH',M_ROOT.M_DS.'conf');
define('M_FRAMEWORK_PATH',M_ROOT.M_DS.'moses');
define('M_LIB_PATH',M_ROOT.M_DS.'lib');
define('M_STATIC_PATH', M_ROOT.M_DS.'static');
define('M_TPL_PATH', M_STATIC_PATH.M_DS.'tpl');
define('M_TMP_PATH',M_ROOT.M_DS.'tmp');
define('M_LOG_PATH',M_TMP_PATH.M_DS.'log');


//启动引导
Moses_Bootstrap::start();



/**
 * 引导类
 * 
 * 定义全局的类寻址机制
 * 定义全局的ERROR捕捉机制
 * 定义全局的Exception捕捉机制
 * 加载核心配置
 * 
 */
class Moses_Bootstrap{

	/**
	 * 应用程序初始化
	 * @return void
	 */
	public static function start(){

		//加载常用函数
		include_once M_FRAMEWORK_PATH.M_DS.'func.php';
		
		//注册AUTOLOAD
		spl_autoload_register('Moses_Bootstrap::autoload');

		//错误处理		
		set_error_handler('Moses_Bootstrap::appError');
		set_exception_handler('Moses_Bootstrap::appException');
		
			
		Moses_Lib_Hook::exec('before_compile');
		
		//文件缓存配置信息
		self::compileConf();
		
		Moses_Lib_Hook::exec('before_app_start');
		
		//启动项目
		Moses_Core_App::start();
	
	}

	/**
	 * 类库自动加载
	 * @param  string $vClassName 类名称
	 * @return void
	 */
	public static function autoload($vClassName){
		
		//解析类名称
		$nameList = explode('_',strtolower($vClassName));
		$filePath = implode(M_DS,$nameList);
		
		//加载类l
		if(file_exists($filePath.M_CLASS_EXT)){
			require_once $filePath.M_CLASS_EXT;
		}else if(file_exists(M_LIB_PATH.M_DS.$filePath.M_CLASS_EXT)){
			require_once M_LIB_PATH.M_DS.$filePath.M_CLASS_EXT;
		}else{
			E('file not found:'.$filePath.M_CLASS_EXT);
		}
		
	}

	/**
	 * 自定义错误处理
	 * @param  int $vErrNum  错误类型
	 * @param  string $vErrStr  错误信息
	 * @param  string $vErrFile 错误文件
	 * @param  int $vErrLine 错误行数
	 * @return void
	 */
	public static function appError($vErrNum,$vErrStr,$vErrFile,$vErrLine){			
		
		$e = array();
		
		if(M_DEBUG == true){
			
			$errortype = array (
				E_ERROR              => 'E_ERROR',
				E_WARNING            => 'E_WARNING',
				E_PARSE              => 'E_PARSE',
				E_NOTICE             => 'E_NOTICE',
				E_CORE_ERROR         => 'E_CORE_ERROR',
				E_CORE_WARNING       => 'E_CORE_WARNING',
				E_COMPILE_ERROR      => 'E_COMPILE_ERROR',
				E_COMPILE_WARNING    => 'E_COMPILE_WARNING',
				E_USER_ERROR         => 'E_USER_ERROR',
				E_USER_WARNING       => 'E_USER_WARNING',
				E_USER_NOTICE        => 'E_USER_NOTICE',
				E_STRICT             => 'E_STRICT',
				E_RECOVERABLE_ERROR  => 'E_RECOVERABLE_ERROR'
			);
			
		}else{
			
			$errortype = array (
				E_ERROR    => 'E_ERROR'
			);
		}
		
		
		$e['message'] = $vErrStr;
		$e['typeStr'] = $errortype[$vErrNum];        
        $e['file']      = $vErrFile;
        $e['line']      = $vErrLine;
        ob_start();
        debug_print_backtrace();
        $e['trace']     = ob_get_clean();                			


        //调试模式下输出错误信息  
        if(M_DEBUG){      
	        
	        $displayStr = '['.$e['typeStr'].']&nbsp;';
	        $displayStr .= $e['file'].'('.$e['line'].'):';        
	        $displayStr .= $e['message'].'<BR/>';	        
	        $displayStr .= nl2br($e['trace']);

	        echo $displayStr;
		}
		
		$logStr = $e['file'].'('.$e['line'].'): ';
		$logStr .= $e['message'].PHP_EOL;
		$logStr .= $e['trace'];
		
		Moses_Lib_Log::E($logStr);
		
        exit;
	}

	/**
	 * 自定义异常处理
	 * @param Exception $vException
	 * @return void
	 */
	public static function appException($vException){
		
		$trace =  $vException->getTrace();
		if('E'==$trace[0]['function']) {
			$file  =   $trace[0]['file'];
			$line  =   $trace[0]['line'];
		}else{
			$file  =   $vException->getFile();
			$line  =   $vException->getLine();
		}

		$info  = 'Exception:';
		$info .= $file.'('.$line.'): ';				
		$info .= $vException->getMessage().PHP_EOL.'[Trace]';
		$info .= $vException->getTraceAsString().PHP_EOL;		
		
		if(M_DEBUG) echo nl2br($info);	
		Moses_Lib_Log::E($info);
		
		exit;		
	}
	
	/**
	 * 缓存配置文件
	 * @return void
	 */
	private static function compileConf(){
	
		$cacheConfigFile = M_TMP_PATH.M_DS.'~core.inc.php';
		
		if(file_exists($cacheConfigFile) && M_DEBUG == false){
			
			C(include $cacheConfigFile);
			
		}else{
			
			//遍历加载所有的核心配置文件
			Moses_Lib_Util::loadConfDir(M_FRAMEWORK_PATH.M_DS.'conf'.M_DS);
			
			//加载app配置文件，只加载.inc.php结尾的文件
			Moses_Lib_Util::loadConfDir(M_CONF_PATH.M_DS);
			
			$config = C();
			$contentStr = Moses_Lib_Util::printArray($config);
			$contentStr = '<?php return array('.$contentStr.');?>';
			
			file_put_contents($cacheConfigFile, $contentStr);
			
			
		}

	}
}

