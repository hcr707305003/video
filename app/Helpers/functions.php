<?php

function curl($url = '',$data = '') {
    if(!function_exists('curl_init')) die('curlº¯Êý¿âÎ´¿ªÆô£¡£¡£¡');
    $ch = curl_init();  //³õÊ¼»¯Ò»¸öcurl
    curl_setopt($ch , CURLOPT_URL , $url);     //ÉèÖÃ½Ó¿ÚµØÖ·
    curl_setopt($ch , CURLOPT_RETURNTRANSFER , true);   //½«curl_execÒÔÎÄµµÁ÷µÄÐÎÊ½·µ»Ø¶ø²»ÊÇÖ±½ÓÊä³ö

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 	//¹Ø±ÕÑéÖ¤.Ã»Ê²Ã´¿ÉÑéµÄ.
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    if(!empty($data)) {
        curl_setopt($ch , CURLOPT_POST , true);      //Ê¹ÓÃpost·½Ê½Ìá½»
        curl_setopt($ch , CURLOPT_POSTFIELDS , $data);//Ìá½»µÄ²ÎÊý post fields
    }

    if(curl_errno($ch)) {
        echo 'curl_errno:' . curl_errno . 'curl_errno';
        echo 'curl_error:' . curl_error . 'curl_error';
    }
    $data = curl_exec($ch);    //Ö´ÐÐcurl
    if(!$data) {
        $data = 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);  //¹Ø±Õcurl
    return $data;
}


/**
 * GET 请求
 * @param string $url
 */
function http_get($url){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}

/**
 * POST 请求
 * @param string $url
 * @param array $param
 * @param boolean $post_file 是否文件上传
 * @return string content
 */
function http_post($url,$param,$post_file=false){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if (is_string($param) || $post_file) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach($param as $key=>$val){
            $aPOST[] = $key."=".urlencode($val);
        }
        $strPOST =  join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_POST,true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
 }

 /**
  * 远程跨域请求
  */
function curl_request($url,$post='',$cookie='', $returnCookie=0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if($returnCookie){
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie']  = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    }else{
        return $data;
    }
}