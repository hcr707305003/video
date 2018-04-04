<?php

use Illuminate\Support\Facades\Route;

?>
@extends('layouts.default')

@section('content')
    <div id="user_list" class="aw-content-wrap">
        <div class="mod">
            <div class="mod-head">
                <h3>
                    <ul class="nav nav-tabs">
                        <li>
                            <a data-toggle="tab" href="#listss">子站列表</a>
                        </li>
                        <li>
                            <a href="{{route('sonAddList')}}" >添加子站</a>
                        </li>
                    </ul>
                </h3>
            </div>
            <div class="box-body tab-content">
                <div id="listss" class="tab-pane active">
                    <form class="well form-search" method="get" id="form_id" action="{{route('sonIndex')}}">
                        子站名称:
                        <input type="text"  name="name" style="width: 120px;height: 30px;border-radius:5px ;" value=""
                               placeholder="请输入子站名称" class="border-left-right">
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        审核：
                        <select name="status" style="width: 120px;height: 30px;border-radius:5px ;" class="border-left-right" >
                            <option value="">请选择</option>
                            <option value="1">待审核</option>
                            <option value="5">通过的审核</option>
                            <option value="-5">未通过的审核</option>
                        </select>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;查看时间：&nbsp;
                        <input type="datetime-local" id="" name=""  value=""
                               style="width: 180px;height: 30px;border-radius:5px ;" autocomplete="off" class="border-left-right">&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="datetime-local" id="" name="" value=""
                               style="width: 180px;height: 30px;border-radius:5px ;" autocomplete="off" class="border-left-right"> &nbsp;
                        <input type="hidden" id="tro_id" name="action_name">

                        <input type="button" class="btn btn-primary" id="search_id" value="搜索">
                        <a class="btn btn-danger" href="{{route('index')}}">清空</a>
                    </form>
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>用户名称</th>
                            <th>用户密码</th>
                            <th>子站域名</th>
                            <th>code</th>
                            <th>cide过期时间</th>
                            <th>token</th>
                            <th>token过期时间</th>
                            <th>新增接口推送url</th>
                            <th>编辑接口推送url</th>
                            <th>下架接口</th>
                            <th>code的回调url</th>
                            <th>审核状态</th>
                            <th width="150">操作</th>
                            <th width="230">审核操作</th>
                        </tr>
                        @foreach($data as $vo)
                            <tr>
                                <td>{{$vo->id}}</td>
                                <td>{{$vo->name}}</td>
                                <td>{{$vo->password}}</td>
                                <td>{{$vo->host}}</td>
                                <td>{{$vo->code}}</td>
                                <td>{{$vo->codetime}}</td>
                                <td>{{$vo->access_token}}</td>
                                <td>{{$vo->tokentime}}</td>
                                <td>{{$vo->create_url}}</td>
                                <td>{{$vo->update_url}}</td>
                                <td>{{$vo->down_url}}</td>
                                <td>{{$vo->code_url}}</td>
                                <td>{{$status[$vo->status]}}</td>
                                <td>
                                    <a href="{{ route('sonEdit',$vo->id )}}" class="btn btn-default" title="编辑"><span class="fa fa-edit"></span> 编辑</a>
                                    <a class="btn btn-danger" href="{{ route('sonDelete',$vo->id) }}" title="删除" onclick="return confirm('是否删除？');"><span class="fa fa-trash-o"></span> 删除</a>
                                </td>
                                <td>
                                    <a href="{{route('sonStatus',$vo->id)}}" class="btn btn-success" title="审核通过"><span class="fa fa-edit"></span> 审核通过</a>
                                    <a href="{{route('sonStatus',$vo->id)}}" class="btn btn-warning" title="审核不通过"><span class="fa fa-trash-o"></span> 审核不通过</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                {{ $data->links() }}
            </div>

        </div>
    </div>

    <script>
        document.getElementById('search_id').onclick = function () {
            document.getElementById('tro_id').value = 'search';
            document.getElementById('form_id').setAttribute('action', "{{route('sonIndex')}}");
            document.getElementById('form_id').submit();
        };
    </script>
@stop