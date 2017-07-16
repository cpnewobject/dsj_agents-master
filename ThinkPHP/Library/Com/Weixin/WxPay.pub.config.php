<?php
/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wxc3b2df3eefe8c22b';
//    const APPID = 'wx9dc849c95b0ff394';
	//受理商ID，身份标识
	const MCHID = '1263517901';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = 'Abcdefghijklmnopqrstuvwxyz123456';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = '5206ed9219c59938b15139b3894e26cb';
//	const APPSECRET = '0fef2f541a87597be44921c84454e6c7';

	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = 'http://www.xxxxxx.com/demo/js_api_call.php';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
//	const SSLCERT_PATH = 'D:\WWW\m\ThinkPHP\Library\Com\cert\apiclient_cert.pem';
//	const SSLKEY_PATH = 'D:\WWW\m\ThinkPHP\Library\Com\cert\apiclient_key.pem';
	
	//=======【异步通知url设置】===================================
	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;
}
	
?>