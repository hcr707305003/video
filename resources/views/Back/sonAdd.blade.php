<?php

use Illuminate\Support\Facades\Route
?>
@extends('layouts.default')

@section('content')
    <div id="user_list" class="aw-content-wrap">
        <div class="mod">
            <div class="mod-head">
                <h3>
                    <ul class="nav nav-tabs">
                       <li><a  href="{{ route('sonIndex')}} ">返回列表</a></li>
                        <li class="active"><a data-toggle="tab" href="#add">添加用户</a></li>
                    </ul>
                </h3>
            </div>

            <div class="tab-content mod-content">
                <div id="add" class="tab-pane active">
                    <div class="table-responsive">
                        <form method="post" id="settings_form"
                              action="{{route('sonAdd')}}">
                            {{csrf_field()}}
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">用户名称:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{old('name', '')}}" name="name"
                                                       id="input-name" class="form-control">
                                                @if($errors->has('name'))
                                                    <label for="input-name"
                                                           class="text-danger">{{$errors->first('name')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">用户密码:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{old('password', '')}}" name="password"
                                                       id="input-password" class="form-control">
                                                @if($errors->has('password'))
                                                    <label for="input-password"
                                                           class="text-danger">{{$errors->first('password')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">子站域名:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{old('host','')}}" name="host"
                                                       id="input-host" class="form-control">
                                                @if($errors->has('host'))
                                                    <label for="input-host"
                                                           class="text-danger">{{$errors->first('host')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">新增接口推送地址:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{old('create_url','')}}" name="create_url"
                                                       id="input-create_url" class="form-control">
                                                @if($errors->has('create_url'))
                                                    <label for="input-create_url"
                                                           class="text-danger">{{$errors->first('create_url')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">编辑接口推送地址:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{old('update_url','')}}" name="update_url"
                                                       id="input-update_url"
                                                       class="form-control">
                                                @if($errors->has('update_url'))
                                                    <label for="input-update_url"
                                                           class="text-danger">{{$errors->first('update_url')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">下架接口:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{old('down_url','')}}" name="down_url"
                                                       id="input-down_url" class="form-control">
                                                @if($errors->has('down_url'))
                                                    <label for="input-down_url"
                                                           class="text-danger">{{$errors->first('down_url')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">code的回调地址:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{old('code_url','')}}" name="code_url"
                                                       id="input-code_url" class="form-control">
                                                @if($errors->has('code_url'))
                                                    <label for="input-code_url"
                                                           class="text-danger">{{$errors->first('code_url')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">审核状态:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                待审核<input type="radio" value="0" name="status">
                                                &nbsp;&nbsp;审核通过<input type="radio" value="5" name="status">
                                                &nbsp;&nbsp;审核不通过<input type="radio" value="-5" name="status">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input type="submit" class="btn btn-primary center-block" value="添加数据">
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop