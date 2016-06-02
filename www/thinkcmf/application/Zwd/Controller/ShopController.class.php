<?php

namespace Zwd\Controller;
use Common\Controller\HomebaseController;
class ShopController extends HomebaseController{
	protected $shopinfo_model;
	
    public function index(){
    	$this->shopinfo_model = M('Shopinfo');
		
		$where = array("id"=>1);
		$shopinfo = $this->shopinfo_model->where($where)->select();
		
		dump($shopinfo[0], $echo=true, $label=null, $strict=true);
		
		$this->assign("shopinfo",$shopinfo[0]);
		$this->display(":shop");
		
    }
}