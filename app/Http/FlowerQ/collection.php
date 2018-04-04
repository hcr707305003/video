<?php

namespace App\Http\Controllers\FlowerQ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class collection extends Controller
{
    public function __construct()
	{
		//设置php最大执行时间
		ini_set('max_execution_time', '1000000');
		//设置错误模式
		// error_reporting(0);
        //采集的网站
        $this->url = "http://33uudy.com";
	}	

	public function film_get($url = "", $proxy = "", $cookie = "", $returnCookie=0)
	{
		$curl = curl_init();
        if (!$url) {
            $url = $this->url;
        }
		curl_setopt($curl, CURLOPT_PROXY, $proxy);//设置代理ip
		curl_setopt($curl, CURLOPT_URL, $url);//url地址
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');//模仿header头中 "User-Agent:"的字符串。修改user_agent来伪造成浏览器请求
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  //自cURL 7.10开始默认为 TRUE。 	FALSE 禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置，或在 CURLOPT_CAPATH中设置证书目录
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  //发送几次就重定向几次，除非设置了 CURLOPT_MAXREDIRS，限制最大重定向次数。
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);  //TRUE 时将根据 Location: 重定向时，自动设置 header 中的Referer:信息。
        // curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
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
            return $this->data = $data;
        }  
	}

    /*
    * 统计总共有多少页
    */
    public function page()
    {   
        $url = $this->film_get();//获取页面数据
        $reg = "/<a.*class=\"pagelink_a.*<\/a>/";
        $reg1 = "/<a\b[^>]+\bhref=\"([^\"]*)\"[^>]*>尾页<\/a>/";
        $reg2 = "/[0-9].*[0-9]/";
        preg_match($reg, $url, $a);
        preg_match($reg1, $a[0], $b);
        preg_match($reg2, $b[1], $c);

        //判断是否获取最大值。如果获取不到则返回1
        if ($c[0]) {
            return $c[0];
        } else {
            return 1;
        }
    }

    /*
    * 获取首页的所有数据
    */
    public function all_data($set_max_page, $set_min_page = 1)
    {   
        if ($set_max_page) {
            $this->page();
            $maxpage = $set_max_page;
        } else {
            $maxpage = $this->page();//获取最大页数
        }
        $page = $set_min_page;
        $maxpage = $set_max_page?$set_max_page:$maxpage;//判断是否存在
        $data = $this->data;//获取页面数据
        for ($page; $page <= $maxpage; $page++) {
            $max_url = $this->url.'/?m=vod-index-pg-'.$page.'.html';
            $str = $this->film_get($max_url);//获取分页的页面数据
            $reg = "/<span class=[\"|']tt[\"|'].*<\/span>/i";
            preg_match_all($reg, $str, $span_array);
            foreach ($span_array[0] as $k => $v) {
                $reg1 = "/<a href=\"[^\"]*\"[^>]*>(.*)<\/a>/";  //获取a标签的内容
                $reg2 = "/href=\"([^\"]+)/";    //获取href的链接地址
                $reg4 = '/<span[^>]*class=\"xing_vb[6|7]\".*?>.*?<\/span>/ism'; //获取视频更新时间
                preg_match($reg1, $v, $acontent);//获取每个内容
                preg_match($reg2, $v, $hrefarray);//获取每个链接
                preg_match($reg4, $v, $up_time);//获取每个更新时间
                $acontent = explode(' ', $acontent[1]);
                $arr[$k]['last'] = intval(substr(strip_tags($up_time[0]), 3, 0));
                $arr[$k]['name'] = $acontent[0];//获取名称

                $arr[$k]['letter'] = $this->getFirstCharter($acontent[0]);//获取首字母
                $arr[$k]['note'] = $acontent[1];

                //获取连载
                preg_match('/\d.*\d/', $acontent[1], $aa);
                if ($aa) {
	    			$arr[$k]['state'] = intval($aa[0]);
	    		} else {
	    			$arr[$k]['state'] = 0;
	    		}

                $url_link = $this->url.$hrefarray[1];//获取每一个视频的内容
                $one_string = $this->film_get($url_link);
                $arr_string = $this->get_link_data($one_string);
                $arr[$k]['downurl'] = $url_link;//下载地址

                foreach ($arr_string as $key => $value) {
                    $arr[$k]['pic'] = $arr_string['vod_pic'];//获取图片
                    $arr[$k]['subname'] = $arr_string['vod_ename'];//获取别名
                    $arr[$k]['director'] = $arr_string['vod_director'];//获取导演
                    $arr[$k]['actor'] = $arr_string['vod_actor'];//获取主演
                    $arr[$k]['type_name'] = $this->type_tf(isset($arr_string['vod_type'])?explode(' ', $arr_string['vod_type'])[0]: '福利片');//获取类型
                    $arr[$k]['area'] = $arr_string['vod_area'];//获取地区
                    $arr[$k]['lang'] = $arr_string['vod_language'];//获取语言
                    $arr[$k]['score'] = $arr_string['score'];//获取评分
                    $arr[$k]['year'] = $arr_string['vod_year'] == "未知"?1:$arr_string['vod_year'];//获取年份
                    $arr[$k]['playfrom'] = '';//过滤字段
                    // $arr[$k]['created_at'] = $arr_string['vod_addtime'];//获取天假时间
                    // $arr[$k]['vod_filmtime'] = $arr_string['vod_filmtime'];//获取电影时间
                    $arr[$k]['des'] = $arr_string['vod_content'];//获取内容
                    $episodes_string = '';//存放播放地址
                    foreach ($arr_string['Episodes'] as $key => $value) {
                        $episodes_string .= "$".implode('#', $value);
                    }
                    $arr[$k]['dd'] = $episodes_string;//获取播放地址
                }
            }
        }
        if ($page % 5 == 0) {
            sleep(10);
        }
        return $arr;
    }

    /**
     * 获取子页的所有数据
     **/
    public function get_link_data($url)
    {
        $reg8 = "/<div class=[\"|']vodinfobox.*<\/div>/ism";
        $reg9 = '/<ul>.*?<\/ul>/ism';
        $reg10 = '/<li>.*?<\/li>/';
        $reg11 = '/<img class=\"lazy.*?\/>/';
        $reg12 = '/<div class=\"vodplayinfo\"><!--介绍开始-->.*?<\/div>/ism';

        //采集图片
        preg_match($reg11, $url, $a);
        preg_match('/src=\"([^ \t]+)\"/', $a[0], $img_src);
        $arr['vod_pic'] = $img_src[1];

        //采集评分
        preg_match('/<label.*?<\/label>/', $url, $score);
        $arr['score'] = strip_tags($score[0]);
        //采集内容
        preg_match($reg12, $url, $content);
        $contentData = strip_tags($content[0])?strip_tags($content[0]):" ";
        $arr['vod_content'] = $contentData;

        preg_match($reg8, $url, $a);
        preg_match($reg9, $a[0], $b);
        preg_match_all($reg10, $b[0], $c);
        foreach ($c[0] as $keys => $values) {
            $arr['vod_ename'] = mb_substr(strip_tags($c[0][0]),3);
            $arr['vod_director'] = mb_substr(strip_tags($c[0][1]),3);
            $arr['vod_actor'] = mb_substr(strip_tags($c[0][2]),3);
            $arr['vod_type'] = mb_substr(strip_tags($c[0][3]),3);
            $arr['vod_area'] = mb_substr(strip_tags($c[0][4]),3);
            $arr['vod_language'] = mb_substr(strip_tags($c[0][5]),3);
            $arr['vod_year'] = mb_substr(strip_tags($c[0][6]),3);
            $arr['vod_addtime'] = time();
            // $arr['vod_filmtime'] = strtotime(mb_substr(strip_tags($c[0][7]), 3));
        }
        $reg5 = '/<h3>来源.*<\/h3>.*<ul>.*<\/ul>/ism';
        $reg6 = '/<ul>.*?<\/ul>/s';
        preg_match($reg5, $url, $a);
        preg_match_all($reg6, $a[0], $b);
        foreach ($b[0] as $key => $value) {
            $reg7 = '/<li.*?<\/li>/ism';            
            preg_match_all($reg7, $value, $all_li);
            foreach ($all_li[0] as $ks => $vs) {
                $arr['Episodes'][$key][$ks] = strip_tags($vs);
            }
        }
        return $arr;
    }

    //判断分类
    public function type_tf($type){
        if (strstr($type,'动漫')) {
            return '动漫';
        }elseif (strstr($type,'动画片')) {
            return '动漫';
        }elseif (strstr($type,'动画片')) {
            return '动漫';
        }elseif (strstr($type,'奇幻片')) {
            return '剧情片';
        }elseif (strstr($type,'伦理')) {
            return '伦理片';
        }elseif (strstr($type,'韩剧')) {
            return '日韩剧';
        }elseif (strstr($type,'韩国剧')) {
            return '日韩剧';
        }elseif (strstr($type,'其他剧')) {
            return '电视剧';
        }elseif (strstr($type,'海外剧')) {
            return '欧美剧';
        }elseif (strstr($type,'日剧')) {
            return '日韩剧';
        }elseif (strstr($type,'日本剧')) {
            return '日韩剧';
        }elseif (strstr($type,'台剧')) {
            return '港台剧';
        }elseif (strstr($type,'台湾剧')) {
            return '港台剧';
        }elseif (strstr($type,'港剧')) {
            return '港台剧';
        }elseif (strstr($type,'香港剧')) {
            return '港台剧';
        }elseif (strstr($type,'泰剧')) {
            return '电视剧';
        }elseif (strstr($type,'泰国剧')) {
            return '电视剧';
        }elseif (strstr($type,'视讯美女')) {
            return '福利片';
        }elseif (strstr($type, '腿模写真')) {
        	return '福利片';
        } 
        return $type;
    }

    public function getFirstCharter($str)//取首拼音
    {
        if (empty($str)) {
            return '';
        }
        $str=str_replace('・','',$str);
        $firstchar_ord=ord(strtoupper($str{0})); 
        if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return $str{0}; 
        $s=iconv("UTF-8","gbk", $str); 
        $asc=ord($s{0})*256+ord($s{1})-65536; 
        if($asc>=-20319 and $asc<=-20284)return "A";
        if($asc>=-20283 and $asc<=-19776)return "B";
        if($asc>=-19775 and $asc<=-19219)return "C";
        if($asc>=-19218 and $asc<=-18711)return "D";
        if($asc>=-18710 and $asc<=-18527)return "E";
        if($asc>=-18526 and $asc<=-18240)return "F";
        if($asc>=-18239 and $asc<=-17923)return "G";
        if($asc>=-17922 and $asc<=-17418)return "H";
        if($asc>=-17417 and $asc<=-16475)return "J";
        if($asc>=-16474 and $asc<=-16213)return "K";
        if($asc>=-16212 and $asc<=-15641)return "L";
        if($asc>=-15640 and $asc<=-15166)return "M";
        if($asc>=-15165 and $asc<=-14923)return "N";
        if($asc>=-14922 and $asc<=-14915)return "O";
        if($asc>=-14914 and $asc<=-14631)return "P";
        if($asc>=-14630 and $asc<=-14150)return "Q";
        if($asc>=-14149 and $asc<=-14091)return "R";
        if($asc>=-14090 and $asc<=-13319)return "S";
        if($asc>=-13318 and $asc<=-12839)return "T";
        if($asc>=-12838 and $asc<=-12557)return "W";
        if($asc>=-12556 and $asc<=-11848)return "X";
        if($asc>=-11847 and $asc<=-11056)return "Y";
        if($asc>=-11055 and $asc<=-10247)return "Z";
        return 0;//null      
    }


    //判断数据库去重(主动)
    public function insert_into($page = 1)
    {
		$this->data = 'AllIdData';
    	// $geturl = DB::table('vods')->get(['id','downurl']);
        $html = $this->all_data($page);
        // var_dump($html);
    	$geturllink = $this->updateLink();
    	$arrData = array();
    	foreach ($html as $key => $value) {
    		if (in_array($value['downurl'], $geturllink)) {
    			$one_string = $this->film_get($value['downurl']);
    			$getLinkData = $this->get_link_data($one_string);
    			$episodes_string = '';//存放播放地址
                    foreach ($getLinkData['Episodes'] as $key => $value) {
                        $episodes_string .= "$".implode('#', $value);
                    }
    			DB::table('vods')->where('id', "=", $key)
        			->update(['dd' => $episodes_string]);
    		} else {
				$getId = DB::table('vods')->insertGetId($value);
				$this->getLastId($getId, $value['downurl']);
    		}
    	}
    }

    //判断数据库去重(被动)
    public function set_to_db($data)
    {	
    	$array = array();
    	$geturllink = $this->updateLink();
    	foreach ($data as $key => $value) {
    		if (in_array($value['downurl'], $geturllink)) {
    			$one_string = $this->film_get($value['downurl']);
    			$getLinkData = $this->get_link_data($one_string);
    			$episodes_string = '';//存放播放地址
                    foreach ($getLinkData['Episodes'] as $key => $value) {
                        $episodes_string .= "$".implode('#', $value);
                    }
    			DB::table('vods')->where('id', "=", $key)
        			->update(['dd' => $episodes_string]);
    		} else {
				$getId = DB::table('vods')->insertGetId($value);
				$array[] = $getId;
				$this->getLastId($getId, $value['downurl']);
    		}
    	}
    	return $array;
    }

    //数据不存在的时候插入id和链接
    public function getLastId($getId, $downurl) 
    {
    	$SigerId = '';
    	$arr = "";
    	$SigerId .= $getId."@".$downurl."$";
    	if (!is_dir('AllIdData')) {
    		mkdir('AllIdData', 0777);
    	} else {
			$arr .= file_get_contents("AllIdData/GetId.txt");
    	}
    	$arr .= $SigerId;
    	if (file_put_contents("AllIdData/GetId.txt", $arr))return $arr;
    }

    //数据存在需要更新链接里面的视频源
    public function updateLink() 
    {	
    	$GetIdByFile = "AllIdData/GetId.txt";
    	$data = file_get_contents($GetIdByFile);
    	$arr = explode("$", $data);
    	$geturllink = array();
    	foreach ($arr as $key => $value) {
    		if (!$value) {
    			unset($value);
    		} else {
				$url = explode('@', $value);
				$geturllink[$url[0]] = $url[1]; 
    		}
    	}
    	// var_dump($geturllink);
    	return $geturllink;
    }

    /**
     *	必须经过接口获取到的数据
     *  
     **/
    public function searchNameAllDate()
    {	
    	//通过cuel模拟post请求访问数据
    	if (!$_POST['wd']) {
    		return [
				"status"=> 400,
				"msg"=> "非法访问"
			];
    	}
    	$data = ['wd' => $_REQUEST['wd']];
    	$action_url = '/index.php?m=vod-search';
    	$post_url = $this->url.$action_url;
    	$ch = curl_init ();
    	curl_setopt ( $ch, CURLOPT_URL, $post_url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		$str = curl_exec ( $ch );
		curl_close ( $ch );
		$reg = "/<span class=[\"|']tt[\"|'].*<\/span>/i";
        preg_match_all($reg, $str, $span_array);
        foreach ($span_array[0] as $k => $v) {
            $reg1 = "/<a href=\"[^\"]*\"[^>]*>(.*)<\/a>/";  //获取a标签的内容
            $reg2 = "/href=\"([^\"]+)/";    //获取href的链接地址
            $reg4 = '/<span[^>]*class=\"xing_vb[6|7]\".*?>.*?<\/span>/ism'; //获取视频更新时间
            preg_match($reg1, $v, $acontent);//获取每个内容
            preg_match($reg2, $v, $hrefarray);//获取每个链接
            preg_match($reg4, $v, $up_time);//获取每个更新时间
            $acontent = explode(' ', $acontent[1]);
            $arr[$k]['last'] = intval(substr(strip_tags($up_time[0]), 3, 0));
            $arr[$k]['name'] = $acontent[0];//获取名称

            $arr[$k]['letter'] = $this->getFirstCharter($acontent[0]);//获取首字母
            $arr[$k]['note'] = $acontent[1];

            //获取连载
            preg_match('/\d.*\d/', $acontent[1], $aa);
            if ($aa) {
    			$arr[$k]['state'] = intval($aa[0]);
    		} else {
    			$arr[$k]['state'] = 0;
    		}

            $url_link = $this->url.$hrefarray[1];//获取每一个视频的内容
            $one_string = $this->film_get($url_link);
            $arr_string = $this->get_link_data($one_string);
            $arr[$k]['downurl'] = $url_link;//下载地址

            //判断数据库是否一样,去重

            foreach ($arr_string as $key => $value) {
                $arr[$k]['pic'] = $arr_string['vod_pic'];//获取图片
                $arr[$k]['subname'] = $arr_string['vod_ename'];//获取别名
                $arr[$k]['director'] = $arr_string['vod_director'];//获取导演
                $arr[$k]['actor'] = $arr_string['vod_actor'];//获取主演
                $arr[$k]['type_name'] = $this->type_tf(isset($arr_string['vod_type'])?explode(' ', $arr_string['vod_type'])[0]: '福利片');//获取类型
                $arr[$k]['area'] = $arr_string['vod_area'];//获取地区
                $arr[$k]['lang'] = $arr_string['vod_language'];//获取语言
                $arr[$k]['score'] = $arr_string['score'];//获取评分
                $arr[$k]['year'] = $arr_string['vod_year'] == "未知"?1:$arr_string['vod_year'];//获取年份
                $arr[$k]['playfrom'] = '';//过滤字段
                // $arr[$k]['created_at'] = $arr_string['vod_addtime'];//获取天假时间
                // $arr[$k]['vod_filmtime'] = $arr_string['vod_filmtime'];//获取电影时间
                $arr[$k]['des'] = $arr_string['vod_content'];//获取内容
                $episodes_string = '';//存放播放地址
                foreach ($arr_string['Episodes'] as $key => $value) {
                    $episodes_string .= "$".implode('#', $value);
                }
                $arr[$k]['dd'] = $episodes_string;//获取播放地址
            }
        }
            $all_id = $this->set_to_db($arr);
            return responce()->josn($all_id);
    }

    /*
	* 删除视频数据
    */
    public function delDate()
    {
    	if (!$_REQUEST['id']) {
    		return [
    			"status"=> 400,
				"msg"=> "非法访问"    		
			];
    	}
    	$id = $_REQUEST['id'];
    	// $id = '107';
    	$arr = array();
    	$all_data = array();
    	$allDate = file_get_contents('AllIdData/GetId.txt');
    	foreach (explode("$", $allDate) as $key => $value) {
    		$arr[$key] = $value;
    	}
    	foreach (array_filter($arr) as $key => $value) {
    		$a = explode('@', $value);
    		$all_data[$a[0]] = $a;
    	}
    	// var_dump($all_data);
    	unset($all_data[$id]);
    	$all_string = "";

    	// var_dump($all_data);
    	foreach ($all_data as $key => $value) {
    		$all_string .= $value[0]."@".$value[1]."$";
    	}
    	file_put_contents('AllIdData/GetId.txt', $all_string);
    }

    /*
	* 恢复视频数据
    */

    public function recoveryData()
    {	
    	if (!$_REQUEST['id'] && !$_REQUEST['downurl']) {
    		return [
    			"status"=> 400,
				"msg"=> "非法访问"    		
			]; 
    	}
    	$array = [
    		'id' => $_REQUEST['id'],
    		'downurl' => $_REQUEST['downurl']
    	];
    	$data = $array;
    	$allDate = file_get_contents('AllIdData/GetId.txt');
    	$str_data = "";
    	$str_data .= $allDate.$array['id']."@".$array['downurl']."$";
    	file_put_contents('AllIdData/GetId.txt', $str_data);
    }
}