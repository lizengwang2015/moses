<?php 

/**
 * 
 * 记录框架内部钩子
 * 
 * 调试信息输出钩子
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 * 
 */


class Moses_Core_Hook_Vdebug implements Moses_Lib_Hook_Interface{
	
	/**
	 * 页面调试信息
	 * @see Moses_Lib_Hook_Interface::run()
	 */
	public function run($vView){

		//是否显示当前页面调试信息
		$debugToken = I('get._t',null);
		if(Moses_Lib_Util::debugToken($debugToken) === true){
		
			//显示页面时间信息
			$totalPageTime = Moses_Lib_Util::runtime('controller_start','view_end');
			$viewPageTime = Moses_Lib_Util::runtime('view_start','view_end');
		
			echo '<div><h6>环境信息</h6>';
			echo '<span style="font-size:weight">【totalTime】：'.$totalPageTime.'s</span><br/>';
			echo '<span style="font-size:weight">【renderTime】：'.$viewPageTime.'s</span><br/>';
			echo '<span style="font-size:weight">【serverIP】：'.Moses_Lib_Util::hideIP($_SERVER['SERVER_ADDR']).'</span><br/>';
			echo '<span style="font-size:weight">【clientIP】：'.Moses_Lib_Util::hideIP($_SERVER['REMOTE_ADDR']).'</span><br/>';
			echo '<div>';
		
			//TODO:显示数据来源信息，主要是cache信息
		
		
			//显示页面变量信息
			echo '<div><h6>页面变量</h6>';
			$vars = $vView->getVars();
			foreach($vars as $key=>$node){
				echo '<span style="font-size:weight">【'.$key.'】：</span>';
				echo print_r($node,true);
				echo '<br/>';
			}
			echo '<div>';
		
		}
		
	}
}



