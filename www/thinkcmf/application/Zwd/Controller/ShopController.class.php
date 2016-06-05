<?php

namespace Zwd\Controller;
use Common\Controller\HomebaseController;
class ShopController extends HomebaseController{
	protected $shopinfo_model;
	protected $goodsinfo_model;
	
    public function index(){
    	$this->shopinfo_model = M('Shopinfo');
		$this->goodsinfo_model = M('Goodsinfo');
		
		$shopid    =  I('get.sid',1); // 获取get变量
		$curpage    =  I('get.p',1); // 获取get变量
		if ($shopid == 0)
		{
			if(show404()) $this->display(":404");			
    	    return ;
		}
		
		$where = array("id"=>$shopid);
		$shopinfo = $this->shopinfo_model->where($where)->select();
		if ( empty($shopinfo))
		{
			if(show404()) $this->display(":404");
    	    return ;
		}
		
		$result = $this->goodsinfo_model->query("select count(id) total from s_shop_goods where shopid=%d",$shopid);
		$count = $result[0]['total'];
		$pagesize = 20;
		$Page       = $this->Page($count,$pagesize);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show('Admin');// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		
		$goodsimg = array();
		$goodswhere = array("shopid"=>$shopid);
		$goodslist = $this->goodsinfo_model
			->join('s_shop_goods on s_shop_goods.goodsid = s_goodsinfo.id')
			->field('s_goodsinfo.id,goodsname,goodsimgs,goodsprice')
			->where($goodswhere)
			->order('createtime')
			->page($curpage,$pagesize)
			->select();
			
		
		
		foreach($goodslist as $key=>$goods)
		{
			$goodsimg[$key] = str_replace("50x50","220x220",explode(",", $goods['goodsimgs'])[0]);
			
		}
		#dump($goodslist[0], $echo=true, $label=null, $strict=true);
		
		$this->assign("total",$total[0]['total']);
		$this->assign("pagesize",5);
		$this->assign("shopinfo",$shopinfo[0]);
		$this->assign("goodslist",$goodslist);
		$this->assign("goodsimg",$goodsimg);
		
		$catlist = array("明日热卖","今日热卖","昨日热卖");
		$this->assign("catlist",$catlist);
		
		$this->display(":shop");
		
    }
}