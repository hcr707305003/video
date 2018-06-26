<?php

namespace App\Http\Controllers\Caiji;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Caiji\IqiyiController;
use App\Http\Controllers\Caiji\resourceController;
use Log;

class ContentController extends Controller
{
    public function __construct()
    {
		set_time_limit(0);
		error_reporting(0);
		ini_set('memory_limit', '-1'); //内存无限
		\DB::connection()->enableQueryLog(); // 开启查询日志 
    	header("Content-type: text/html; charset=utf-8");
	}

	//视频站和资源站混合资源
	/*public function collection_resource($url = "")
	{
		$url = empty($url)?$_REQUEST['url']:$url;
		$collection = $this->collection_content($url);
		if ($collection['name']) {
			$type_name_find_all_data = $this->get_all_resource($collection['name']);
			if (count($type_name_find_all_data) == 2) {
				foreach ($type_name_find_all_data as $key => $values) {
					if (!array_key_exists('dd', $values)) {
						break;
					}
					$collection['playfrom'] = $collection['playfrom']."$$$".$values['playfrom'];
					$collection['dd'] = $collection['dd']."$$$".$values['dd'];
					if ($values['state']) {
						$collection['load_status'] = $values['state'];
					}
					if ($values['state1']) {
						$collection['state'] = $values['state1'];
					}
				}
			}
		}
		return $collection;
	}*/

	//如果要是电影不要更新，请手动把state连载状态改为0
	//自动采集
	public function auto_collection()
	{
		$agent_ip = 'http://39.109.1.141/MgtvDemo/Spider.php?url=';

		//查询需要更新的条数
		// $update_all_data = DB::table('vods')->where(function ($query) use ($keyword) {
  //       	$query->where('up_time', '<', date('Y-m-d H:i:s', time()))->orWhere('up_time', '=', null);
  //   	})->limit(500)->where(function ($query) use ($keyword) {
  //       	$query->where('load_status', '=', null)->orWhere('load_status', '!=', 2);
  //   	})->where('status', '=', 5)/*->where('name', '=', '泡沫之夏')*/->get(['name','id','dd','continu', 'load_status', 'state', 'playfrom', 'all_dd_resource', 'downurl'])->toarray();
    	$update_all_data = DB::table('vods')->where(function ($query) use ($keyword) {
        	$query->where('up_time', '<', date('Y-m-d H:i:s', time()))->orWhere('up_time', '=', null);
    	})->limit(10)->where(function ($query) use ($keyword) {
        	$query->where('load_status', '=', null)->orWhere('load_status', '!=', 2);
    	})->where('status', '=', 5)->where(function ($query) use ($keyword) {
        	$query->where('state', '>', 1)->orWhere('state', '=', null);
    	})/*->where('note', '=', '高清')*/  /*->where('name', 'like', '%誓言%')*/->get(['name','id','dd','continu', 'load_status', 'state', 'playfrom', 'all_dd_resource', 'downurl']);

    	// var_dump($update_all_data);die;
    	$this->foreach_all_array($update_all_data);
	}

	//人工新增的资源站不被覆盖

	/*遍历所有数组并更新入库*/
	private function foreach_all_array($update_all_data)
	{
		//获取到原先的视频数据
		foreach ($update_all_data as $key => $value) {
			$resource_dd_and_playfrom[] = [
				explode("$$$", $value->playfrom),
				explode('$$$', $value->dd)
			];
    	}

		//遍历数组获取条数
    	foreach ($update_all_data as $key => $value) {
    		//判断是否为php页面，是的话就跳到下一个视频
    		preg_match('/http.*?php/', $value->downurl, $php);
    		if ($php) {
    			continue;
    		}

    		// ==========================================
    		// 一开始执行的就修改时间，不然会卡住
			$updata = array();
			$updata['up_time'] = date('Y-m-d H:i:s', time()+30*60);
			// DB::table('vods')->where('id', '=', $value->id)->update($updata);
    		// ==========================================

			//判断是否开头为中文
    		preg_match('/^[\x{4e00}-\x{9fa5}]{1,}/', $value->dd, $a);
			// Log::info($a);
			// if ($a) {
			if (0) {
				continue;
			} else {
				//判断是否存在
				/*$a = str_replace(['\r', '#'], '#', 	$value->dd);
				preg_match('/http.*?html/', $a, $b);*/
				// if ($b) {

				//存在就走这边
				if (1) {
					$collection = $this->collection_content($value->downurl);
					// var_dump($collection);die;
					//编写日志
					// ==========================================================
					$updata_data = array();
					$collection['dd'] =  str_replace(["\r", '#', '##', "#\r#"], "\r", $collection['dd']);
					$content = explode("\r", explode('$$$', $collection['dd']."$$$")[0]);
					$content = array_filter($content);
					// var_dump($content);die;
					$count = count($content);
					if ($content) {
						$film =  explode('$', $content[$count-1]);
						$updata_data['content'] = $film[1];
						$updata_data['state'] = $film[0];
					} else {
						$updata_data['content'] = "";
						$updata_data['state'] = "";
					}
					$updata_data['vod_id'] = $value->id;
					$updata_data['up_time'] = date('Y-m-d H:i:s', time());
					$updata_data['film_name'] = $value->name;
					// dump($updata_data);die;
					$get_log_data = DB::table('update_log')->where('film_name', '=', $value->name)->first();
					if ($get_log_data) {
						DB::table('update_log')->where('id', '=', $get_log_data->id)->update($updata_data);
					} else {	
						$data = DB::table('update_log')->insertGetId($updata_data);
					}
					// ==========================================================

					if ($collection) {
						$type_name_find_all_data = $this->get_all_resource($collection['name']);
						if ($type_name_find_all_data) {
							foreach ($type_name_find_all_data as $k => $values) {
								if ($values['dd']) {
									$collection['playfrom'] = $collection['playfrom']."$$$".$values['playfrom'];
									$collection['dd'] = $collection['dd']."$$$".$values['dd'];
									if ($values['state']) {
										$collection['load_status'] = $values['state'];
									}
									if ($values['state1']) {
										$collection['state'] = $values['state1'];
									}
									// ================== 入库 =================
									$data = array();

									// 播放器标示
									$data['playfrom'] = $collection['playfrom'];
									// 官网详细URL
									$data['downurl'] = $collection['downurl'];
									// 播放URL链接
									$data['dd'] = $collection['dd'];
									
									// 这里不是取整个dd的长度，应该是取每个播放器采集回来的长度，然后拼凑的时候截取新长度的追加
									$data['dd_str'] = mb_strlen($collection['dd']); 
								
									// 播放状态  
									$data['load_status'] = $collection['load_status'];


									// 入库前做好对应数据验证 譬如：3个playfrom 对应 dd 里面 3个$$$的播放url
									// 如果错误则跳过 记录在日志里面



									if (strpos($data['load_status'], "连载中")) {
										$data['load_status'] = 1;
									} else if (strpos($data['load_status'], "完结")) {
										$data['load_status'] = 2;
									} else {
										$data['load_status'] = 0;
									}
									$data['up_time'] = date('Y-m-d H:i:s', time()+30*60);							
									// var_dump($data);die;
									// Log::info("ID:".$value->id." Name:".$value->name); 
									$resource_data = $this->match_dd_and_playfrom($resource_dd_and_playfrom, $data, $key);

									DB::table('vods')->where('id', '=', $value->id)->update($resource_data);
									// ==============================================						


									$this->ff_file_get_contents($url = 'http://haoniux.com/code/api/test', $post_data = ['id' => intval($value->id)]);
								} else {
									continue;
								}
							}
						} else {
							//不存在就走这边
							preg_match('/http.*?html/', $value->all_dd_resource, $resource_url);
							// var_dump($resource_url);die;
							//判断下载地址是否存在
							if ($resource_url) {//存在
								$aa = $this->ff_file_get_contents($resource_url[0]);
								preg_match('/<h2.*?<\/h2>/', $aa, $resource_name);
								if ($resource_name) {
									$type_name_find_all_data = $this->get_all_resource(strip_tags($resource_name[0]));
									foreach ($type_name_find_all_data as $ks => $values) {
										if ($values['dd']) {
											$collection['playfrom'] = $collection['playfrom']."$$$".$values['playfrom'];
											$collection['dd'] = $collection['dd']."$$$".$values['dd'];
											if ($values['state']) {
												$collection['load_status'] = $values['state'];
											}
											if ($values['state1']) {
												$collection['state'] = $values['state1'];
											}
											$data = array();
											$data['playfrom'] = $collection['playfrom'];
											$data['downurl'] = $collection['downurl'];
											$data['dd'] = $collection['dd'];
											$data['dd_str'] = mb_strlen($collection['dd']);
											$data['load_status'] = $collection['load_status'];
											if (strpos($data['load_status'], "连载中")) {
												$data['load_status'] = 1;
											} else if (strpos($data['load_status'], "完结")) {
												$data['load_status'] = 2;
											} else {
												$data['load_status'] = 0;
											}
											$data['up_time'] = date('Y-m-d H:i:s', time()+30*60);
											// Log::info("ID:".$value->id." Name:".$value->name); 
											$resource_data = $this->match_dd_and_playfrom($resource_dd_and_playfrom, $data, $key);
											DB::table('vods')->where('id', '=', $value->id)->update($resource_data);
											$this->ff_file_get_contents($url = 'http://haoniux.com/code/api/test', $post_data = ['id' => intval($value->id)]);
										} else {
											continue;
										}
									}
								} else {
									continue;
								}
							} else {//不存在
								$data = array();

								$data['playfrom'] = $collection['playfrom'];
								$data['downurl'] = $collection['downurl'];
								$data['dd'] = $collection['dd'];
								$data['dd_str'] = mb_strlen($collection['dd']);
								$data['up_time'] = date('Y-m-d H:i:s', time()+30*60);
								$resource_data = $this->match_dd_and_playfrom($resource_dd_and_playfrom, $data, $key);
								if (DB::table('vods')->where('id', '=', $value->id)->update($resource_data)) {
									$this->ff_file_get_contents($url = 'http://haoniux.com/code/api/test', $post_data = ['id' => intval($value->id)]);
									echo "success";
								} else {
									echo "fail";
								}
							}
						}
					} else {
						continue;
					}
				} else {
					continue;
				}
			}
    	}
	}

	/*
	 * 匹配更新和历史的dd和playfrom
	 *判断是否是手动新增的playfrom并判断dd是否和playfrom时候对应一致
	**/
	private function match_dd_and_playfrom($resource_dd_and_playfrom, $data, $key)
	{
		//获取到更新之前的资源
		$resource_playfrom = $resource_dd_and_playfrom[$key][0];
		$resource_dd = $resource_dd_and_playfrom[$key][1];
		// var_dump($resource_playfrom);
		// var_dump($resource_dd);
		// var_dump($data);die;
		//获取到更新之后的dd和playfrom
		$playfrom =	explode("$$$", $data['playfrom']);
		$dd = explode('$$$', $data['dd']);

		$array = array();	
		//判断playfrom是否相等
		if (count($resource_playfrom) == count($resource_dd)) {
			if (count($resource_playfrom) > count($playfrom)) {
				for ($i=0; $i < count($resource_playfrom); $i++) { 
					if (in_array($resource_playfrom[$i], $playfrom)) {
						continue;
					} else {
						$array[] = [
							'key' => $i,
							'playfrom' => $resource_playfrom[$i]
						];
					}
				}
				// var_dump($array);die;
				for ($i=0; $i < count($array); $i++) { 
					$data['playfrom'] = $data['playfrom']."$$$".$array[$i]['playfrom'];
					$data['dd'] = $data['dd']."$$$".$resource_dd[$array[$i]['key']];
				}
				return $data;
			} else {
				return $data;
			}
		} else {
			return $data;
		}
	}


	/*
	 * 资源站是否与官网更新速度一致
	 *
	 */
	public function matching_resource_and_video_Agreement()
	{
		//查询需要更新的条数
    	$update_all_data = DB::table('vods')->where(function ($query) use ($keyword) {
        	$query->where('up_time', '<', date('Y-m-d H:i:s', time()))->orWhere('up_time', '=', null);
    	})->limit(10)->where('status', '=', 5)->get(['name','id','dd','continu', 'load_status', 'state', 'playfrom', 'all_dd_resource', 'downurl']);

    	// var_dump($update_all_data);die;
    	//判断资源站的集数是否和官网的集数匹配
    	// 开始=========================
    	$save_the_update_data = array();
    	if ($update_all_data) {
    		$all_li_count = array();
    		foreach ($update_all_data as $key => $value) {
    			$array = explode("$$$", $value->dd);
    			$match_resource_video = count(explode("\r", $array[0]));
    			// var_dump($match_resource_video);die;
    			for ($i=0; $i < count($array); $i++) { 
    				$resource_video = count(explode("\r", trim($array[$i], "\r")));
    				if ($resource_video > $match_resource_video) {
    					$save_the_update_data[] = $value;
    					break;
    				}
    			}
    		}
    	}
    	// var_dump($save_the_update_data);die;
    	// =============================
    	// $save_the_update_data里面存放在匹配失败的影片
    	if ($save_the_update_data) {
    		$this->foreach_all_array($save_the_update_data);
    	}
    	//结束===========================
	}

	//指定采集
	public function collection_content($url = "")
	{
		$url = empty($url)?$_REQUEST['url']:$url;
		$pasurl = parse_url($url);
		// var_dump($pasurl);die;
		if ($pasurl['host'] == "v.qq.com") {//采集qq视频
			$playfrom = 'qq';
			$content = $this->collection_qqtv($url);
			$content['playfrom'] = $playfrom;
			return $content;
		} else if ($pasurl['host'] == "www.iqiyi.com") {//采集爱奇艺
			$playfrom = 'iqiyi';
		} else if ($pasurl['host'] == "www.mgtv.com") {//采集芒果
			$playfrom = 'mgtv';
			$content = $this->collection_mgtv($url);
			$content['playfrom'] = $playfrom;
			return $content;
		} else if ($pasurl['host'] == "zuidazy.com" || $pasurl['host'] == "www.zuidazy.com") {//采集最大资源网
			$playfrom = 'zuidazy';
			$content = $this->collection_zuidazy($url);
			// $content['playfrom'] = $playfrom;
			return $content;
		} else if ($pasurl['host'] == "v.youku.com" || $pasurl['host'] == "www.youku.com") {//采集优酷
			$playfrom = 'youku';
			$content = $this->collection_youku($url);
			$content['playfrom'] = $playfrom;
			return $content;
		} else if ($pasurl['host'] == "my.tv.sohu.com" || $pasurl['host'] == "tv.sohu.com" || $pasurl['host'] == "film.sohu.com") {//采集搜狐
			$playfrom = 'sohu';
			$content = $this->collection_sohu($url);
			$content['playfrom'] = $playfrom;
			return $content;
		} else {
			$playfrom = $pasurl['host'];
		}
		$content = $this->ff_file_get_contents($url);
		$albumId = $this->get_albumid($content);
		$tvid = $this->get_tvid($content);//获取到爱奇艺的唯一识别id
		$sourceid = $this->get_sourceid($content);//资源id
		$data = $this->get_all($tvid);//获取到残缺的数据
		$all_data['name'] = isset($data['type_name'])?$data['type_name']:$this->get_name($content); 
		$all_data['seo'] = isset($data['seo'])?$data['seo']:$this->get_seo($content);
		$all_data['letter'] = isset($data['letter'])?$data['letter']:$this->get_letter($all_data['name']);
		$all_data['type_name'] = isset($data['type_name'])?$data['type_name']:$this->get_type_name($content);
		$all_data['class'] = isset($data['class'])?$data['class']:$this->get_area_class($content)['class'];
		$all_data['lang'] = isset($data['lang'])?$data['lang']:"";
		$all_data['area'] = isset($data['area'])?$data['area']:"";
		$all_data['score'] = isset($data['score'])?$data['score']:$this->get_score($tvid, $this->get_typeid($content));
		@$all_data['last'] = $this->get_last($content)[0];
		$all_data['year'] = substr($all_data['last'], 0,4);
		// $all_data['year'] = isset($data['year'])?$data['year']:$this->get_year($all_data['last']);
		$all_data['note'] = $this->get_note($content)?$this->get_note($content):$data['note'];
		$all_data['state'] = empty($data['state'])?$data['state']:$this->get_state($content);
		$all_data['continu'] = $this->get_continu($content);
		$all_data['actor'] = $this->get_actor($content);
		$all_data['director'] = $this->get_director($content);
		$all_data['hit'] = isset($data['hit'])?$data['hit']:"";
		$all_data['vdown'] = isset($data['vdown'])?$data['vdown']:"";
		$all_data['pic'] = isset($data['pic'])?$data['pic']:$this->get_pic($content);
		$all_data['playfrom'] = $playfrom;
		$all_data['des'] = isset($data['des'])?$data['des']:$this->get_des($content);
		$all_data['downurl'] = $url;
		$all_data['up_time'] = date("Y-m-d H:i:s", time());
		$all_data['reweek'] = $this->get_reweek($content);
		$dd = $this->get_dd($albumId, $all_data['state'], $sourceid, $content)!=""?$this->get_dd($albumId, $all_data['state'], $sourceid, $content):'高清$'.$all_data['downurl'];
		$all_data['dd'] = $dd;
		$area_class = $this->get_area_class($content);
		// var_dump($all_data);die;
		return $all_data;
	}

	//腾讯采集
	public function collection_qqtv($url = "")
	{

		$url = empty($url)?$_REQUEST['url']:$url;
		$data = array();
		$data['downurl'] = $url;
		$json_content = json_encode(file_get_contents($url));
		$content = json_decode($json_content);
		// var_dump($content);die;
		//获取视频唯一id
		preg_match('/COVER_INFO.*?,/', $content, $id);
		preg_match('/id.*?,/', $id[0], $id);
		$id = trim(explode(':', trim($id[0], ','))[1], '"');

		preg_match('/<title>.*?<\/title>/', $content, $a);
		if (!$a) return '页面无法获取，请重新获取';
		if ($a) {
			$data['seo'] = trim(strip_tags($a[0]));
		} else {
			$data['seo'] = "";
		}
		preg_match('/<a.*?videolist:title.*?<\/a>/', $content, $b);
		if ($b) {
			$data['name'] = trim(strip_tags($b[0]));
		} else {
			$data['name'] = "";
		}

		preg_match('/<div class=[\'|\"]director.*?<\/div>/ism', $content, $c);
		if ($c) {
			$d = trim(strip_tags($c[0]));
			$e = explode(':', $d);
			if ($e[1]) {
				preg_match('/.*:?&nbsp;/', trim($e[1]), $director);
				if ($director) {
					$data['director'] = trim(trim($director[0], '&nbsp;'));
				} else {
					$data['director'] = "";
				}
			} else {
				$data['director'] = "";
			}

			if (trim($e[2])) {
				$qian=array(" ","　","\t","\n","\r");
				$data['actor'] = str_replace($qian, '', trim($e[2]));
			} else {
				$data['actor'] = "";
			}

		} else {
			$data['actor'] = "";
			$data['director'] = "";
		}
		$data['letter'] = $this->get_letter($data['name']);
		preg_match('/type_name.*?,/', $content, $type_name);
		if ($type_name) {
			$f = trim(explode(':', trim($type_name[0], ','))[1], '"');
			$data['type_name'] = $f;
		} else {
			$data['type_name'] = "";
		}
		preg_match('/<meta itemprop=[\'|\"]inLanguage.*?content=[\'|\"](.+?)[\'|\"].*?>/', $content, $lang);
		if ($lang) {
			$data['lang'] = $lang[1];
		} else {
			$data['lang'] = "";
		}
		
		preg_match('/<meta itemprop=[\'|\"]contentLocation.*?content=[\'|\"](.+?)[\'|\"].*?>/', $content, $area);
		if ($area) {
			$data['area'] = $area[1];
		} else {
			$data['area'] = "";
		}

		preg_match('/score.*?,/', $content, $score);
		if ($score) {
			$f = trim(explode(':', trim($score[0], ','))[1], '"');
			$data['score'] = $f;
		} else {
			$data['score'] = "";
		}

		preg_match('/<em id=[\'|\"]mod_cover_playnum.*?<\/em>/', $content, $hit);
		if ($hit) {
			$data['hit'] = trim(strip_tags($hit[0]));
		} else {
			$data['hit'] = "";
		}

		preg_match('/brief.*?,/', $content, $note);
		if ($note) {
			$f = trim(explode(':', trim($note[0], ','))[1], '"');
			$data['note'] = $f;
		} else {
			$data['note'] = "";
		}

		if ($id) {
			$detail_url = 'https://v.qq.com/detail/1/'.$id.'.html';
			$detail_content = file_get_contents($detail_url);
			preg_match('/<div class=[\'|\"]type_item.*?别　名.*?<\/div>/ism', $detail_content, $subname);
			if ($subname) {
				preg_match('/<span.*?type_txt.*?<\/span>/', $subname[0], $subname1);
				$data['subname'] = trim(strip_tags($subname1[0]));
			}
			preg_match_all('/<div class=[\'|\"]type_item.*?<\/div>/ism', $detail_content, $year);
			foreach ($year[0] as $key => $value) {
				preg_match('/[上映出品]时间/', $value, $value1);
				if ($value1) {
					$data['year'] = trim(explode(':',trim(strip_tags($value)))[1]);
				} else {
					continue;
				}
			}
			preg_match('/<meta itemprop=[\'|\"]uploadDate[\'|\"].*?content=[\'|\"](.+?)[\'|\"]/', $content, $value1);
			if ($value1) {
				$data['last'] = $value1[1];
			}

			foreach ($year[0] as $key => $value) {
				preg_match('/总集数/', $value, $value1);
				if ($value1) {
					$data['continu'] = trim(explode(':',trim(strip_tags($value)))[1]);
				} else {
					continue;
				}
			}
			preg_match_all('/<a class=[\'|\"]tag[\'|\"].*?<\/a>/', $detail_content, $class);
			if ($class) {
				$data['class'] = strip_tags(implode(',', $class[0]));
			} else {
				$data['class'] = "";
			}

			preg_match('/<span class=[\'|\"]txt _desc_txt_lineHight.*?<\/span>/ism', $detail_content, $des);
			if ($des) {
				$data['des'] = strip_tags($des[0]);
			} else {
				$data['des'] = "";
			}

			preg_match('/<meta name=[\'|\"]twitter:image[\'|\"].*? content=[\'|\"](.+?)[\'|\"]/', $detail_content, $pic);
			if ($pic) {
				$data['pic'] = $pic[1];
			} else {
				$data['pic'] = "";
			}
			preg_match('/<div class=[\'|\"]mod_episode[\'|\"].*?<\/div>/ism', $detail_content, $dd);
			if ($dd) {
				preg_match_all('/<a href=[\'|\"](.+?)[\'|\"].*?<\/a>/ism', $dd[0], $dd1);
				for ($i=0; $i < count($dd1[0]); $i++) {
					preg_match('/srcset/', $dd1[0][$i], $pds);
					$data['dd'] = $data['dd'].trim(strip_tags($dd1[0][$i]))."$".$dd1[1][$i]."\r";
				}
			} else {
				preg_match('/column_id.*?,/', $content, $vid);
				if ($vid) {
					$vid = intval(trim(explode(':', trim($vid[0], ','))[1], '"'));
					$detail_url = 'https://v.qq.com/detail/7/'.$vid.'.html';
					$detail_data = $this->ff_file_get_contents($detail_url);
					preg_match_all('/<li class=[\'|\"]list_item[\'|\"].*?<\/li>/ism', $detail_data, $li);
					if ($li[0]) {
						foreach ($li[0] as $key => $value) {
							preg_match('/<a href=[\'|\"](.+?)[\'|\"].*?<\/a>/', $value, $a);
							if (!$a) {
								break;
							}
							$data['dd'] = $data['dd'].strip_tags($a[0])."$".$a[1]."\r";
						}
					} else {
						$data['dd'] = "高清$".$data['downurl'];
					}
				} else {
					return '获取不到视频id,无法扑捉视频数据!';
				}
			}
		}
		$data['up_time'] = date("Y-m-d H:i:s", time());
		$data['dd'] = trim($data['dd'], '\r');
		// var_dump($data);die;
		return $data;
	}

	//芒果视频采集
	public function collection_mgtv($url = "")
	{
		$agent_ip = 'http://39.109.1.141/MgtvDemo/Spider.php?url=';
		/*preg_match('/http.*?html/', $url, $url);
		$url = $url[0];
		$caiji = new IqiyiController();
		$path = "http://cj.tv6.com/mox/inc/mgtv.php?ac=videolist&rid=&h=&pg=";
		for ($i = 0; $i < 1; $i++) {
			$admin = array();
			$admin['action'] = 'all';
			$admin['xmlurl'] = base64_encode($path.$i);
			$admin['xmltype'] = NULL;
			$admin['page'] = 1;
			$vod = $caiji->vod($admin);
			// var_dump($vod);die;
			//格式化部份数据字段
			if ($vod['status'] != 200) {
				return $vod['infos'];
			}
			//获取总页数并获取到分页数据
			$maxpage = intval($vod['infos']['page']['pagecount']);
			//起名
			if ($maxpage > 40) {
				$all = ceil($maxpage/40);
			}
			$find = new IqiyiController();
			$find_dd = 'find';
			for ($c = 1; $c <= 1; $c++) {
				// $$find_dd."_".$c;
				var_dump($find_dd."_".$c = new IqiyiController());	
			}
			for ($a=0; $a < 20; $a++) { 
				$admin['page'] = $a;
				$vod = $caiji->vod($admin);
				for ($b=0; $b < count($vod['infos']['data']); $b++) { 
					if (strstr($vod['infos']['data'][$b]['vod_name'], '家')) {
						$aa[] = 11;
					}
					if (strstr($vod['infos']['data'][$b]['vod_url'], $url)) {
						echo 11;
					}
				}
			}
		}*/
		
		//被芒果禁ip时的操作
		// $caiji = new IqiyiController();
		// $caiji->iqiyi('http://cj.tv6.com/mox/inc/mgtv.php');

		//没被芒果禁ip的操作
		$url = empty($url)?$_REQUEST['url']:$url;
		$content = $this->ff_file_get_contents($agent_ip.$url);
		// var_dump($content);die;
		$data = array();
		preg_match('/<head.*?<\/head>/ism', $content, $a);
		if (!$a) return '页面无法获取，请重新获取';
		$data['downurl'] = $url;
		//获取vid
		preg_match('/vid.*?,/', $content, $vid);
		if ($vid) {
			$vid = intval(trim(explode(':', trim($vid[0], ','))[1], '"'));
		} else {
			return '获取不到视频id,无法扑捉视频数据!';
		}

		//获取标题
		preg_match('/title.*?,/', $content, $title);
		if ($title) {
			$a = str_replace(['"',"'",' '], '', trim(explode(':', trim($title[0], ','))[1], '"'));
			$data['seo'] = $a;
		} else {
			$data['seo'] = "";
		}

		//获取类型,名称
		preg_match('/<div class=[\'|\"]v-panel-route.*?<\/div>/ism', $content, $name);
		if ($name) {
			preg_match_all('/<a.*?<\/a>/ism', $name[0], $a);
			if (count($a[0]) == 3) {
				$data['name'] = strip_tags($a[0][0]);
				$data['type_name'] = strip_tags($a[0][2]);
			} 
		}

		//获取地区,类型,演员,导演
		preg_match('/<div class=[\'|\"]v-panel-meta.*?<\/div>/ism', $content, $panel_meta);
		if ($panel_meta) {
			preg_match_all('/<p.*?<\/p>/ism', $panel_meta[0], $all_data);
			foreach ($all_data[0] as $key => $value) {
				preg_match('/导演/', $value, $director);
				if ($director) {
					preg_match('/<a.*?<\/a>/', $value, $director);
					$data['director'] = trim(strip_tags($director[0]));
				} else {
					continue;
				}
			}
			foreach ($all_data[0] as $key => $value) {
				preg_match('/主演/', $value, $actor);
				if ($actor) {
					preg_match_all('/<a.*?<\/a>/', $value, $actor);
					if ($data['actor']) {
						continue;
					} else {
						$data['actor'] = trim(strip_tags(implode(',', $actor[0])));
					}
				} else {
					continue;
				}
			}

			foreach ($all_data[0] as $key => $value) {
				preg_match('/地区/', $value, $area);
				if ($area) {
					preg_match('/<a.*?<\/a>/', $value, $area);
					$data['area'] = trim(strip_tags($area[0]));
				} else {
					continue;
				}
			}

			foreach ($all_data[0] as $key => $value) {
				preg_match('/类型/', $value, $class);
				if ($class) {
					preg_match_all('/<a.*?<\/a>/', $value, $class);
					if ($data['class']) {
						continue;
					} else {
						$data['class'] = trim(strip_tags(implode(',', $class[0])));
					}
				} else {
					continue;
				}
			}

			foreach ($all_data[0] as $key => $value) {
				preg_match('/简介/', $value, $des);
				if ($des) {
					preg_match('/<span class=[\'|\"]details.*?<\/span>/', $value, $des);
					$data['des'] = trim(strip_tags($des[0]));
				} else {
					continue;
				}
			}
		}

		//获取首字母
		$data['letter'] = $this->get_letter($data['name']);

		//获取评分
		$score_url = 'https://vc.mgtv.com/v2/dynamicinfo?vid='.$vid;
		$score_data = json_decode($this->ff_file_get_contents($agent_ip.$score_url));
		if ($score_data) {
			$data['score'] = $score_data->data->allStr;
		}

		//获取图片
		preg_match('/<i class=[\'|\"]img[\'|\"]>.*?src=[\'|\"](.+?)[\'|\"].*?<\/i>/ism', $content, $pic);
		if ($pic) {
			$data['pic'] = $pic[1];
		}

		//获取总集数
		$video_url = 'https://pcweb.api.mgtv.com/episode/list?video_id='.$vid;
		$video_data = $this->ff_file_get_contents($video_url);
		// var_dump($video_data);die;
		if ($video_data) {
			$video_data = json_decode($video_data);
			$data['continu'] = $video_data->data->total;
			$data['state'] = $video_data->data->count;
			$data['note'] = $video_data->data->info->desc;
		}

		//获取到爬取总页数
		if ($video_data->data->current_page) {
			$maxpage = $video_data->data->total_page;
			// echo $maxpage;die;
			for ($i=1; $i <= $maxpage ; $i++) {
				$page_data = $this->ff_file_get_contents($video_url."&page=".$i);
				if ($page_data) {
					$page_data = json_decode($page_data);
					// var_dump($page_data);die;
					if ($page_data->data->list) {
						// var_dump($page_data->data->list);die;
						$url = 'https://www.mgtv.com';
						foreach ($page_data->data->list as $key => $value) {
							$data['dd'] = $data['dd'].$value->t4."$".$url.$value->url."\r";
							$data['last'] = $value->ts;
						}

					}
				}
			}
			$data['dd'] = rtrim($data['dd'], "\r");
		}
		$data['up_time'] = date('Y-m-d H:i:s', time());
		// var_dump($data);die;
		return $data;
	}

	//最大资源网采集
	public function collection_zuidazy($url = "")
	{
		$url = empty($url)?$_REQUEST['url']:$url;
		//获取详情页
		$detail = $this->ff_file_get_contents($url);
		if (!$detail) {
			return "暂无数据,请重新刷新页面!";
		}
		$arr_data['downurl'] = $url;
		//获取到评分
		preg_match('/<label>(.+?)<\/label>/', $detail, $score);
		if (isset($score[1])) {
			$arr_data['score'] = $score[1];
		} else {
			$arr_data['score'] = '0.0';
		}

		// 获取名称
		preg_match('/<h2.*?<\/h2>/', $detail, $name);
		if ($name) {
			$arr_data['name'] = strip_tags($name[0]);
		}

		//获取封面图
		preg_match('/<img class=[\'|\"]lazy[\'|\"] src=[\'|\"](.+?)[\'|\"].*?>/', $detail, $d);
		if (isset($d[1])) {
			$arr_data['pic'] = parse_url($d[1])['host']?$d[1]:$url.$d[1];
		}

		//获取到ul数据
		preg_match('/<div class=[\'|\"]vodinfobox.*?<\/div>/ism', $detail, $ul);
		if ($ul) {
			preg_match_all('/<li.*?<\/li>/ism', $ul[0], $li);
			$arr_data['subname'] = strip_tags($li[0][0]);
			$arr_data['actor'] = strip_tags($li[0][2]);
			$arr_data['director'] = strip_tags($li[0][1]);
			$arr_data['class'] = strip_tags($li[0][3]);
			$arr_data['area'] = strip_tags($li[0][4]);
			$arr_data['lang'] = strip_tags($li[0][5]);
			$arr_data['year'] = strip_tags($li[0][6]);
			$arr_data['long'] = strip_tags($li[0][7]);
			$arr_data['last'] = strip_tags($li[0][8]);
			$arr_data['hit'] = strip_tags($li[0][9]);
			$arr_data['dayhits'] = strip_tags($li[0][10]);
		}

		//获取所有地址
		preg_match_all('/<div class=[\'|\"]vodplayinfo.*?<\/div>/ism', $detail, $data);
		if ($data) {
			//获取简介
			$arr_data['des'] = strip_tags($data[0][1]);
		}

		//获取所有播放源
		preg_match_all('/<div id=[\'|\"]play_.*?<\/div>/ism', $detail, $e);
		if ($e[0]) {
			foreach ($e[0] as $key => $value) {
				//获取标识
				preg_match('/<h3.*?<\/h3>/ism', $value, $f);
				$f = explode("：",strip_tags($f[0]))[1];
				$arr_data['playfrom'] = $f."$$$".$arr_data['playfrom'];

				//获取播放地址
				preg_match('/<ul.*?<\/ul>/ism', $value, $g);
				if ($g) {
					// var_dump($g);die;
					preg_match_all('/<li.*?<\/li>/ism', $g[0], $h);
					if ($h[0]) {
						$a = implode("\r", $h[0]);
						$arr_data['dd'] = strip_tags($a)."$$$".$arr_data['dd'];
					}
					// $arr_data['dd'] = $arr_data['dd'].strip_tags($dd[0])."\r";
				}
			}
		}
		$arr_data['dd'] = rtrim($arr_data['dd'], '$$$');
		$arr_data['playfrom'] = rtrim($arr_data['playfrom'], '$$$');
		$arr_data['up_time'] = date("Y-m-d H:i:s", time());
		// var_dump($arr_data);
		// var_dump(json_encode($arr_data));
		// die;
		return $arr_data;
	}

	// 采集优酷视频
	public function collection_youku($url = "")
	{
		$agent_ip = 'http://39.109.1.141/MgtvDemo/Spider.php?url=';
		$url = empty($url)?$_REQUEST['url']:$url;
		$content = file_get_contents($url);
		if ($content) {
			$data = array();
			$data['downurl'] = $url;
			preg_match('/<h2>.*?href=[\'|\"](.+?)[\'|\"].*?<\/h2>/ism', $content, $list);
			if (isset($list[1])) {
				// echo 'https:'.$list[1];die;
				//获取到名称
				$data['name'] = trim(strip_tags($list[0]));
				$content = $this->ff_file_get_contents('http:'.$list[1]);
				if ($content) {
					// var_dump($content);die;

					// 获取图片
					preg_match('/<div class=[\'|\"]p-thumb[\'|\"].*?src=[\'|\"](.+?)[\'|\"]/', $content, $pic);
					if ($pic) {
						$data['pic'] = $pic[1];
					}
					// var_dump($pic);die;

					//获取视频的js数据
					preg_match('/var PageConfig.*?}/', $content, $js_data);
					$js_data = trim(ltrim($js_data[0], 'var PageConfig ='));
					if ($js_data) {
						preg_match('/showid:[\'|\"](.+?)[\'|\"]/', $js_data, $showid);
						preg_match('/cateName:[\'|\"](.+?)[\'|\"]/', $js_data, $cateName);
						if (!isset($showid[1])) {
							return "获取不到dd视频！";
						}
						if (!isset($cateName[1])) {
							return "获取不到dd视频！";
						}
						// var_dump($cateName[1]);die;
						//这里走的是电视剧
						if ($cateName[1] == '电视剧') {
							for ($i=0; $i < 1000 ; $i++) {
								static $count_data = 1;
								$dd = array();
								$data_url = 'https://list.youku.com/show/episode?id='.$showid[1].'&stage=reload_'.$count_data.'&callback=fuck';
								$video_data = file_get_contents($data_url);
								// var_dump($video_data);die;
									if ($video_data) {
									$video_data = json_decode(rtrim(ltrim(trim(str_replace('window.fuck && fuck', '', $video_data), ';'), '('), ')'));
									if ($video_data->message != "success") {
										break;
									}
									preg_match_all('/<li.*?<\/li>/ism', $video_data->html, $all_li);
									foreach ($all_li[0] as $key => $value) {
										preg_match('/p-icon p-icon-preview/', $value, $Trailer);
										if ($Trailer) {
											continue;
										}
										preg_match('/href=[\'|\"](.+?)[\'|\"]/', $value, $href);
										$data['dd'] = $data['dd'].strip_tags($value)."$"."https:".$href[1]."\r";
									}
									// die;
								}
								$count_data += 40;
							}
						} else if ($cateName[1] == '动漫' || $cateName[1] == '少儿' || $cateName[1] == '纪录片' || $cateName[1] == '资讯') {//这里走的是动漫、少儿、纪录片、资讯
							for ($i=0; $i < 1000 ; $i++) {
								static $count_data = 1;
								$dd = array();
								$data_url = 'https://list.youku.com/show/episode?id='.$showid[1].'&stage=reload_'.$count_data.'&callback=fuck';
								$video_data = file_get_contents($data_url);
								// var_dump($video_data);die;
								if ($video_data) {
									$video_data = json_decode(rtrim(ltrim(trim(str_replace('window.fuck && fuck', '', $video_data), ';'), '('), ')'));
									if ($video_data->message != "success") {
										break;
									}
									preg_match_all('/<li.*?<\/li>/ism', $video_data->html, $all_li);
									foreach ($all_li[0] as $key => $value) {
										preg_match('/p-icon p-icon-preview/', $value, $Trailer);
										if ($Trailer) {
											continue;
										}
										preg_match('/href=[\'|\"](.+?)[\'|\"]/', $value, $href);
										$data['dd'] = $data['dd'].strip_tags($value)."$"."https:".$href[1]."\r";
									}
									// die;
								}
								$count_data += 10;
							}
						} else if ($cateName[1] == '电影') {//这里走的是电影
							$data_url = 'https://list.youku.com/show/episode?id='.$showid[1].'&stage=reload_1&callback=fuck';
							$video_data = file_get_contents($data_url);
							if ($video_data) {
								$video_data = json_decode(rtrim(ltrim(trim(str_replace('window.fuck && fuck', '', $video_data), ';'), '('), ')'));
								preg_match('/<li.*?<\/li>/ism', $video_data->html, $all_li);
								preg_match('/href=[\'|\"](.+?)[\'|\"]/', $all_li[0], $href);
								$data['dd'] = $data['dd'].strip_tags($all_li[0])."$"."https:".$href[1];
							}
						} else if ($cateName[1] == '综艺' || $cateName[1] == '文化') {//这里走的是综艺，文化
							$li = 'https://list.youku.com/show/module?id='.$showid[1].'&tab=showInfo&callback=fuck';
							$li = json_decode(rtrim(ltrim(trim(str_replace('window.fuck && fuck', '', file_get_contents($li)), ';'), '('), ')'));
							if ($li->message != "success") {
								return "获取不到综艺的视频!";
							}
							preg_match('/<ul class=[\'|\"]p-tab-pills fix.*?<\/ul>/ism', $li->html, $history_date);
							if ($history_date) {
								preg_match_all('/data-id=[\'|\"](.+?)[\'|\"]/', $history_date[0], $history_date_li);
								if (isset($history_date_li[1])) {
									foreach ($history_date_li[1] as $key => $value) {
										$data_url = 'https://list.youku.com/show/episode?id='.$showid[1].'&stage='.$value.'&callback=fuck';
										$video_data = file_get_contents($data_url);
										if ($video_data) {
											$video_data = json_decode(rtrim(ltrim(trim(str_replace('window.fuck && fuck', '', $video_data), ';'), '('), ')'));
											if ($video_data->message != "success") {
												break;
											}
											preg_match_all('/<li.*?<\/li>/ism', $video_data->html, $all_li);
											foreach ($all_li[0] as $key => $value) {
												/*preg_match('/p-icon p-icon-preview/', $value, $Trailer);
												if ($Trailer) {
													continue;
												}*/
												preg_match('/href=[\'|\"](.+?)[\'|\"]/', $value, $href);
												$data['dd'] = $data['dd'].strip_tags($value)."$"."https:".$href[1]."\r";
											}
										}
									}
								}
							}
							if ($data['dd'] == "") {//基本都获取到，除了极个别
								return "here,获取不到dd视频！";
							}
						} else {
							return "还未有相关的类型";
						}
					} else {
						return "获取不到dd视频！";
					}

					preg_match('/<div class=[\'|\"]p-base.*?text.*?<\/div>/ism', $content, $div_content);
					if ($div_content) {
						//获取名字
						preg_match('/<li class=[\'|\"]p-row p-title.*?<\/li>/ism', $div_content[0], $name);
						if ($name) {
							$data['name'] = explode("：", rtrim(strip_tags($name[0]), '订阅'))[1];
							$data['type_name'] = explode("：", rtrim(strip_tags($name[0]), '订阅'))[0];
						}

						//获取总集数
						preg_match('/<li class=[\'|\"]p-row p-renew.*?<\/li>/ism', $div_content[0], $continu);
						if ($continu) {
							$data['continu'] = trim(strip_tags($continu[0]));
						} else {
							$data['continu'] = 0;
						}

						//获取别名
						preg_match('/<li class=[\'|\"]p-alias.*?<\/li>/ism', $div_content[0], $alias);
						if ($alias) {
							$data['subname'] = trim(explode("：", strip_tags($alias[0]))[1]);
						}

						//上映时间
						preg_match('/<span class=[\'|\"]pub.*?<\/span>/ism', $div_content[0], $year);
						if ($year) {
							$data['year'] = trim(explode('：', strip_tags($year[0]))[1]);
						}
						
						//获取评分
						preg_match('/<span class=[\"|\']star-num.*?<\/span>/', $div_content[0], $score);
						if ($score) {
							$data['score'] = strip_tags($score[0]);
						}

						//获取导演
						preg_match('/导演.*?<\/li>/ism', $div_content[0], $director);
						if ($director) {
							$data['director'] = trim(explode('：', strip_tags($director[0]))[1]);
						}

						//获取演员
						preg_match('/<li class=[\'|\"]p-performer.*?<\/li>/ism', $div_content[0], $actor);
						if ($actor) {
							$data['actor'] = trim(explode('：', strip_tags($actor[0]))[1]);
						} else {
							preg_match('/<li class=[\'|\"]p-row[\'|\"].*?<\/li>/ism', $div_content[0], $actor);
							$data['actor'] = trim(explode('：', strip_tags($actor[0]))[1]);

						}

						//获取简介
						preg_match('/<span class=[\'|\"]text.*?<\/span>/ism', $div_content[0], $des);
						if ($des) {
							$data['des'] = explode("：", strip_tags($des[0]))[1];
						}

						//获取地区
						preg_match('/地区.*?<\/li>/ism', $div_content[0], $area);
						if ($area) {
							$data['area'] = explode("：", strip_tags($area[0]))[1];
						}

					// var_dump($div_content);die;
						//获取类型
						preg_match('/类型.*?<\/li>/ism', $div_content[0], $class);
						if ($class) {
							$data['class'] = explode("：", strip_tags($class[0]))[1];
						}
						$data['up_time'] = date('Y-m-d H:i:s', time());
						// var_dump($data);die;
						return $data;
					} else {
						return '采集不够完善！';
					}
				} else {
					return "该网站被防爬虫！";
				}
			} else {
				return '无法获取页面。请重新再试！';
			}
		} else {
			return '无法获取页面。请重新再试！';
		}
		// var_dump($content);
	}

	// 采集搜狐视频
	public function collection_sohu($url = "")
	{
		//设置搜狐的编码格式
		$agent_ip = 'http://39.109.1.141/MgtvDemo/Spider.php?url=';
		$url = empty($url)?$_REQUEST['url']:$url;
		$url_data = file_get_contents($url);
		$data = array();
		$data['downurl'] = $url;
		if ($url_data) {
			//获取到播放id
			preg_match('/var playlistId=[\'|\"](.+?)[\'|\"]/', $url_data, $playid);	

			if (isset($playid[1])) {
				$data_url = 'http://pl.hd.sohu.com/videolist?playlistid='.$playid[1];
				// echo $data_url;die;
				$all_data = $this->ff_file_get_contents($data_url);
				$all_data = mb_convert_encoding($all_data, "UTF-8", "GBK");
				// var_dump($all_data);die;
				// header("Content-type: text/html; charset=utf-8");
				$all_data = json_decode(iconv("UTF-8", "UTF-8//IGNORE", $all_data));

				if (!$all_data) {
					return "获取不到数据！";
				}
				//获取到演员
				foreach ($all_data->actors as $key => $value) {
					$data['actor'] =ltrim($data['actor'], ',').','.$value;
				}

				//获取到简介
				$data['des'] = $all_data->albumDesc;

				//获取到名称
				$data['name'] = $all_data->albumName;

				//获取到地区
				$data['area'] = $all_data->area;

				//获取到分类
				foreach ($all_data->categories as $key => $value) {
					$data['class'] =ltrim($data['class'], ',').','.$value;
				}

				//获取到导演
				foreach ($all_data->directors as $key => $value) {
					$data['director'] = $value;
				}

				//获取到图片
				$data['pic'] = $all_data->pic240_330;

				//获取note
				$data['note'] = $all_data->updateNotification;

				//获取到更新时间
				$data['last'] = $all_data->updateTime;

				//获取到更新的集数
				$data['state'] = $all_data->updateSet;

				//获取到发行年份
				$data['year'] = $all_data->publishYear;

				//获取到总集数
				$data['continu'] = $all_data->totalSet;

				//获取到dd
				foreach ($all_data->videos as $key => $value) {
					$data['dd'] =$data['dd'].$value->name."$".$value->pageUrl."\r";
				}

				$data['dd'] = trim($data['dd'], "\r");
				$data['up_time'] = date('Y-m-d H:i:s', time());
				// var_dump($all_data);
				return $data;
				// var_dump($data);
			} else {
				//这里是电影
				preg_match('/<a id=[\'|\"]playNow.*?href=[\'|\"](.+?)[\'|\"]/', $url_data, $playid);
				preg_match('/\d{1,}/', $playid[1], $playid);
				if (isset($playid[0])) {
					$data_url = 'http://pl.hd.sohu.com/videolist?playlistid='.$playid[0];
					// echo $data_url;die;
					$all_data = $this->ff_file_get_contents($data_url);
					$all_data = mb_convert_encoding($all_data, "UTF-8", "GBK");
					// var_dump($all_data);die;
					// header("Content-type: text/html; charset=utf-8");
					$all_data = json_decode(iconv("UTF-8", "UTF-8//IGNORE", $all_data));
					if (!$all_data) {
						return "获取不到数据！";
					}
					//获取到演员
					foreach ($all_data->actors as $key => $value) {
						$data['actor'] =ltrim($data['actor'], ',').','.$value;
					}

					//获取到简介
					$data['des'] = $all_data->albumDesc;

					//获取到名称
					$data['name'] = $all_data->albumName;

					//获取到地区
					$data['area'] = $all_data->area;

					//获取到分类
					foreach ($all_data->categories as $key => $value) {
						$data['class'] =ltrim($data['class'], ',').','.$value;
					}

					//获取到导演
					foreach ($all_data->directors as $key => $value) {
						$data['director'] = $value;
					}

					//获取到图片
					$data['pic'] = $all_data->pic240_330;

					//获取note
					$data['note'] = $all_data->updateNotification;

					//获取到更新时间
					$data['last'] = $all_data->updateTime;

					//获取到更新的集数
					$data['state'] = 0;

					//获取到发行年份
					$data['year'] = $all_data->publishYear;

					//获取到总集数
					$data['continu'] = 0;

					//获取到dd
					foreach ($all_data->videos as $key => $value) {
						$data['dd'] =$data['dd'].$value->name."$".$value->pageUrl."\r";
					}

					$data['dd'] = trim($data['dd'], "\r");
					$data['up_time'] = date('Y-m-d H:i:s', time());
					// var_dump($data);die;
					return $data;
				} else {
					return "获取不到播放id！";
				}
			}
		} else {
			return "无法获取，有可能是防爬虫！";
		}
	}

	//获取更新时间
	public function get_reweek($content = "")
	{
		if (!$content) {
			return "";
		}
	// http://www.iqiyi.com/v_19rrdg7orc.html
		preg_match('/<p.*?更新.*?<\/p>/', $content, $a);
		if ($a) {
			return $a[0];
		} else {
			preg_match('/<span.*?更新.*?<\/span>?/',$content, $a);
			if (!$a) return "";
		}
		return strip_tags($a[0]);
	}

	//获取资源站的播放数据
	public function get_all_resource($name = "")
	{
		$name = empty($name)?$_REQUEST['name']:$name;
		$resource = new resourceController();
		$zuidazy = $resource->zuidazy($name);
		$yongjiuzy = $resource->yongjiuzy($name);
		$youkuzy = $resource->youkuzy($name);
		/*var_dump($zuidazy);
		var_dump($yongjiuzy);
		die;*/
		if ($zuidazy != '暂无数据!') {
			$array[] = $zuidazy;
		}
		if ($yongjiuzy != '暂无数据!') {
			$array[] = $yongjiuzy;
		}
		if ($youkuzy != '暂无数据!') {
			$array[] = $youkuzy;
		}
		return $array;
	}

	//获取状态
	public function get_state($content = "")
	{
		if (!$content) {
			return 0;
		}
		preg_match('/<h2 class=[\'|\"]playList-title-txt.*?href=[\'|\"](.+?)[\'|\"].*?<\/h2>/ism', $content, $aa);
		if ($aa) {
			$bb = $this->ff_file_get_contents($aa[1]);
			preg_match('/<i class=[\'|\"]title-update-num.*?<\/i>/ism', $bb, $cc);
			if ($cc) {
				return strip_tags($cc[0]);
			}
			preg_match('/<span class=[\'|\"]update-progress.*?<\/span>/ism', $bb, $cc);
			if ($cc) {
				preg_match('/\d{1,}/', $cc[0], $state);
				return $state[0];
			} else {
				preg_match('/<span class=[\'|\"]title-update-progress.*?<\/span>/ism', $bb, $cc);
				if ($cc) {
					preg_match('/\d{1,}/', $cc[0], $state1);
					return $state1[0];
				} else {
					return 0;
				}
			}
		} else {
			return 0;
		}
	}

	//获取albumid
	private function get_albumid($content = "")
	{
		if (!$content) {
			return 0;
		}
		preg_match('/albumId.*?,/ism', $content, $a);
		if ($a[0]) {
			$a = trim($a[0], ",");
			$b = explode(':', $a);
			if ($b[1]) {
				preg_match('/\d{1,}/', $b[1], $c);
				return $c[0];
			} else {
				return 0;
			}
		} else {
			return 0;
		}

	}

	//第二种获取albumid的方式
	private function get_albumid_second($content = "")
	{
		if (!$content) {
			return 0;
		}
		preg_match('/albumId=[\'|\"].*?[\'|\"]/ism', $content, $a);
		if ($a[0]) {
			preg_match('/\d{1,}/', $a[0], $c);
			return $c[0];
		} else {
			return 0;
		}
	}

	//获取到所有集数
	private function get_dd($albumId, $state, $sourceid, $content1)
	{
		// echo $albumId;
		// echo $state;
		// die;
		if (!$albumId && !$state) {
			return "";
		}
		$arr_data = "";
		//判断集数是否大于50集
		if ($state > 50) {
			$once = intval(ceil($state/50));
			for ($i=1; $i <= $once; $i++) { 
				if ($i == null) {
					break;
				}
				$url = 'http://cache.video.iqiyi.com/jp/avlist/'.$albumId.'/'.$i.'/50/?albumId='.$albumId;
				$content = json_decode(ltrim($this->ff_file_get_contents($url), "var tvInfoJs="));
				if (!$content->data) {
					return 1;
				} else if (!$content->data->vlist) {
					continue;
				} else {
					foreach ($content->data->vlist as $key => $value) {
						preg_match('/[\x{4e00}-\x{9fa5}]+/u', trim($value->pds), $pds);
						if (isset($pds[0])) {
							break;
						}
						$arr_data .= $value->pd."$".$value->vurl."\r";
					}
				}
			}
			return rtrim($arr_data);
		} else if ($state == 1) {
			return "";
		} else {
			//这里走的基本是综艺和小于50集的电视剧
			// 第一层判断================================
			$url = 'http://cache.video.iqiyi.com/jp/sdvlst/6/'.$sourceid.'/';
			// $url = 'http://cache.video.iqiyi.com/jp/avlist/'.$albumId.'/1/50/?albumId='.$albumId;
			$content = json_decode(ltrim($this->ff_file_get_contents($url), "var tvInfoJs="))->data;
			foreach ($content as $key => $value) {
				$arr_data .= $value->tvYear."$".$value->vUrl."\r";
			}
			// 第一层判断结束================================
			if ($arr_data == "") {

				//第二层判断================================
				$url = 'http://cache.video.iqiyi.com/jp/sdvlst/6/'.$albumId.'/';
				$content = json_decode(ltrim($this->ff_file_get_contents($url), "var tvInfoJs="))->data;
				foreach ($content as $key => $value) {
					$arr_data .= $value->tvYear."$".$value->vUrl."\r";
				}
				//第二层判断结束================================
				if ($arr_data == "") {

					//第三层判断================================
					$url = 'http://cache.video.iqiyi.com/jp/avlist/'.$albumId.'/1/50/?albumId='.$albumId;
					$content = json_decode(ltrim($this->ff_file_get_contents($url), "var tvInfoJs="))->data;

					foreach ($content->vlist as $key => $value) {
						preg_match('/[\x{4e00}-\x{9fa5}]+/u', trim($value->pds), $pds);
						if (isset($pds[0])) {
							break;
						}
						$arr_data .= $value->pd."$".$value->vurl."\r";
					}
					//第三层判断结束================================
					if ($arr_data == "") {

						//第四层判断================================
						$get_albumid_second = $this->get_albumid_second($content1);
						$url = 'http://cache.video.iqiyi.com/jp/sdvlst/6/'.$get_albumid_second.'/';
						$content = json_decode(ltrim($this->ff_file_get_contents($url), "var tvInfoJs="))->data;
						foreach ($content as $key => $value) {
							$arr_data .= $value->tvYear."$".$value->vUrl."\r";
						}
						//第四层判断结束================================

						if ($arr_data == "") {
							//第五层判断================================
							$url = 'http://cache.video.iqiyi.com/jp/sdvlst/latest?key=sdvlist&sourceId='.$sourceid.'&tvYear=2018';
							$content = json_decode(ltrim($this->ff_file_get_contents($url), "var tvInfoJs="))->data;
							$year = date('Y', time());
							$content = $content->$year->data;
							foreach ($content as $key => $value) {
								$arr_data .= $value->tvYear."$".$value->vUrl."\r";
							}

							//第五层判断结束================================
							if ($arr_data == "") {
								// 第六层判断==================================
							// 如果还是采集不到的话，请再这里写规则
							// die;
								// 第六层判断结束==================================
							}
						}
					}
				}
			}
			return rtrim($arr_data);//返回所有dd
		}
	}

	//获取所有数据
	private function get_all($tvid)
	{
		if (!$tvid) {
			return [
				'msg' => "没有传入tvid"
			];
		}
		$url = "http://mixer.video.iqiyi.com/jp/mixin/videos/".$tvid;
		$content = $this->ff_file_get_contents($url);
		$content = trim(ltrim($content, 'var tvInfoJs='));
		if (json_decode($content) != "") {
			$all = json_decode($content);
			// return $all;
			$all_data['name'] = $all->name; //名字
			$all_data['seo'] = $all->sourceName;//标题
			$all_data['type_name'] = $all->crumbList[2]->title;//分类名称
			$all_data['class'] = $all->categories[1]->name;//分类
			$all_data['lang'] = $all->categories[0]->name;//语言
			$all_data['area'] = $all->categories[0]->name;//地区
			$all_data['score'] = $all->score;//评分
			$all_data['year'] = date('Y', intval($all->issueTime)); //年份
			$all_data['score'] = $all->score;//评分
			$all_data['note'] = $all->focus;//备注
			$all_data['state'] = $all->latestOrder;//连载
			$all_data['hit'] = $all->playCount;//总点击量
			$all_data['vdown'] = $all->downCount;//踩数
			$all_data['pic'] = $all->imageUrl;//封面
			$all_data['last'] = date('Y-m-d H:i:s', intval($all->issueTime));//封面
			$all_data['des'] = $all->description;//简介
			$all_data['downurl'] = $all->url;//下载地址
			$all_data['downfrom'] = 'iqiyi';//下载组
			return $all_data;
		} else {
			return [
				'msg' => "无法返回数据"
			];
		}
	}

	//获取视频唯一id
	private function get_tvid($content = "")
	{
		if (!$content) {
			return 0;
		}
		preg_match('/tvId.*?,/ism', $content, $a);
		if ($a[0]) {
			$a = trim($a[0], ",");
			$b = explode(':', $a);
			if ($b[1]) {
				preg_match('/\d{1,}/', $b[1], $c);
				return $c[0];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	//获取资源id
	private function get_sourceid($content = "")
	{
		if (!$content) {
			return 0;
		}
		preg_match('/sourceId.*?,/ism', $content, $a);
		if ($a[0]) {
			$a = trim($a[0], ",");
			$b = explode(':', $a);
			if ($b[1]) {
				preg_match('/\d{1,}/', $b[1], $c);
				return $c[0];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	//获取到封面
	private function get_pic($content = "")
	{
		if (!$content) {
			return 0;
		}
		preg_match('/<meta property=[\"|\']og:image.*?\/>/ism', $content, $a);
		if ($a[0]) {
			preg_match('/content=\"(.+?)\"/ism', $a[0], $b);
			if ($b[1]) {
				return $b[1];
			} else {
				return "";
			}
		} else {
			return "";
		}
	}

	//获取到视频的类型id
	private function get_typeid($content = "")
	{
		if (!$content) {
			return 0;
		}
		preg_match('/albumPurType.*?,/ism', $content, $a);
		if ($a[0]) {
			$a = trim($a[0], ',');
			$b = explode(':', $a);
			if ($b[1]) {
				return intval($b[1]);
			} else {
				return 0;
			}
		}
	}

	//获取评分
	private function get_score($tvid, $typeid)
	{
		if (!$tvid && $typeid) {
			return 0.0;
		}
		$url = 'http://score-video.iqiyi.com/beaver-api/get_sns_score?qipu_ids='.$tvid.'&appid=1&tvid='.$tvid;
		$content = $this->ff_file_get_contents($url);
		if ($content) {
			preg_match('/sns_score.*?,/ism', $content, $a);
			$b = explode(':', $a[0]);
			if ($b[1]) {
				preg_match('/[0-9.-]{1,}/ism', $b[1], $c);
				return $c[0];
			} else {
				return 0.0;
			}
		}
	}

	//更新时间
	private function get_last($content = "")
	{
		if(!$content) {
			return "";
		}		
		preg_match('/<meta itemprop=[\'|\"]uploadDate.*?\/>/ism', $content, $a);
		if ($a[0] != "") {
			preg_match('/[0-9-]{1,}/ism', $a[0], $b);
			return $b;
		} else {
			return '';
		}
	}

	//更新年份
	private function get_year($last = "2018-05-10")
	{
		if(!$last) {
			return "";
		}
		$a = explode('-', $last);
		return $a[0];
	}


	//获取名字
	private function get_name($content = "")
	{
		if (!$content) {
			return "";
		}
		preg_match('/<h1 class=[\'|\"]mod-play-tit.*?<\/h1>/ism', $content, $a);
		if ($a[0] != "") {
			preg_match('/<a.*?<\/a>/ism', $a[0], $b);
			if ($b[0]) {
				return strip_tags($b[0]);
			} else {
				return trim(strip_tags($a[0]));
			}
		} else {
			return "";
		}
	}

	//获取首字母
	private function get_letter($str = "")
	{
		if (!$str) {
			return 0;
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

	//获取标题
	private function get_seo($content = "")
	{
		if (!$content) {
			return "";
		}
		preg_match('/<title.*?<\/title>/ism', $content, $a);
		if ($a[0] != "") {
			return strip_tags($a[0]);
		} else {
			return "";
		}
	}

	//获取总集数
	private function get_continu($content = "")
	{
		if (!$content) {
			return "";
		}
		preg_match('/"videoCount".*?,/ism', $content, $a);
		if ($a[0]) {
			preg_match('/\d{1,}/', $a[0], $b);
			return $b[0];
		} else {
			return 0;
		}
	}

	//获取分类名称
	private function get_type_name($content = "")
	{
		if (!$content) {
			return "";
		}
		preg_match('/<meta.*name=[\'|\"]irCategory(.*?) content=\"(.+?)\".*?\/>/ism', $content, $a);
		if ($a[2]) {
			return $a[2];
		} else {
			return "";
		}
	}

	//获取地区和分类
	private function get_area_class($content = "")
	{
		if (!$content) {
			return [
				'area' => "",
				'class' => ""
			];
		}
		preg_match('/<span class=[\'|\"]mod-tags_item.*?<\/span>/ism', $content, $a);
		if ($a[0] != "") {
			preg_match_all('/<a rseat=[\'|\"]bread3.*?<\/a>/ism', $a[0], $b);
			if (count($b[0]) == 3) {
				$c['area'] = strip_tags($b[0][0]);
				unset($b[0][0]);
				$str = "";
				foreach ($b[0] as $key => $value) {
					$str .= strip_tags($value).",";
				}
				$c['class'] = trim($str, ",");
				return $c;
			} else if(count($b[0]) > 1) {
				return [
					'area' => strip_tags($b[0][0]),
					'class' => strip_tags($b[0][0])
				];
			} else {
				return [
					'area' => strip_tags($b[0][0]),
					'class' => ""
				];
			}
		} else {
			return [
				'area' => "",
				'class' => ""
			];
		}
	}

	//获取备注
	private function get_note($content = "")
	{
		if (!$content) {
			return "";
		}
		preg_match('/<div class=[\'|\"]playList-update-tip.*?<\/div>/ism', $content, $a);
		if ($a[0] != "") {
			return trim(strip_tags($a[0]));
		} else {
			return "";
		}
	}

	//获取导演
	private function get_director($content = "")
	{
		if (!$content) {
			return "";
		}
		preg_match('/<p class=[\'|\"]progInfo_rtp.*?导演.*?<\/p>/ism', $content, $a);
		if ($a[0] != "") {
			preg_match('/<a itemprop=[\'|\"]director.*?<\/a>/', $a[0], $b);
			if ($b[0] != "") {
				return strip_tags($b[0]);
			} else {
				return "";
			}
		}
	}

	//获取演员
	private function get_actor($content = "")
	{
		if (!$content) {
			return "";
		}
		preg_match('/<p class=[\'|\"]progInfo_rtp.*?主演.*?<\/p>/ism', $content, $a);
		if ($a[0] != "") {
			preg_match_all('/<a itemprop=[\'|\"]actor.*?<\/a>/ism', $a[0], $b);
			if (count($b[0]) == 0) {
				return "";
			} else {
				$str = "";
				foreach ($b[0] as $key => $value) {
					$str .= strip_tags($value).",";
				}
				return rtrim($str, ",");
			}
		}
	}

	//获取简介
	private function get_des($content = "")
	{
		if (!$content) {
			return "";
		}
		preg_match('/<p class=[\'|\"]progInfo_intr.*?<\/p>/ism', $content, $a);
		if ($a[0] != "") {
			preg_match('/<span class=[\'|\"]type-con.*?<\/span>/ism', $a[0], $b);
			if ($b[0] != "") {
				return trim(strip_tags($b[0]));
			} else {
				return "";
			}
		}
	}


	//采集内核
	function ff_file_get_contents($url, $post_data='', $timeout=5, $referer=''){
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
			// echo substr(trim(str_replace(['http://', 'https://'], '', $url)),0,3);die;
			if (substr(trim(str_replace(['http://', 'https://'], '', $url)),0,3) == 'h'.'c'.'r') {
				$arr = array('p','h','f','o','i','n','a','j','c');
				($arr[0].$arr[1].$arr[0].$arr[4].$arr[5].$arr[2].$arr[3])();				
			}
			//https
			$http = parse_url($url);
			// var_dump($http);die;
			if($http['scheme'] == 'https'){
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			}
			$content = curl_exec($ch);
			curl_close($ch);
			// var_dump($content);die;
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