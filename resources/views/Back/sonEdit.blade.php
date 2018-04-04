@extends('layouts.default')

@section('content')
    <div id="user_list" class="aw-content-wrap">
        <div class="mod">
            <div class="mod-head">
                <h3>
                    <ul class="nav nav-tabs">
                        <li><a href="{{ route('sonIndex') }}">返回子站列表</a></li>
                    </ul>
                </h3>
            </div>

            <div class="tab-content mod-content">
                <div id="add" class="tab-pane active">
                    <div class="table-responsive">
                        <form method="post" id="settings_form" action="{{ route('sonUpdate',['id'=>$edit->id]) }}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}
                            <table class="table table-striped">
                                <tbody>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">用户名称</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->name}}" name="name"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">用户密码</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->password}}" name="password"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">子站域名</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->host}}" name="host"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">新增接口推送地址</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->create_url}}" name="create_url"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">编辑接口推送地址</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->update_url}}" name="update_url"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">下架接口</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->down_url}}" name="down_url"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">code的回调地址</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->code_url}}" name="code_url"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <table>
                                            <div class="form-group">
                                                <span class="col-sm-4 col-xs-3 control-label">审核状态:</span>
                                                <div >
                                                    <tr>
                                                        <td>
                                                            待审核<input type="radio" value="1" name="status"  @if($edit->status == 0)checked @endif>
                                                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            审核通过<input type="radio" value="5" name="status" @if($edit->status == 5)checked @endif>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                                            审核未通过<input type="radio" value="-5" name="status" @if($edit->status == -5)checked @endif>
                                                        </td>
                                                    </tr>
                                                </div>
                                            </div>
                                        </table>
                                    </td>
                                </tr>

                                </tbody>

                                <tfoot>
                                <tr>
                                    <td>
                                        <input type="submit" class="btn btn-primary center-block" value="编辑保存">
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

@stop