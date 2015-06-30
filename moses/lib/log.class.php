<?php
/**
 * 
 * 日志工具类
 * 
 * Moses_Lib_Log::E('错误');
 * Moses_Lib_Log::I('信息');
 * Moses_Lib_Log::D('调试');
 * 
 * @author lizengwang <lizengwang@gmail.com>
 * @version 1.0.3
 */
 
class Moses_Lib_Log{

	//日志级别 从上到下，由低到
    const ERROR     = 'ERROR';  		// 一般错误: 一般性错误
    const INFO      = 'INFO';  		    // 信息: 程序输出信息
    const DEBUG     = 'DEBUG';  		// 调试: 调试信息  

    public static function E($vMessage){
        self::write($vMessage,self::ERROR);
    }

    public static function I($vMessage){
        self::write($vMessage,self::INFO);
    }

    public static function D($vMessage){
        self::write($vMessage,self::DEBUG);
    }

    /**
     * 写入日志，默认DEBUG
     * @param string $vMessage
     * @param int $vLevel 日志级别
     */
    private static function write($vMessage,$vLevel = self::DEBUG){

        $now = date('Y-m-d H:i:s');
        $dir = M_LOG_PATH.M_DS;
        !is_dir($dir) && @mkdir($dir,0777);
        
    	$destination = $dir.date('Y_m_d').'.log';

        $logStr = "[{$vLevel}] {$now} ".$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REQUEST_URI']." {$vMessage}".PHP_EOL;
        
        //记录日志
        error_log($logStr,3,$destination);
    }

}
