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
	
	if(!empty($api)){
    	$sign_str = $apiInfo . $sign_str;
	}
	
	
	
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
  
  $postdata = http_build_query($post_data);  
  $options = array(  
    'http' => array(  
      'method' => 'POST',  
      'header' => 'Content-type:application/x-www-form-urlencoded',  
      'content' => $postdata,  
      'timeout' => 15 * 60 // 超时时间（单位:s）  
    )  
  );  
  var_dump($url,$postdata);  
  
  $context = stream_context_create($options);  
  $result = file_get_contents($url, false, $context);  
  
  var_dump($http_response_code, $http_response_header,$php_errormsg);   
  
  return $result;  
}

		
function do_sync($urls){
	if(IS_POST){
		$apiInfo = C("API_1688.API_ADD_PRODUCT");
		$usertoken = M("UserToken");
		$usertoken->find(2);
		//
		$goodsModel = new \Think\Model('Goodsinfo','s_',C("SPIDER_DB"));
		$goods = $goodsModel->where('id=120')->select(); 
		
		foreach($urls as $u){
			//$goods = $goodsModel->query("SELECT * FROM `s_goodsinfo` where goodsurl like '%s' and rownum = 1",$u);			
		}			
		$g = $goods[0];		
		$img = "{\"images\":". str_replace("//", "http://", $g["goodsimgs"]) . "}";
		$productImg = json_encode(array(
			"images"	=>	$img,
		));
		$img = '{"images":["http://g03.s.alicdn.com/kf/HTB1PYE9IpXXXXbsXVXXq6xXFXXXg/200042360/HTB1PYE9IpXXXXbsXVXXq6xXFXXXg.jpg"]}';
		$postdata = array(				
			'productType' 	=>	"wholesale",
			"categoryID"	=>	"122214007",
			"subject"		=>	"goodsname",
			"description"	=>	"details",
			"language"		=>	"CHINESE",
			"image"			=>	'{"images":["http://g03.s.alicdn.com/kf/HTB1PYE9IpXXXXbsXVXXq6xXFXXXg/200042360/HTB1PYE9IpXXXXbsXVXXq6xXFXXXg.jpg","http://g01.s.alicdn.com/kf/HTB1tNhsIFXXXXb2XXXXq6xXFXXX9/200042360/HTB1tNhsIFXXXXb2XXXXq6xXFXXX9.jpg"]}',
			"webSite"		=>	"1688",
		);				
		$postdata_sys = array(
			"_aop_signature" => Signature($apiInfo,$postdata),
			"access_token"	=> $usertoken->access_token,
		);
		
		$postdata = array_merge($postdata,$postdata_sys);
		
			
		$url =  C("API_1688.API_BASE") .$apiInfo.C("API_1688.APP_KEY");
				
		$json = send_post($url,$postdata);
		var_dump($json);
	}
} 
	

/*
 *	获取1688网站的类目信息。
 */
function getCat($catid){
		$url = "http://gw.open.1688.com:80/openapi/param2/1/com.alibaba.product/alibaba.category.get/9982392?categoryID=%u&webSite=1688&access_token=5a1dbc83-8a81-4575-bb28-7feaf3be8668&_aop_signature=7974801A90C24516614C9A208E0CC36F7D7308D1";
		$url = sprintf($url,$catid);		
		$json = send_post($url);
		$arr =json_decode($json,true);		
		if ( $arr["errorMsg"] == "success")
		{
			return $arr["categoryInfo"];			
		}else
		{
			var_dump($json);	
		}
} 

function getProductList()
{
	$url = "http://gw.open.1688.com:80/openapi/param2/1/com.alibaba.product/alibaba.product.getList/9982392?&webSite=1688&access_token=5a1dbc83-8a81-4575-bb28-7feaf3be8668&_aop_signature=7974801A90C24516614C9A208E0CC36F7D7308D1";
	$json = send_post(sprintf($url,$catid));
	$arr =json_decode($json,true);
		
}
