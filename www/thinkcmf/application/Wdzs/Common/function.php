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
	if(empty($catid) ){
		$catid = 122196005;	
	}
	
	
	$apiinfo = M("WdzsApiCategoryAttr");
	$arr = $apiinfo->where("categoryid='".$catid."' and required in (1,0) ")->order("attrID")->select();
	
	if ( empty($arr))
	{
		$postdata= array(
			"categoryID"=> $catid,
			"webSite"	=> "1688",
		);	
		$data= _invokeApi("alibaba.category.attribute.get",$postdata,TRUE,TRUE);
		foreach($data["attributes"] as $r){
			$r["categoryid"] = $catid;
			$r["API_TYPE"] = "1688";
			$r["attrValues"] = json_encode($r["attrValues"]);
			$apiinfo->add($r);			
		}		
		return $data; 		
	}else{
		foreach($arr as $k=>$r){
			$arr[$k]["attrvalues"] = json_decode($r["attrvalues"],TRUE);			
		}
	}
	return $arr;
	
	
	

	//$catinfo["categoryInfo"][0]["api_type"] = "1688";
			
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

function addProduct($data){
	
	$retailPrice = $data["goodsprice"]+7;
	
	$catid = "122196005";
	
 	$goodsinfo = M("SGoodsinfo");
 	$goodsinfo->where("goodsid='".$data['gid']."'")->find();
 	
 	$attrs = null;
 	foreach($data as $k => $v){
 		if ( is_numeric($k) ){
 			if($k == 450){
 				//码数拆分
 				$sizes = explode(",", $v);
 				foreach($sizes as $s){
 					$attrs[] = array(
 							"attributeID"	=> $k,
 							"value"	=>$s,
 							"isCustom" => false,
 					);
 				}
 			
 			}else{
	 			$attrs[] = array(
	 				"attributeID"	=> $k,
	 				"value"	=>$v,
	 				"isCustom" => false,	
	 			);
 			}
 		}
 	}
 	$attrs[] = array(
 			"attributeID"	=> 7869,
 			"value"	=>"否",
 			"isCustom" => false,
 	);
	//图片需要调用上传，不能直接用他人图片链接做主图。
 	$imgs = str_replace("50x50","400x400",$goodsinfo->goodsimgs);
 	$imgs = str_replace("//","http://",$imgs);
 	
 	//addPhoto(json_decode($imgs,true));
 	//return;
 	
 	//sku属性(颜色3216+码数450)
 	$skuinfos = array();
 			
 					
 	
 	foreach($attrs as $a){
 		$arrid = $a["attributeID"] ; 		
 		if ($arrid == 3216  ){ 		
 			foreach($attrs as $ma){ 	
 				$maid = $ma["attributeID"];
 				if ($maid == 450){
 					$skuinfo = array(
 							"retailPrice" => $retailPrice,
 							"amountOnSale"=>888,
 							"skuid"=>3149890863276,
 							"specId"=>"9da620ca4ba93e8c6ff98936d3de4f00",
 							"attributes" =>array(
 									0=>array(
 											'attributeID' =>	$arrid,
 											'attributeValue' => $a["value"],
 									),
 									1=>array(
 										'attributeID' =>	$maid,
		 								'attributeValue' => $ma["value"],
 									),
 							),
		 			);
 					$skuinfos[] = $skuinfo;
 				}
 			} 			
 		} 			 			
 	}
 	$skuinfos = json_encode($skuinfos,JSON_UNESCAPED_UNICODE);
 	trace($skuinfos,"skuinfo");
 
 	
 	//销售属性
 	$saleInfo = array(
 			'supportOnlineTrade'=>true,
 			'mixWholeSale'=>true,
 			'saleType'=>'normal',
 			'priceAuth'=>false,
 			'priceRanges'=>array(
 				array('startQuantity'=>1,'price'=>$retailPrice),
 				array('startQuantity'=>10,'price'=>$retailPrice-1),
 				array('startQuantity'=>20,'price'=>$retailPrice-2),
 			) ,
 			'amountOnSale'=>888.0,
 			'unit'=>'件',
 			'minOrderQuantity'=>1,
 			'quoteType'=>2
 	);
 	$saleInfo = json_encode($saleInfo,JSON_UNESCAPED_UNICODE);
 	trace($saleInfo,"saleinfo");
	
	$postdata= array(
		"productType"	=>	"wholesale",
		"categoryID"	=>	$catid,		
		"groupID"		=>	"{[69931588,70763194]}",
		"subject"		=>	$data["goodsname"],
		"description"	=>	$data["content"],
		"language"		=>	"CHINESE",
		"periodOfValidity"=>	200,
		"bizType"		=>	1,
		"pictureAuth"	=>	"false",
		"image"			=>	"{'images':".$imgs."}",
		"attributes"	=>	json_encode($attrs,true),
// 			"[{'attributeID':364,'attributeName':'产品类别','value':'连衣裙','isCustom':false},".
// 			"{'attributeID':100000691,'attributeName':'货源类别','value':'现货','isCustom':false},".
// 			"{'attributeID':2176,'attributeName':'品牌','value':'其他','isCustom':false},".
// 			"{'attributeID':346,'attributeName':'产地','value':'广州','isCustom':false},".
// 			"{'attributeID':100017842,'attributeName':'最快出货时间','value':'1-3天','isCustom':false},".
// 			"{'attributeID':973,'attributeName':'风格','value':'欧美','isCustom':false},".
// 			"{'attributeID':2531,'attributeName':'适合季节','value':'夏季','isCustom':false},".
// 			"{'attributeID':7002,'attributeName':'厚薄','value':'普通','isCustom':false},".
// 			"{'attributeID':20602,'attributeName':'领型','value':'圆领','isCustom':false},".
// 			"{'attributeID':7001,'attributeName':'袖长','value':'常规','isCustom':false},".
// 			"{'attributeID':31610,'attributeName':'衣长','value':'常规','isCustom':false},".
// 			"{'attributeID':3216,'attributeName':'颜色','value':'砖红色','isCustom':false},".
// 			"{'attributeID':100031521,'attributeName':'面料名称','value':'雪纺','isCustom':false},".
// 			"{'attributeID':117130178,'attributeName':'主面料成分','value':'聚酯纤维（涤纶）','isCustom':false},".
// 			"{'attributeID':149092418,'attributeName':'主面料成分含量','value':'90','isCustom':false},".
// 			"{'attributeID':450,'attributeName':'尺码','value':'均码（外贸加长版）','isCustom':false},".
// 			"{'attributeID':7869,'attributeName':'是否进口','value':'是','isCustom':false},".
// 			"{'attributeID':159484581,'attributeName':'原产国/地区','value':'中国','isCustom':false},".			
// 			"{'attributeID':1398,'attributeName':'货号','value':'3089C601232','isCustom':false}]",	
		"skuInfos"		=> $skuinfos,
			//$skuinfos,
			//json_encode($skuinfos,true),	
			//"[{'attributes':[{'attributeID':3216,'attributeValue':'黑色'},{'attributeID':450,'attributeValue':'均码（外贸加长版）'}],'cargoNumber':'','amountOnSale':888,'retailPrice':64.0,'skuId':3149890863276,'specId':'9da620ca4ba93e8c6ff98936d3de4f00'}]",
		"saleInfo"		=>$saleInfo,
			//json_encode($saleInfo,true),	
			//"{'supportOnlineTrade':true,'mixWholeSale':true,'saleType':'normal','priceAuth':false,'priceRanges':[{'startQuantity':1,'price':39.0},{'startQuantity':10,'price':38.0},{'startQuantity':20,'price':37.0}],'amountOnSale':2663.0,'unit':'件','minOrderQuantity':1,'quoteType':2}",
		"shippingInfo"	=>	"{'freightTemplateID':3485370,'unitWeight':0.2,'sendGoodsAddressId':11933061}",
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
	
	_init_apiinfo();
	
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
	if ( !C('API_1688.APP_KEY'))
	{
		$apiinfo = M("WdzsApiInfo");
		$arr = $apiinfo->where("api_type='1688'")->select();
		
		C('API_1688.APP_KEY',$arr[0]["api_value"]);
		C('API_1688.APP_CODE',$arr[1]["api_value"]);
		C('API_1688.R_URL',$arr[2]["api_value"]);
		
	}			
}

function _init_cat($data, $gid=null){
	$props = array();
	if($gid){
		$goodsinfo = M("SGoodsinfo");
		$goodsinfo->where("goodsid=$gid")->find();
		
		if ( empty($goodsinfo->goodsid)){
			return htmlspecialchars("未找到商品: ".$gid);
		}
		$goodsinfo->goodsprice = intval($goodsinfo->goodsprice);
		$props = json_decode($goodsinfo->props,true);	
		
		$data["subject"]=$goodsinfo->goodsname;
		$data["description"]=$goodsinfo->details;
		$data["goodsimg"]=str_replace("50x50","400x400",json_decode($goodsinfo->goodsimgs,true)[0]);
		$data["goodsprice"]=intval($goodsinfo->goodsprice);

	}
	foreach($data as $k=>$a){
		$attrvalue = "";
		if( !is_array($a)) continue;
		switch($a["attrid"]){
			case 364 :
				$attrvalue= "连衣裙";
				break;
			case 100000691 :
				$attrvalue = "现货";
				break;
			case 2176 :
				$attrvalue = "其他";
				break;				
			case 346 :
				 $attrvalue= "广州";
				break;
			case 100017842 :
				$attrvalue= "1-3天";
				break;
			case 973 :
				$attrvalue = "韩版";
				break;	
			case 7002 :
				$attrvalue = "普通";
				break;
			case 20602 :
				$attrvalue = "圆领";
				break;
			case 7001:
				$attrvalue = "无袖";
				break;
			case 31610:
				$attrvalue = "常规";
				break;				
			case 20418023:
				$attrvalue = "实拍有模特";
				break;
			case 100031521:
				$attrvalue = "雪纺";
				break;
			case 117130178:
				$attrvalue = "聚酯纤维（涤纶）";
				break;
			case 149092418:
				$attrvalue = 90;
				break;
			case 2900:
				$attrvalue = "纯色";
				break;				
			case 2531 :
				$attrvalue = "秋季";
				break;		
			case 450 :
				$attrvalue = $props["尺码"];
				break;
			case 3216 :
				$attrvalue = $props["颜色分类"];
				break;	
			case 1398 :
				$attrvalue = $gid.'-'.str_replace("#","",$props["货号"])."0".intval($goodsinfo->goodsprice);
				break;
			default:
				$attrvalue = "default";
		}
		if($attrvalue != "default"){
			$data[$k]["attrvalues"] = $attrvalue;
		}else {
			unset($data[$k]);
		}
		
	};


	
	return $data;
}

function addPhoto($imgurls){
	
	foreach($imgurls as $k=>$url){
		
		//$img_file = DATA_PATH."tmp-$k.jpg";
		//file_put_contents($img_file, file_get_contents($url));
		
		//$fp = fopen($img_file, 'rb');
		//$content = fread($fp, filesize($img_file)); //二进制数据
		//fclose($fp);
		
		
		
		$imgdata=base64_encode(file_get_contents($url));
		trace($imgdata,"imgdata");
		
		$postdata= array(
				"groupID"		=>-1,
				"webSite"		=> "1688",
				"imageBytes"	=> $imgdata,
				"name"			=> "1688-$k",
		
		);
		return _invokeApi("alibaba.photobank.photo.add",$postdata,FALSE,TRUE);
		
	}
}

