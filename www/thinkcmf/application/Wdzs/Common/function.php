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
function send_post($url, $post_data) {  
  
  $postdatastr=array();  
  foreach ($post_data as $key => $val) {
        $postdatastr[] = $key ."=". $val;
    }
  //var_dump(join('&',$postdatastr));
  
  
  if ( !empty($post_data)){
  	$postdata = http_build_query($post_data);
  }  
  else{
  	$postdata = $post_data;
  }
  $options = array(  
    'http' => array(  
      'method' => 'POST',  
      'header' => 'Content-type:application/x-www-form-urlencoded',  
      'content' => $postdata,  
      'timeout' => 15 * 60 // 超时时间（单位:s）  
    )  
  );  
   

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
	$usertoken = M("WdzsUserToken");
	$usertoken->find(2);
	return $usertoken->access_token;
}
		
function do_sync($urls){
	if(IS_POST){


	}
} 
	

/*
 *	获取1688网站的类目信息。
 */
function getCat($catid){	
	$postdata= array(
		"categoryID"=> $catid,
		"webSite"	=> "1688",
	);	
	return _invokeApi("alibaba.category.get",$postdata);
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
		"attributes"	=>	"",
		"groupID"		=>	"{[69931588,70763194]}",
		"subject"		=>	"孕妇装裙子夏季欧美外贸大牌走秀原单肩章系带不规则雪纺连衣裙subject",
		"description"	=>	"孕妇装裙子夏季欧美外贸大牌走秀原单肩章系带不规则雪纺连衣裙description",
		"language"		=>	"CHINESE",
		"periodOfValidity"=>	200,
		"bizType"		=>	1,
		"pictureAuth"	=>	"false",
		"image"			=>	'{"images":["img/ibank/2016/364/959/2882959463_858335242.jpg","img/ibank/2016/364/959/2882959463_858335242.jpg"]}',
		"skuInfos"		=>	"",
		"saleInfo"		=>	"",
		"shippingInfo"	=>	"",
		"webSite"		=> "1688",	
	);	
	return _invokeApi("alibaba.product.add",$postdata,True,True);	
}

function addGroup($groupname){
	
	$postdata= array(
		"name"	=>	$groupname,
		"parentID"	=>	70850122,
		"webSite"		=> "1688",
		
	);	
	return _invokeApi("alibaba.product.group.add",$postdata,True,True);
}

function _invokeApi($apiname,$postdata,$needSign,$needToken){
	
	$apiInfo ="param2/1/com.alibaba.product/".$apiname."/";
	$url = C("API_1688.API_BASE") . $apiInfo. C('API_1688.APP_KEY') ;

	
	if($needToken){
		$t = _getToken(1);
		$postdata["access_token"]=	$t;//"233e786d-71e1-4652-8cdf-b3e6b9cc976e",	
	}
	
	
	if($needSign){
		$s = Signature($apiInfo, $postdata);
		$postdata['_aop_signature'] = $s;
	}
			
	trace($postdata,"postdata");
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

