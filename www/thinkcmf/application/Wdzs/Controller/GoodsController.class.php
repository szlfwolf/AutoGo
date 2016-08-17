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
			
			session('[destroy]');
			
			_init_apiinfo();
			
			
			
			//$data = getCat(0);
			//var_dump($data);
			//$goodsList = getProductList();
			//trace($goodsList,"goodslist");
			//$goods = getProduct("529010449551");
			//trace($goods,"goods");
			$data = getCatAttr();
			trace($data,"getCatAttr");
			$data = addProduct();
			trace($data,"add goods");
			//$data = getGroupList();
			//trace($data,"groupList");
			
			//$data = addGroup("testgroup");
			//trace($data,"group");
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
