<?php

/**
 * 会员注册登录
 */
namespace Wdzs\Controller;
use Common\Controller\HomebaseController;
class GoodsController extends HomebaseController {
    //登录
	public function index() {
		
		$data = getCatAttr($catid);
		_init_apiinfo();
		if(IS_POST){
			$urls = explode("\r\n", I('goodsurls'));
			$catid = I("catid");
			trace($urls);
			
			session('[destroy]');	
			
			$inparas = I('post.');
			
			foreach($inparas as $k=>$v){
				echo $k."->".$v."<br/>";
			}	
			echo '$data.<br/>';
			foreach($data as $k=>$a){
				if (array_key_exists($a["attrid"],$inparas)){

					$data[$k]['attrvalues'] = $inparas[$a["attrid"]];
					
					echo $a["attrid"]."->".$data[$k]['attrvalues']."<br/>";
				}
			}	

			trace($data,"attrinfo");
			//$data = getCat(0);
			//var_dump($data);
			//$goodsList = getProductList();
			//trace($goodsList,"goodslist");
			//$goods = getProduct("529010449551");
			//trace($goods,"goods");
			
			foreach($urls as $pid){
				//$data = addProduct($pid);
				//trace($data,"add goods[".$pid."]");	
			}
					
			//$data = getGroupList();
			//trace($data,"groupList");
			
			//$data = addGroup("testgroup");
			//trace($data,"group");
			//do_sync($urls);
					
		}else{
			
			trace($data,"getCatAttr");
			foreach($data as $k=>$d){
				if ($d["attrid"] == 346){
					$data[$k]["attrvalues"] = array(
						0=> array("attrValueID"=>"中国", "name"=>"中国"));
				}	
			}
		}

		$this->assign("catattr",$data);			
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
