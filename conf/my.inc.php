<?php
/**
 *
* 实际应用配置文件
*
* @author lizengwang <lizengwang@gmail.com>
* @version 1.0.3
*/

return array(

		//模块列表
		'M_MODULE_LIST'	     => array('test1'),	//只有存在MODULE的时候才需要配置
		
		'M_CACHE' => array(
				'PREFIX'		=>'',					//moses_
				'TYPE'			=>'memcache',					//file,memcache
				'OPTIONS'		=>array(
					'servers'=>array(
						array(
							'host'=>'127.0.0.1',
							'port'=>11211
						),
					),
				)	       
		)

);
