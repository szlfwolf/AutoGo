<?php

namespace Zwd\Controller;
use Common\Controller\HomebaseController;
class MarketController extends HomebaseController{
	protected $city_model;
	protected $citymarket_model;
	
    public function index(){
    	$this->city_model = M('City');
		$this->citymarket_model = M('Citymarket');
		
		$cityid    =  I('get.cityid',1); // 获取get变量		
		    				
		$cityArray = $this->city_model ->select();
		foreach ($cityArray as $key => $value) 
		{
			if( $value['id'] == $cityid)
				$cityinfo = $value;
		} 
		
		$wheremarket = array("cityname"=>$cityinfo['cityname']);	    	
    	$citymarketArray = $this->citymarket_model 
    		->where($wheremarket)
    		->limit(30)
    		->select();
		
		$this->assign("CityList",$cityArray);
		$this->assign("CityInfo",$cityinfo);
		$this->assign("CityMarketList",$citymarketArray);
		
		$this->display(":market");
    }
}