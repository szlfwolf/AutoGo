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
			$catid = I("catid");
			$data = getCatAttr($catid);

			$urls = explode("\r\n", I('goodsurls'));
			

			
			session('[destroy]');	
			
			$inparas = I('post.');
			


			
			//$data = getCat(0);
			//var_dump($data);
			//$goodsList = getProductList();
			//trace($goodsList,"goodslist");
			//$goods = getProduct("529010449551");
			//trace($goods,"goods");
			
			foreach($urls as $url){
				
				if(preg_match("/GID=(?P<gid>\d+)/i",$url,$match) ==1){
					$pid = $match[1];
				}else{
					$pid = $url;
				}
				
				if ( !is_numeric($pid)){
					$this->error("抓取商品地址错误！");
				}
				
				$data = _init_cat($data,$pid);
				
				if(!is_array($data)){

					$this->error($data);
				}
				
				//$data = addProduct($pid);
				//trace($data,"add goods[".$pid."]");	
			}
					
			//$data = getGroupList();
			//trace($data,"groupList");
			
			//$data = addGroup("testgroup");
			//trace($data,"group");
			//do_sync($urls);
			$this->assign("gid",$pid);
			$this->assign("goodsurl",$urls[0]);
			
			$this->assign("subject",$data["subject"]);
			$this->assign("description",$data["description"]);
			$this->assign("goodsprice",$data["goodsprice"]);
			$this->assign("goodsimgs",$data["goodsimgs"]);
			
			unset($data["subject"]);
			unset($data["description"]);
			unset($data["goodsimgs"]);
			
			trace($data["goodsimg"],"catattr");
			
			$this->assign("catattr",$data);
					
		}else{
			
			
		}

				
		$this->display("index");
		
    }	  
    
    public function getProduct(){
    	
    	$gid = I("get.gid");
    	if(empty($gid)){
    		$gid = 529010449551;
    	}
				
		
    	$data = getProduct($gid);
    	trace($data,"goodsinfo");
    	
    	
    	$this->display("index");
    }
    
    //添加商品
    public function add(){
    	if ( IS_POST){
	    	$inparas = I('post.');    	
			
			trace($inparas["goodsimgs"],"postData");
			
	    	$data = addProduct($inparas);
	    	trace($data,"addProduct");
	    	
	    	if( array_key_exists ("errorCode",$data)){
	    		
	    		var_dump($data);
	    	}
	    	if( array_key_exists ("productID",$data)){
	    	
	    		$this->success("添加商品成功,即将跳转1688商品页! ","https://detail.1688.com/offer/".$data["productID"].".html");
	    		
	    	}
	    	else {
	    		
	    		//$this->error("添加商品失败：".$pid);
	    	}
	    		
	    		    	
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
	//批量提交商品页面
	public function batch(){
		if(IS_POST){
			$urls = explode("\r\n", I('goodsurls'));
			$gids = array();
			foreach($urls as $url){				
				if(preg_match("/GID=(?P<gid>\d+)/i",$url,$match) ==1){
					$gids[] = $match[1];					
				}
			}
			if(count($gids) > 0){
				$gidstr =implode(",", $gids); 
				//trace($gidstr,"gidstr");
				$goodsinfo = M("SGoodsinfo");
				$goodslist = $goodsinfo->field("goodsid,goodsname,goodsprice,goodsimgs,props")->where("goodsid in ($gidstr)")->select();
				foreach($goodslist as &$goods){
					$imgs = json_decode($goods["goodsimgs"]);
					$goods["goodsimg"] = str_replace("50x50","50x50",$imgs[0]);
					$props = json_decode($goods["props"],true);
					$goods["size"] = $props["尺码"];
					$goods["color"] = $props["颜色分类"];
				}
				trace($goodslist,"goodslist");
				$this->assign("goodsurls",I('goodsurls'));
				$this->assign("goodslist",$goodslist);
			}
		}else{
			$this->assign("goodsurls","http://gz.17zwd.com/item.htm?GID=5124002&spm=s4xdQ.42.57236.13683.5124002.7577");
		}
		
		$this->display("batch");
	}
	public function addbatch(){
		
		trace(I("post."),"postdata");
		//$this->success("批量发布商品成功");
		$this->display("batch");
	}
	
	

		
}
