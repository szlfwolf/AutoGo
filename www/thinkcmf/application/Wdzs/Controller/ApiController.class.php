<?php
namespace Wdzs\Controller;
use Common\Controller\HomebaseController;
class ApiController extends HomebaseController {
    	
    public function Index(){
    	$api = Array(
    		"url" => U("api/apitest"),
    		"fn" => Array(
    			"getCategory" => "获取类目信息",
    			"getProductList"=> "获取产品列表",
    			"getProduct"=> "获取产品信息",
    			"addGroup"=> "添加产品分组",
			),
		);
		$this->assign("api",$api);
        $this->display();
    }
	
	public function addgroup(){
		$groupname=I("groupname");
		$data =addGroup($groupname);					
		$this->ajaxReturn($data);
	}
	
	
	public function apitest(){
		$fn=I("fn");
		switch($fn){
			case "getCategory":
				$catid=I("catid");						
				$data =getCat($catid) ;
				break;
			case "addGroup":
				$groupname=I("groupname");
				$data =addGroup($groupname);
				break;
			case "getProductList":								
				$data =getProductList() ;
				break;
			case "getProduct":
				$pid=I("productid");						
				$data =getProduct($pid) ;
				break;												
		}
		$this->ajaxReturn($data);
	}
}