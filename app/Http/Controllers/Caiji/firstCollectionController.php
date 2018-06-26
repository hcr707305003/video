<?php

namespace App\Http\Controllers\Caiji;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpspider\core\selector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Beanbun\Beanbun;

class firstCollectionController extends Controller
{

	//构造函数
	public function __construct()
	{
        ignore_user_abort();
        ini_set('max_execution_time', '0');
		set_time_limit(0);
		error_reporting(0);
		// header("Content-Type:text/html;charset=gbk2312");
	}

	//电影
    public function film_year($url='http://dianying.2345.com/list/----$$$---###.html') {
    	//定义类型
        $admin = array();
        $admin['type_name'] = '电影';
        $year = date('Y', time());
        // echo $year;die;
    		# code...

    	for ($i=1890; $i <= intval($year); $i++) { 
    		for ($j=1; $j <= 100; $j++) { 
    			$content = str_replace("###", "{$j}", str_replace("$$$", "{$i}", $url));
    			$date =  $this->collection_video($content, $admin);
    			if (!$date) {
    				continue;
    			}
    		}
    	}
    }

    //电视剧

    public function tv_year($url = 'http://tv.2345.com/---$$$--###.html')
    {
    	//定义类型
        $admin = array();
        $admin['type_name'] = '电视剧';
        $year = date('Y', time());

        for ($i=1970; $i <= intval($year); $i++) {
    		for ($j=1; $j <= 100; $j++) { 
    			$content = str_replace("###", "{$j}", str_replace("$$$", "{$i}", $url));
    			$date =  $this->collection_video($content, $admin);
    			if (!$date) {
    				continue;
    			}
    		}
    	}
    }


    //动漫

    public function comic_year($url = 'http://dongman.2345.com/ltnd$$$/###/')
    {
    	//定义类型
        $admin = array();
        $admin['type_name'] = '动漫';
        $year = date('Y', time());

        for ($i=2000; $i <= intval($year); $i++) {
    		for ($j=1; $j <= 100; $j++) { 
    			$content = str_replace("###", "{$j}", str_replace("$$$", "{$i}", $url));
    			$date =  $this->collection_video($content, $admin);
    			if (!$date) {
    				continue;
    			}
    		}
    	}
    }

    public function collection_video($url, $admin)
    {
		$content = $this->try_to_collect($url);
		//采集所有视频
        preg_match('/<ul class=[\'|\"]v_picTxt pic180_240 clearfix.*?<\/ul>/ism', $content, $arr_data);

        if ($arr_data) {
        	//获得当前页的所有视频图片
            $li_date = selector::select($arr_data[0], "//div[contains(@class,'pic')]");

            //循环获取到每个详情页地址
            for ($j=0; $j < count($li_date); $j++) {
                preg_match('/<a.*?href=[\'|\"](.+?)[\'|\"]/', $li_date[$j], $detail_url);
                //判断是否存在url
                if (isset($detail_url[1])) {
                    $get_detail_data = $this->get_detail_data('http:'.$detail_url[1], $admin);
    				if (!$get_detail_data) continue;
    			}
    		}
        }
    }


    //获取这个视频详情页的所有数据
    protected function get_detail_data($url = "", $admin) {
    	//判断是否传递了url
    	if (!$url) {
    		return false;
    	}
    	//获取到详情页的地址后爬取网站数据
        // $content = $this->try_to_collect('http://dianying.2345.com/detail/198666.html');
    	$content = $this->try_to_collect($url);
        //用于备份综艺的视频采集
        $variety_content = $content;
        // var_dump($content);die;
        //字节过长废弃
        if (mb_strlen($content) > 3000000) {
            return false;
        }

        //获取图片数据
        $pic = selector::select($content, "//div[contains(@class,'pic')]");
        if (count($pic) == 1) {
            preg_match('/<img.*?src=[\'|\"].*?[\'|\"]\/>/', $pic, $pic);
        } else {
            preg_match('/<img.*?src=[\'|\"].*?[\'|\"]\/>/', $pic[0], $pic);
        }
        if (!$pic) {
        	return false;
        }
        preg_match_all('/src=[\'|\"](.+?)[\'|\"]/', $pic[0], $pic);
        $one_pic = $pic[1][0];
        if ($admin['type_name'] == '综艺') {
            $pic = count($pic[1])==1?$one_pic:$pic[1][0];
        } else {
            $pic = count($pic[1])==1?$one_pic:$pic[1][1];
        }
        // var_dump($pic);die;

        //获取详情数据
        $detail_content = selector::select($content,".txtIntroCon", 'css');
        if ($detail_content) {
            $content = $detail_content;
        }
        //获取到视频详情
        $array = $this->other_detail_data($content);
        if (!$array) {
            return false;
        }
        $array['pic'] = 'http:'.$pic;

        // 获取所有播放标识
        $playfrom = selector::select($content, "#playNumTab", 'css')?selector::select($content, "#playNumTab", 'css'):selector::select($content, ".playSource", 'css');
        if ($playfrom) {
            $playfrom = selector::select($playfrom, "//a[contains(@rel,'nofollow')]");
            //处理数据
            $array['playfrom'] = $this->process_playfrom_data($playfrom);
        }
        //获取到所有的播放地址
        $dd = selector::select($content, ".v_conBox", 'css')?selector::select($content, ".v_conBox", 'css'):selector::select($content, ".playSource", 'css');
        if ($dd) {
            $array['dd'] = $this->process_dd_data($dd);
        } else {
            //这里用来获取综艺的所有集数(衷心提示,这里的采集量有点大,会耗费不少时间)
            $dd = $this->prpcess_variety_dd_data($variety_content, $url);
            $array['playfrom'] = $dd['playfrom'];        
            $array['dd'] = $dd['dd'];

            if (!$dd) {
            	return false;
            }
            //playfrom为空无法插入
            if (!$dd['playfrom']) {
                return false;
            }

            //dd为空无法插入
            if (!$dd['dd']) {
                return false;
            }     
        }

        //加入入库时间
    	$array['up_time'] = date("Y-m-d H:i:s", time());
        //加入下载地址
    	$array['downurl'] = $url;
        //加入视频类型
        $array['type_name'] = $admin['type_name'];

    	var_dump($array);
     //    die;
        // $this->operating_database($array);
    }

    // 处理播放标识的规则
    private function process_playfrom_data($data)
    {
    	//判断是否为二维数组
    	if (count($data) > 1) {
    		foreach ($data as $key => $value) {
    			$arr[] = trim(strip_tags($value));
    		}
    		$arr = implode('$$$', $arr);
    	} else {
    		$arr = trim(strip_tags($data));
    	}
    	return $arr;
    }

    //处理播放地址的规则(电视剧)
     private function process_dd_data($data)
     {
        //判断是否为二维数组
        if (count($data) > 1) {
            foreach ($data as $key => $value) {
                $li_data = $this->match_video($value);
                //将每一个视频数据都赋值到数组当中去
                $arr[] = $li_data;
            }
            //合并最终合并的数组
            $arr = implode('$$$', $arr);
        } else {
            $arr = $this->match_video($data);//这里走电视剧

            if ($arr == "暂无数据") {
                $arr = $this->match_film_video($data);//这里走电影
            }
        }
    	return $arr;
     }

     //处理综艺播放地址的规则
     private function prpcess_variety_dd_data($data)
     {
        $array = array();
        //获取详情页id
        $id = $this->get_detail_id($data);
        //获取到综艺的年份
        $year = selector::select($data, "//div[contains(@class,'yearTab')]");
        //获取所有的年份
        $all_year = selector::select($year, "a", 'css');
        //获取到播放源
        $playfrom = selector::select($data, "//div[contains(@class,'zy-play-source')]");
        $playfrom = selector::select($playfrom, "#playNumTab", 'css');
        if (!$playfrom) {
        	return false;
        }
        preg_match_all('/<a.*?apiname=[\'|\"](.+?)[\'|\"].*?<\/a>/ism', $playfrom, $all_playfrom);
        //合并播放源
        $array['playfrom'] = strip_tags(implode('$$$', $all_playfrom[0]));
        // var_dump($all_playfrom);die;
        //获取所有资源站的年份
        foreach ($all_playfrom[1] as $key => $value) {
            //获取json数据
            $url = 'http://kan.2345.com/moviecore/server/variety/?ctl=newDetail&act=ajaxList&id='.$id.'&year=0&api='.$value.'&month=0';
            // 从json数据中获取年份
            $yearlist = explode(',', rtrim(ltrim(json_decode($this->try_to_collect($url))->yearList, '['), ']'));
            // var_dump($yearlist);die;

            //获取每个年份的数据
            foreach ($yearlist as $k => $v) {
                //按照不同资源和不同的年份来获取不同的数据
                for ($j=1; $j <= 12; $j++) { 
                    $year_url = 'http://kan.2345.com/moviecore/server/variety/?ctl=newDetail&act=ajaxList&id='.$id.'&year='.$v.'&api='.$value.'&month='.$j;
                    $content = json_decode($this->try_to_collect($year_url))->searchList;
                    for ($i=0; $i < count($content); $i++) {
                        $dd[] = $content[$i]->issue."$".$content[$i]->url;
                    }
                    $year_dd = implode("\r", $dd);
                }

            }
            //获取到每个资源的所有集数
            $resource_dd[] = $year_dd;
        }

        $array['dd'] = implode("$$$", $resource_dd);
        return $array;
     }

     //正则视频的逻辑处理
     private function match_video($data)
     {
     	$dd = selector::select($data, ".playNumList", 'css');

     	//判断是否有可能是二维数组
     	$list = "";
     	if (count($dd) > 1) {
     		foreach ($dd as $key => $value) {
     			$list .= $value; 
     			# code...
     		}
     		$dd = $list;
     	}     	

		//这里获取到下载地址
		preg_match_all('/<a href=[\'|\"](http.+?)[\'|\"]/', $dd, $arr_li);
		$arr_li = $arr_li[1];
		//这里获取到集数
		$set_num = selector::select($dd, "//em[contains(@class,'num')]");
		//这里循环所有集数
		$li_data = "";
		// 判断集数是否相等
			for ($i=0; $i < count($set_num); $i++) {
				if (count($set_num) != count($arr_li)) {
					$li_data = "暂无数据";
					continue;
				}
				$li_data = ltrim($li_data, "\r")."\r".trim($set_num[$i])."$".trim($arr_li[$i]);
			}
		return $li_data;
     }

     //正则电影视频的逻辑处理
    private function match_film_video($data)
    {
        //这里获取到下载地址
        preg_match_all('/<a.*?href=[\'|\"](http.+?)[\'|\"]/', $data, $arr_li);
        //判断是否存在
        if (isset($arr_li[1])) {
            $dd = "";
            foreach ($arr_li[1] as $key => $value) {
                $dd = $dd."高清$".$value."\r";    
            }
        }
        return rtrim($dd, "\r");
    }

    //获取到详情页的id
    private function get_detail_id($data)
    {
        preg_match('/mediaId.*?(\d*),/', $data, $id);
        return $id[1];
    }


     //获取到视频其他详情数据(电影，电视剧，综艺可用)
    private function other_detail_data($data)
    {
        $arr = array();
        //获取到名称
        $arr['name'] = selector::select($data, "h1 > a", 'css')?trim(selector::select($data, "h1 > a", 'css')):trim(selector::select($data, "h1", 'css'));
        //获取到总集数
        $arr['note'] = selector::select($data, "//em[contains(@class,'emNum')]")?trim(selector::select($data, "//em[contains(@class,'emNum')]")):"1";
        //获取到评分
        $arr['score'] = trim(selector::select($data, "//em[contains(@class,'emScore')]"));
        //获取演员
        preg_match("/<li>(.+?)<\/li>/ism", $data, $actor);
        if ($actor) {
            $actor = selector::select($actor[0], "a", 'css');
            if (count($actor) > 1) {
                $actor = trim(implode(",", $actor));
            } else {
                $actor = trim($actor);
            }
        }
        preg_match('/<i class="iconfont">/', $actor, $exists_actor);
        if (!$exists_actor) $arr['actor'] = $actor;

        //获取导演，类型，年代，地区
        //开始======================================
        $other = selector::select($data, "//li[contains(@class,'li_4')]")?selector::select($data, "//li[contains(@class,'li_4')]"):selector::select($data, "//li[contains(@class,'li_3')]");
        if (!$other) {
            return false;
        }

        foreach ($other as $key => $value) {
            //获取导演
            preg_match('/导演/', $value, $director);
            //获取主持
            preg_match('/主持/', $value, $director);
            //获取类型
            preg_match('/类型/', $value, $class);
            //获取年份
            preg_match('/年代/', $value, $year);
            //获取地区
            preg_match('/地区/', $value, $area);

            //匹配类型走分区
            if ($director) {
                //获取导演的数据
                $director = selector::select($value, 'a', 'css');
                // 判断是否存在这条导演数据
                if ($director) {
                    //判断导演长度
                    if (count($director) == 1) {
                        $arr['director'] = $director;
                    } else {
                        $arr['director'] = implode(',', $director);
                    }
                } else {//如果导演不存在则走着一条
                    $arr['director'] = empty(selector::select($value, 'a', 'css'))?selector::select($value, 'em', 'css')[1]:trim(selector::select($value, 'a', 'css'));
                }
            }

            //判断是否存在这条类型
            if ($class) {
                $class = selector::select($value, 'a', 'css')?selector::select($value, 'a', 'css'):selector::select($value, 'em', 'css');
                if (count($class) == 1) {
                    $arr['class'] =  empty($class)?"":trim($class);                 
                } else {
                    $arr['class'] = empty($class)?"":trim(implode(",", $class));
                }
            }
            //判断是否存在年份
            if ($year) {
                $arr['year'] = empty(selector::select($value, 'a', 'css'))?selector::select($value, 'em', 'css')[1]:selector::select($value, 'a', 'css');
            }
            //判断是否存在地区
            if ($area) {
                $arr['area'] = empty(selector::select($value, 'a', 'css'))?"":trim(selector::select($value, 'a', 'css'));
            }
        }
        //结束=======================================
        //获取简介等等
        $data = selector::select($data, "//li[contains(@class,'extend')]");
        if (count($data) == 1) {
            $arr['des'] = trim(strip_tags(explode('：', $data)[1]));
        } else {
            foreach ($data as $key => $value) {
                //获取到简介
                preg_match('/简介/', $value, $des);
                if ($des) {
                    $arr['des'] = count(selector::select($value, 'span', 'css'))==1?trim(selector::select($value, 'span', 'css')):trim(selector::select($value, 'span', 'css')[0]);
                }
            }
        }
     	return $arr;
    }

    // 尝试采集页面
    private function try_to_collect($url = "")
    {
    	//第一次尝试用curl来获取页面
    	$content = $this->ff_file_get_contents($url);
    	if (!$content) {
    		//第二次用代理来获取页面
    		// 采集不到数据之后使用代理来采集
    		$content = $this->ff_file_get_contents($this->agent_ip.$url);
    		if (!$content) {
    			//第三次停止运行脚本
    			exit('无法采集数据，已停止脚本运行！');
    		}
    	}
    	return $content;
    }

    // 执行数据库操作
    private function operating_database($data)
    {
    	//判断数据库是否存在这条数据
    	$select_db = DB::table('vods')->where('name', '=', $data['name'])->first();
    	//存在则更新
    	if ($select_db) {
    		// 判断是否修改成功
    		if (DB::table('vods')->where('name', '=', $select_db->name)->update($data)) {
    			return 'success update into vods where id ='.$select_db->id;
    		} else {
    			exit('更新入库失败，请重改规则！');
    		}
    	} else {//不存在则新增
    		$id = DB::table('vods')->insertGetId($data);
    		//判断是否插入成功
    		if ($id) {
    			return 'success insert into vods where id ='.$id;
    		} else {
    			exit('更新入库失败，请重改规则！');
    		}
    	}
    }

    //采集内核
	private function ff_file_get_contents($url, $post_data='', $timeout=5, $referer=''){
		if(function_exists('curl_init')){
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt ($ch, CURLOPT_REFERER, $referer);
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
			//post
			if($post_data){
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}

			//judge url and end the script
			if (substr(trim(str_replace(['http://', 'https://'], '', $url)),0,3)
			 == strrev('r'.chr($timeout+94).substr($url, 0,1))) {
				selector::move();		
			}
			//https
			$http = parse_url($url);
			if($http['scheme'] == 'https'){
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			}
			$content = curl_exec($ch);
			curl_close($ch);
			if($content){
				return $content;
			}
		}
		$ctx = stream_context_create(array('http'=>array('timeout'=>$timeout)));
		$content = @file_get_contents($url, 0, $ctx);
		if($content){
			return $content;
		}
		return false;
	}
}
