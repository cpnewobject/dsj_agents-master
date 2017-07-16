<?php

/*curl请求*/
function curlRequest($curl, $https='true', $method='get', $data=null){
    $ch = curl_init($curl);
    curl_setopt($ch, CURLOPT_HEADER, 0); //不返回头信息
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//不直接输出，返回字符串
    if($https){//是https协议时
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);//不对服务器端进行验证
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);//不对客户端端进行验证
    }
    if($method=='post'){//是post提交时
        curl_setopt($ch,CURLOPT_POST,true); //设置POST提交方式
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data); //设置post提交时的数据
    }
    $str = curl_exec($ch);
    curl_close($ch);
    return $str;
}
/*data中增加sign参数*/
function addSign($data)
{
    $sign = '';
    if(is_array($data))
    {
    	ksort($data);
    	$str="";
    	foreach($data as $key=>$v){
    	    if(is_array($v)){
                $str.=$key.json_encode($v);
            }else{
                $str.=$key.$v; 
            } 
    	}
    	$API_KEY=C('API_KEY');
	$sign=strtoupper(md5($str.$API_KEY));
    }
    return $sign;
}

/**
 *  调用api方法 
 */
function getApidata($data)
{
    $sign=addSign($data);
    $data['sign']=$sign;
    $curl=C('URL').'?'.http_build_query($data);

    $result = curlRequest($curl,"false","post",NULL);
    return json_decode($result,true);
}

?>