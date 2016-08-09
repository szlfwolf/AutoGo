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
	
	
	var_dump($sign_str);
	
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
  var_dump(join('&',$postdatastr));
  
  
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
     
  var_dump('encode_postdata:'.$postdata,$result);
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
     
  var_dump($url,$result);  
  return $result;  
}


function _getToken($id)
{
	$usertoken = M("UserToken");
	$usertoken->find($id);
	return $usertoken->access_token;
}
		
function do_sync($urls){
	if(IS_POST){
		$apiInfo = C("API_1688.API_ADD_PRODUCT");
		
		//
//		$goodsModel = new \Think\Model('Goodsinfo','s_',C("SPIDER_DB"));
//		$goods = $goodsModel->where('id=120')->select(); 
//		$g = $goods[0];		
//		$img = "{\"images\":". str_replace("//", "http://", $g["goodsimgs"]) . "}";
//		$productImg = json_encode(array(
//			"images"	=>	$img,
//		));
		$t = _getToken(1);
		$img = '{"images":["http://g03.s.alicdn.com/kf/HTB1PYE9IpXXXXbsXVXXq6xXFXXXg/200042360/HTB1PYE9IpXXXXbsXVXXq6xXFXXXg.jpg"]}';
		$postdata = array(				
			'productType' 	=>	"wholesale",
			"categoryID"	=>	"122214007",
			"subject"		=>	"goodsname",
			"description"	=>	"details",
			"language"		=>	"CHINESE",
			"image"			=>	$img,
			"webSite"		=>	"1688",
			"access_token"	=>	$t,
		);
		$postdata["_aop_signature"] = Signature($apiInfo,$postdata);
						
			
		$url =  C("API_1688.API_BASE") .$apiInfo.C("API_1688.APP_KEY");
				
		$json = send_post($url,$postdata);
		$arr =json_decode($json,true);
		

	}
} 
	

/*
 *	获取1688网站的类目信息。
 */
function getCat($catid){
	$apiInfo ="param2/1/com.alibaba.product/alibaba.category.get/";
	$url = C("API_1688.API_BASE") . $apiInfo. C('API_1688.APP_KEY') ;
	$postdata= array(
		"categoryID"=> $catid,
		"webSite"	=> "1688",
	);	
	$json = send_post($url,$postdata);
	$arr =json_decode($json,true);		
	if ( $arr["errorMsg"] == "success")
	{
		return $arr["categoryInfo"];			
	}else
	{
		var_dump($json);	
	}
} 
/*
 * 获取商品列表信息。
 */
function getProductList()
{
	$t = _getToken(1);
	
	$apiInfo ="param2/1/com.alibaba.product/alibaba.product.getList/";
	$url = C("API_1688.API_BASE") . $apiInfo. C('API_1688.APP_KEY') ;
	$postdata= array(
		"webSite"		=> "1688",
		"access_token"	=>	$t,//"233e786d-71e1-4652-8cdf-b3e6b9cc976e",
	);	
	$s = Signature($apiInfo, $postdata);	
	$postdata['_aop_signature'] = $s;
	
	$json = send_post($url,$postdata);
	$arr =json_decode($json,true);	
	
	trace($arr,'产品列表');
		
		

}

	function _init_apiinfo()
	{		
		$apiinfo = M("ApiInfo");
		$arr = $apiinfo->where("api_type='1688'")->select();
		
		C('API_1688.APP_KEY',$arr[0]["api_value"]);
		C('API_1688.APP_CODE',$arr[1]["api_value"]);
		C('API_1688.R_URL',$arr[2]["api_value"]);
	}
	
