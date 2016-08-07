<?php

/**
 * 会员注册登录
 */
namespace Wdzs\Controller;
use Common\Controller\HomebaseController;
class InstallController extends HomebaseController {
	
	function _initialize(){
        if(file_exists_case("./data/install.lock")){        	
            redirect( U("Wdzs/Index/Index"));
        }
    }
		
    //登录
	public function index() {
		//sp_execute_sql($db, "thinkcmf.sql", $table_prefix);							
		
    }
	 
		
}
