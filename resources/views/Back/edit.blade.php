@extends('layouts.default')

@section('content')
    <div id="user_list" class="aw-content-wrap">
        <div class="box">
                <div class="mod-head">
                    <h3>
                        <ul class="nav nav-tabs">
                            <li><a href="{{ route('index') }}">返回视频列表</a></li>
                        </ul>
                    </h3>
                </div>

            <div class="box-body tab-content">
                    <div class="table-responsive">
                        <form method="post" id="settings_form" action="{{ route('vodUpdate',['id'=>$edit->id]) }}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('POST') }}
                            <table class="table table-striped">
                                <tbody>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">视频名称</span>
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
                                            <span class="col-sm-4 col-xs-3 control-label">副标题</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->subname}}" name="subname"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">英文名称</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->enname}}" name="enname"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">首字母</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->letter}}" name="letter"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">分类名称</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->type_name}}" name="type_name"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">封面</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <label for="pic"><img src="{{$edit->pic}}" alt="" style="width: 50px;height: 50px"></label>
                                                <input type="file" value="{{$edit->pic}}" name="pic"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">语言</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->lang}}" name="lang"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">地区</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->area}}" name="area"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">评分</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->score}}" name="score"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">年份</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->year}}" name="year"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">更新时间</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->last}}" name="last"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">连载</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->state}}" name="state"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">备注</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->note}}" name="note"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">演员</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->actor}}" name="actor"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">导演</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->director}}" name="director"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">过滤文字</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->playfrom}}" name="playfrom"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">播放url</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->dd}}" name="dd" class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">简介:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->des}}" name="des"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">下载组:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->downfrom}}" name="downfrom"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">下载地址:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->downurl}}" name="downurl"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">推送标志:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                <input type="text" value="{{$edit->tuisong}}" name="tuisong"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <span class="col-sm-4 col-xs-3 control-label">审核状态:</span>
                                            <div class="col-sm-5 col-xs-8">
                                                   待审核<input type="radio" value="0" name="status"  @if($edit->vod_status == 0)checked @endif>
                                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                   审核通过<input type="radio" value="5" name="status" @if($edit->vod_status == 5)checked @endif>
                                                   &nbsp;&nbsp;&nbsp;&nbsp;
                                                   审核未通过<input type="radio" value="-5" name="status" @if($edit->vod_status == -5)checked @endif>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                </tbody>

                                <tfoot>
                                    <tr class="form-group">
                                        <td><input type="submit" class="btn btn-primary center-block" value="编辑保存"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                    </div>
                </div>
        </div>
    </div>
@stop