<?php 
/**
 *
 * 通用函数文件
 *
 * @author lizengwang<lizengwang@gmail.com>
 * @version 1.0.3
 */


/**
 * 获取和设置配置参数,支持批量定义
 * @param string|array $name   配置变量名称
 * @param string|array $value  配置值
 * @param mixed $default 默认值
 */
function C($name = null,$value = null,$default = null){

	//配置参数
	static $gConfig = array();
	
    //无参数时获取所有
    if(empty($name)) return $gConfig;

    //优先执行设置获取或复制
    if(is_string($name)){
    	
    	$name = strtoupper($name);
    	
    	if(is_null($value)){
    		return isset($gConfig[$name]) ? $gConfig[$name] : $default;
    	}else{       		
    		
    		if(isset($gConfig[$name]) && is_array($value) && is_array($gConfig[$name])){    			
    			$gConfig[$name] = array_merge($gConfig[$name],$value);
    		}else{
    			
    			$gConfig[$name] = $value;
    		}    	
    	}
    	
    }else if(is_array($name)){
    	$gConfig = array_merge_recursive($gConfig,array_change_key_case($name,CASE_UPPER));
    }

    return;
}


/**
 * 抛出异常处理
 * @param string  $msg  异常消息
 * @param integer $code 异常代码，默认0
 */
function E($msg,$code = 0){	
	throw new Exception($msg,$code);
}


/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */

function I($name,$default = '',$filter = null){
	    
    if(strpos($name,'.')){ 
    	// 指定参数来源
        list($method,$name) =   explode('.',$name,2);
    }else{ 
    	// 默认为自动判断
        $method =   'param';
    }

    switch(strtolower($method)){
        case 'get':
        	$input = &$_GET;
        	break;
        case 'post':
        	$input = &$_POST;
        	break;
        case 'put':
        	parse_str(file_get_contents('php://input'), $input);
        	break;
        case 'param':
            switch($_SERVER['REQUEST_METHOD']){
                case 'POST':
                    $input  =  $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input  =  $_GET;
            }
            break;
        case 'request' :   
        	$input = &$_REQUEST;   
        	break;
        case 'session':
        	$input = &$_SESSION;   
        	break;
        case 'cookie':
        	$input = &$_COOKIE;    
        	break;
        case 'server':
           	$input = &$_SERVER;    
           	break;
        case 'globals':
        	$input = &$GLOBALS;    
        	break;
        default:
            return NULL;
    }
	    
    if($name == ''){
    	$data = $input;
    }else if(isset($input[$name])){
    	$data = $input[$name];
    }
	    
	//过滤
	if(isset($data)){
		
		if(is_null($filter) == false){
			            
            $filters = explode(',',$filter);                        
            foreach($filters as $filter){   
				if(Moses_Lib_Filter::isFilterTypeExists($filter) !== false){
					$data = Moses_Lib_Filter::filter($filter, $data);
				}else if(function_exists($filter)){
					$data = call_user_func($filter,$data);
				}            	         	
            	            	            		                
            }
        }
        
    }else{
    	
		$data = $default != '' ? $default : null;
		
    }        
	    
	return $data;
}

