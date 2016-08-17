<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------

//生成签名
function Signature($api,$param=array())
{
    //param为其他参数，调用不同接口，参数也不同。参数会随之加入签名计算
    
    
    $redirectUrl = C('API_1688.R_URL');
	
	$url = 'http://gw.api.alibaba.com/openapi';//1688开放平台使用gw.open.1688.com域名
    $appKey = C('API_1688.APP_KEY');
    $appSecret = C('API_1688.APP_CODE');
    $apiInfo = '';//此处请用具体api进行替换
    if(!empty($api)){
    	$apiInfo = $api . $appKey;
	}
	
    
    //生成签名    
	$aliParams = array();
    foreach ($param as $key => $val) {
        $aliParams[] = $key . $val;
    }
    sort($aliParams);
    $sign_str = join('', $aliParams);
	
	if(!empty($apiInfo)){
    	$sign_str = $apiInfo . $sign_str;
	}
	
	
	//var_dump($sign_str);
	
    $code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
	
	
	    
    return $code_sign;
}

/** 
 * 发送post请求 
 * @param string $url 请求地址 
 * @param array $post_data post键值对数据 
 * @return string 
 */  
function send_post($url, $post_data=null) {  
  
  
  if ( !empty($post_data)){
  	$post_data = http_build_query($post_data);
  }  

  $options = array(  
    'http' => array(  
      'method' => 'POST',  
      'header' => 'Content-type:application/x-www-form-urlencoded',
      "user_agent"	=> "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; GreenBrowser)",
      'content' => $post_data,  
      'timeout' => 15 * 60 // 超时时间（单位:s）  
    )  
  );  
	trace($url."?".$post_data,"uri");   
	   

  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
    
     
  //var_dump('encode_postdata:'.$postdata,$result);
  return $result;  
}

function send_get($url, $get_data) {  
  
  if ( !empty($get_data)){
  	$getdata = http_build_query($get_data);
  }  
  else{
  	$getdata = $get_data;
  }
  $url = $url ."?".$getdata;
  $options = array(  
    'http' => array(  
      'method' => 'GET',  
      'header' => 'Content-type:application/x-www-form-urlencoded',   
      'timeout' => 15 * 60 // 超时时间（单位:s）  
    )  
  );  


  $context = stream_context_create($options);  
  $result = file_get_contents($url, false, $context);  
     
  //var_dump($url,$result);  
  return $result;  
}


function _getToken($id)
{
	if ( !session("?access_token") ){
		$usertoken = M("WdzsUserToken");
		$usertoken->order("id desc")->find();
		session("access_token", $usertoken->access_token);
	}
	
	
	return session("access_token");
	
}
		
function do_sync($urls){
	if(IS_POST){


	}
} 
	

/*
 *	获取1688网站的类目信息。
 */
function getCat($catid){
	
	//$cat = M(“WdzsApiCategory”);
	if(empty($catid) ){
		$catid = 0;	
	}
	
	
	$postdata= array(
		"categoryID"=> $catid,
		"webSite"	=> "1688",
	);	
	$catinfo= _invokeApi("alibaba.category.get",$postdata);
	
	
	
	$catinfo["categoryInfo"][0]["api_type"] = "1688";
	
	
	
	 
	return $catinfo["categoryInfo"][0];
		
	
	
	
}

/*
 *	获取1688网站的类目属性。
 */
function getCatAttr($catid){
	
	//$cat = M(“WdzsApiCategory”);
	if(empty($catid) ){
		$catid = 0;	
	}
	
	
	$postdata= array(
		"categoryID"=> 122196005,
		"webSite"	=> "1688",
	);	
	$catinfo= _invokeApi("alibaba.category.attribute.get",$postdata,TRUE,TRUE);
	//$catinfo["categoryInfo"][0]["api_type"] = "1688";
	return $catinfo;		
}

/*
 * 获取商品列表信息。
 */
function getProductList()
{

	$postdata= array(
		"webSite"		=> "1688",
		
	);	

	return _invokeApi("alibaba.product.getList",$postdata,True,True);	

}

function getProduct($productId)
{

	$postdata= array(
		"productID"		=>	$productId,
		"webSite"		=> "1688",
	);	
	
	return _invokeApi("alibaba.product.get",$postdata,True,True);
			
}

function addProduct(){
	$postdata= array(
		"productType"	=>	"wholesale",
		"categoryID"	=>	"122196005",		
		"groupID"		=>	"{[69931588,70763194]}",
		"subject"		=>	"孕妇装裙子夏季大牌走秀原单肩章系带不规则雪纺连衣裙subject12",
		"description"	=>	"孕妇装裙子夏季欧美外贸大牌走秀原单肩章系带不规则雪纺连衣裙description",
		"language"		=>	"CHINESE",
		"periodOfValidity"=>	200,
		"bizType"		=>	1,
		"pictureAuth"	=>	"false",
		"image"			=>	'{"images":["img/ibank/2016/364/959/2882959463_858335242.jpg","img/ibank/2016/364/959/2882959463_858335242.jpg"]}',
		"attributes"	=>	'[{"attributeID":364,"attributeName":"产品类别","value":"连衣裙","isCustom":false},'.
			'{"attributeID":100000691,"attributeName":"货源类别","value":"现货","isCustom":false},'.
			'{"attributeID":2176,"attributeName":"品牌","value":"其他","isCustom":false},'.
			'{"attributeID":346,"attributeName":"产地","value":"广州","isCustom":false},'.
			'{"attributeID":100017842,"attributeName":"最快出货时间","value":"1-3天","isCustom":false},'.
			'{"attributeID":973,"attributeName":"风格","value":"欧美","isCustom":false},'.
			'{"attributeID":2531,"attributeName":"适合季节","value":"夏季","isCustom":false},'.
			'{"attributeID":7002,"attributeName":"厚薄","value":"普通","isCustom":false},'.
			'{"attributeID":20602,"attributeName":"领型","value":"圆领","isCustom":false},'.
			'{"attributeID":7001,"attributeName":"袖长","value":"常规","isCustom":false},'.
			'{"attributeID":31610,"attributeName":"衣长","value":"常规","isCustom":false},'.
			'{"attributeID":3216,"attributeName":"颜色","value":"砖红色","isCustom":false},'.
			'{"attributeID":100031521,"attributeName":"面料名称","value":"雪纺","isCustom":false},'.
			'{"attributeID":117130178,"attributeName":"主面料成分","value":"聚酯纤维（涤纶）","isCustom":false},'.
			'{"attributeID":149092418,"attributeName":"主面料成分含量","value":"90","isCustom":false},'.
			'{"attributeID":450,"attributeName":"尺码","value":"均码（外贸加长版）","isCustom":false},'.
			'{"attributeID":7869,"attributeName":"是否进口","value":"是","isCustom":false},'.
			'{"attributeID":159484581,"attributeName":"原产国/地区","value":"中国","isCustom":false},'.			
			'{"attributeID":1398,"attributeName":"货号","value":"3089C601232","isCustom":false}]',
		"skuInfos"		=>	'[{"attributes":[{"attributeID":3216,"attributeValue":"砖红色"},{"attributeID":450,"attributeValue":"均码（外贸加长版）"}],"cargoNumber":"","amountOnSale":888,"retailPrice":64.0,"skuId":3149890863276,"specId":"9da620ca4ba93e8c6ff98936d3de4f00"}]',
		"saleInfo"		=>	'{"supportOnlineTrade":true,"mixWholeSale":true,"saleType":"normal","priceAuth":false,"priceRanges":[{"startQuantity":1,"price":39.0},{"startQuantity":10,"price":38.0},{"startQuantity":20,"price":37.0}],"amountOnSale":2663.0,"unit":"件","minOrderQuantity":1,"quoteType":2}',
		"shippingInfo"	=>	'{"freightTemplateID":3485370,"unitWeight":0.2,"sendGoodsAddressId":11933061}',
		"webSite"		=> "1688",	
	);	
	
	return _invokeApi("alibaba.product.add",$postdata,True,True);	
}

function addGroup($groupname){
	
	if(empty($groupname)){
		$groupname = "TEST-GROUP";
	}
	
	$postdata= array(
		"name"	=>	$groupname,
		"parentID"	=>	-1,
		"webSite"		=> "1688",
		
	);	
	return _invokeApi("alibaba.product.group.add",$postdata,True,True,TRUE);
}

function getSwitch(){
	$postdata= array(		
		"webSite"		=> "1688",		
	);
	return _invokeApi("alibaba.product.group.getSwitch", $postdata,TRUE,TRUE);
	
}

function getGroup($groupid){
	
	if(empty($groupname)){
		$groupname = "TEST-GROUP";
	}
	
	$postdata= array(
		"parentID"	=>	-1,
		"webSite"		=> "1688",
		
	);	
	return _invokeApi("alibaba.product.group.get",$postdata,True,True,TRUE);
}

function getGroupList(){
		
	$postdata= array(	
		"groupID"		=>-1,	
		"webSite"		=> "1688",
		
	);	
	return _invokeApi("alibaba.product.group.getList",$postdata,TRUE,TRUE);
}


function _invokeApi($apiname,$postdata,$needSign=FALSE,$needToken=FALSE,$https=FALSE){
	
	$apiInfo ="param2/1/com.alibaba.product/".$apiname."/";
	$url = C("API_1688.API_BASE") . $apiInfo. C('API_1688.APP_KEY') ;
	
	if($https){
		$url = str_replace("http","https",$url);
	}

	
	if($needToken){
		$t = _getToken(1);
		$postdata["access_token"]=	$t;	
	}
	
	
	if($needSign){
		$s = Signature($apiInfo, $postdata);
		$postdata['_aop_signature'] = $s;
	}
			
	
	$json = send_post($url,$postdata);
	
	trace($json,"api return");
	
	$arr =json_decode($json,true);
		
	return $arr;
}

function _init_apiinfo()
{		
	$apiinfo = M("WdzsApiInfo");
	$arr = $apiinfo->where("api_type='1688'")->select();
	
	C('API_1688.APP_KEY',$arr[0]["api_value"]);
	C('API_1688.APP_CODE',$arr[1]["api_value"]);
	C('API_1688.R_URL',$arr[2]["api_value"]);
}

