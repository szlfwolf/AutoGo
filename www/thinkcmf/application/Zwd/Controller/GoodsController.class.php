<?php

namespace Zwd\Controller;
use Common\Controller\HomebaseController;
class GoodsController extends HomebaseController{
	
	protected $shopinfo_model;
	protected $goodsinfo_model;
	
	
    public function index(){
		$this->shopinfo_model = M('Shopinfo');
		$this->goodsinfo_model = M('Goodsinfo');
		
		$goodsid    =  I('get.gid',1); // 获取get变量
		$where = array("id"=>$goodsid);
		$goodsinfoArray = $this->goodsinfo_model->where($where)->select();
		$goodsinfo = $goodsinfoArray[0];
		$goodsimgs = json_decode($goodsinfo['goodsimgs']);
		#$goodsimgs = explode(",", $goodsinfo['goodsimgs']);
		$goodsimgs_m = str_replace("50x50","400x400",$goodsimgs[0]);
		    	
		#dump($goodsimgs);
		$this->assign("goodsinfo",$goodsinfo);
		$this->assign("goodsimgs",$goodsimgs);
		$this->assign("goodsimgs_m",$goodsimgs_m);
	  	$this->display(":goods");
    }
}