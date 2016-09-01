<?php

/**
 * 会员注册登录
 */
namespace Wdzs\Controller;
use Common\Controller\HomebaseController;
class IndexController extends HomebaseController {
	

	public function index() {
		

		
		//$usertoken = M("WdzsUserToken");
		//$tokendata = $usertoken->order('id desc')->find();
		//刷新token
		
				
		_init_apiinfo();										
		$appKey = C('API_1688.APP_KEY');
	    $redirectUrl = C('API_1688.R_URL');					
		//生成签名
	    $code_arr = array(
	        'client_id' => $appKey,
	        'redirect_uri' => $redirectUrl,
	        'site' => 'china'
	    ); 
			$authurl="http://gw.open.1688.com/auth/authorize.htm?client_id=".$appKey."&site=china&redirect_uri=".$redirectUrl."&_aop_signature=".Signature(null,$code_arr);	
			this.redirect($authurl);
			//$this->assign("authurl",$authurl);
			//$this->display(":index");

    }
	
	public function token(){
		$authcode = I("get.code",'');
		if (!empty($authcode))
		{
			//
			_init_apiinfo();
			$tokenurl = "https://gw.open.1688.com/openapi/http/1/system.oauth2/getToken/".
				C('API_1688.APP_KEY')."?grant_type=authorization_code&need_refresh_token=true&client_id=".
				C('API_1688.APP_KEY')."&client_secret=".
				C('API_1688.APP_CODE')."&redirect_uri=http://localhost".
				U("goods/index").
				"&code=".$authcode;
			var_dump($tokenurl);			
			$json = send_post($tokenurl);			
			$tokenarr = json_decode($json,TRUE);
			var_dump($tokenarr);

			$usertoken = M("WdzsUserToken");
			//$tokenarr["createtime"] = date("Y-m-d H:i:s");
			$usertoken->add($tokenarr);
			
			
			this.redirect(U("goods/index"));
			//$this -> display(":token");
		}
		else{
			$ip = get_client_ip();
			$this->error('非法访问,已记录IP:'.$ip, U("index"));
		}
	}
	
	public function refresh_token(){
		$url = "https://gw.api.alibaba.com/openapi/param2/1/system.oauth2/getToken/".C('API_1688.APP_KEY');
		$postdata = "grant_type=refresh_token&client_id=".
			C('API_1688.APP_KEY')."&client_secret=".
			C('API_1688.APP_CODE')."&refresh_token=REFRESH_TOKEN";
		
	}

	
	

	
		
}
