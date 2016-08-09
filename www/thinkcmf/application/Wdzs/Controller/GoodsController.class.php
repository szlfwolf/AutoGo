<?php

/**
 * 会员注册登录
 */
namespace Wdzs\Controller;
use Common\Controller\HomebaseController;
class GoodsController extends HomebaseController {
    //登录
	public function index() {
		if(IS_POST){
			$urls = explode("\r\n", I('goodsurls'));
			
			_init_apiinfo();
			
			//getCat(0);
			getProductList();
			//do_sync($urls);
					
		}
		

			
		$this->display("index");
		
    }	  

	
	public function category()
	{
		$api = C("API_1688.BASEURL") ."alibaba.category.get/".C("API_1688.APP_KEY");
		$postdata = array(
			"categoryID"=>0,
			"webSite"=>"1688",
		);
		var_dump($postdata);
		$json = send_post($api,$postdata);			
		$arr =json_decode($json,true);
		$catinfo = $arr["categoryInfo"][0];
		var_dump($catinfo);
		
		$catInfo = getCat(122196005);
		var_dump($catInfo);
		$this->display(":index");		
	}
	

		
}
