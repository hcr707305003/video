<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//自动采集2345影视
Route::get('tv_auto/{type?}', 'Caiji\TtffController@tv_auto_apis');//采集所有电视剧
Route::get('film_auto', 'Caiji\TtffController@film_auto_apis');//采集所有电影
Route::get('variety_auto/{type?}', 'Caiji\TtffController@variety_auto_apis');//采集所有综艺
Route::get('comic_auto/{type?}', 'Caiji\TtffController@comic_auto_apis');//采集所有动漫


Route::get('film_year', 'Caiji\firstCollectionController@film_year');
Route::get('tv_year', 'Caiji\firstCollectionController@tv_year');
Route::get('comic_year', 'Caiji\firstCollectionController@comic_year');


// Route::get('/{url}', 'Caiji\CaijiController@apis');
Route::get('zuidazy', 'Caiji\resourceController@zuidazy');
Route::get('yongjiuzy', 'Caiji\resourceController@yongjiuzy');
Route::get('mgtv', 'Caiji\mgtvController@collection_mgtv');
Route::get('qqtv', 'Caiji\qqtvController@qqtv');
Route::get('sohu', 'Caiji\sohuController@sohu');
Route::get('youku', 'Caiji\youkuController@youku');


Route::get('iqiyi', 'Caiji\IqiyiController@iqiyi');//自动采集爱奇艺视频
Route::get('iqiyi/collection/content', 'Caiji\ContentController@collection_content');//指定采集
// Route::get('collection/resource', 'Caiji\ContentController@collection_resource');//视频站和资源站混合资源
Route::get('qq/collection/content', 'Caiji\ContentController@collection_qqtv');//指定腾讯视频采集
Route::get('mgtv/collection/content', 'Caiji\ContentController@collection_mgtv');//指定芒果视频采集
Route::get('sohu/collection/content', 'Caiji\ContentController@collection_sohu');//指定搜狐视频采集
Route::get('youku/collection/content', 'Caiji\ContentController@collection_youku');//指定优酷视频采集
Route::get('iqiyi/auto/collection', 'Caiji\ContentController@auto_Collection');//自动采集
Route::get('match/resource', 'Caiji\ContentController@matching_resource_and_video_Agreement');//匹配資源站是否一致

//以下是资源站请求
Route::get('zuidazy', 'Caiji\resourceController@zuidazy');//最大资源网采集
Route::get('yongjiuzy', 'Caiji\resourceController@yongjiuzy');//永久资源网站
Route::get('youkuzy', 'Caiji\resourceController@youkuzy');//01资源网


// Route::get('iqiyi/auto/update_time', 'Caiji\ContentController@get_update');
Route::get('resource', 'Caiji\ContentController@get_all_resource');//获取的入库资源站数据
Route::get('find_name', 'Caiji\ContentController@get_find_name');//根据名称查询入库

// Route::get('a', 'Caiji\IqiyiController@iqiyi');
Route::get('{maxpage?}', 'Caiji\CaijiController@auto_apis');
Route::group([
    'prefix' => 'caiji',
    'namespace' => 'Caiji',
],function(){
   Route::get('/auto','CaijiController@apis')->name('/auto');
   Route::post('/auto','CaijiController@apis')->name('/auto');
   // Route::get('a','CaijiController@auto_apis')->name('a');
});