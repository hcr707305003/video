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

//Route::get('/', 'Auth\\LoginController@login');

Route::group([
    'prefix' => 'back',
    'namespace' => 'Back',
    'middleware' => 'auth',
], function () {
        #主站######列表##########################################################################################
        //列表--控制面板 //搜索
        Route::get('index', 'AdminController@index')->name('index');
        //审核通过
        Route::get('list', 'AdminController@list')->name('vodList');
        //修改页展示数据
        Route::get('edit/{id}', 'AdminController@edit')->name('vodEdit');
        //修改数据
        Route::post('update/{id}', 'AdminController@update')->name('vodUpdate');
        //删除
        Route::get('delete/{id}', 'AdminController@delete')->name('vodDel');
        //回复数据
        Route::get('recover/{id}', 'AdminController@recover')->name('vodRecover');
        Route::get('status/{id}', 'AdminController@status')->name('vodStatus');
        //上下架
        Route::get('above/{id}', 'AdminController@above')->name('vodAbove');
        Route::get('below/{id}', 'AdminController@below')->name('vodBelow');
        #子站######列表##########################################################################################
        //子站列表
        Route::get('sonindex','SonController@sonIndex')->name('sonIndex');
        //添加列表
        Route::get('sonaddlist','SonController@sonAddList')->name('sonAddList');
        //数据入库
        Route::post('sonadd','SonController@sonAdd')->name('sonAdd');
        //编辑
        Route::get('sonedit/{id}','SonController@sonEdit')->name('sonEdit');
        //编辑入库
        Route::post('sonupdate/{id}','SonController@sonUpdate')->name('sonUpdate');
        //删除
        Route::get('sondelete/{id}','SonController@sonDelete')->name('sonDelete');
        Route::get('sonstatus/{id}', 'SonController@sonStatus')->name('sonStatus');

        #任务######列表##########################################################################################
        Route::get('taskindex','TaskController@taskIndex')->name('taskIndex');
        //进行任务
        Route::get('tasklist','TaskController@taskList')->name('taskList');
        //任务状态
        Route::get('taskedit/{id}','TaskController@taskEdit')->name('taskEdit');
        //完成删除
        Route::get('taskdelete/{id}','TaskController@taskDelete')->name('taskDelete');
        //新任务
        Route::get('tasknew/{id}','TaskController@taskNew')->name('taskNew');
        //任务点击采集
        Route::get('taskgather/{id}','TaskController@taskGather')->name('taskGather');
        //任务采集到的视屏展示
        Route::get('taskvod','TaskController@taskVod')->name('taskVod');
        //发送采集审核数据信息.
        Route::get('taskcheck/{id}','TaskController@taskCheck')->name('taskCheck');
});

Auth::routes();
Route::get('/', 'HomeController@index')->name('home');


//测试用curl用
Route::group([
    'prefix' => 'code/api',
    'namespace' => 'Code\api',
],function(){
//    Route::post('crl','AddController@ceshi')->name('ceShi');
    Route::post('test','AddController@httpAdd')->name('test'); //审核通过接口
    Route::post('edit','AddController@editApi')->name('edit'); //编辑接口
    Route::post('taskone','AddController@taskApi')->name('taskone'); //任务接口
});
Route::group([
    'prefix' => 'caiji',
    'namespace' => 'Caiji',
],function(){
    Route::get('/{page}','CollectionCotontroller@insert_into')->name('/{page}'); //恢复视频源
    Route::post('jieApi','CollectionCotontroller@searchNameAllDate')->name('jieApi'); //根据数据名称采集相关的数据
    Route::get('delDate/id/{id}','CollectionCotontroller@delDate')->name('delDate'); //删除视频源
    Route::get('recoveryData/id/{id}/downurl/{downurl}','CollectionCotontroller@recoveryData')->name('recoveryData'); //恢复视频源
});

// 263@http://33uudy.com/?m=vod-detail-id-16711.html