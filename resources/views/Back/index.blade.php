@extends('layouts.default')

@section('content')
    <div id="user_list" class="aw-content-wrap">
        <div class="mod">
            <div class="mod-head">
                <h3>
                    <ul class="nav nav-tabs">
                        <li>
                            <a data-toggle="tab" href="#listss">视频待审核列表</a>
                        </li>
                        <li>
                            <a href="{{ route('vodList') }}">视频已审核列表</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#trash">视频回收站</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#below">下架的视频列表</a>
                        </li>
                    </ul>
                </h3>
            </div>
            <div class="box-body tab-content">
                <div id="listss" class="tab-pane active">
                    <form class="well form-search" method="get" id="form_id" action="{{route('index')}}">
                        视频名称:
                        <input type="text" id="" name="name" style="width: 120px;height: 30px;border-radius:5px ;" class="border-left-right" value=""
                               placeholder="请输入视频名称">
                        主演:
                        <input type="text" id="" name="actor" style="width: 120px;height: 30px;border-radius:5px ;" class="border-left-right" value=""
                               placeholder="请输入主要演员">
                        &nbsp;
                        类别：
                        <input type="text" id="" name="type_name" style="width: 120px;height: 30px;border-radius:5px ;" class="border-left-right" value=""
                               placeholder="请输入视频类型">

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;查看时间：&nbsp;
                        <input type="datetime-local" id="" name="" style="width: 120px;height: 30px;border-radius:5px ;" class="border-left-right" autocomplete="off">&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="datetime-local" id="" name="" style="width: 120px;height: 30px;border-radius:5px ;" class="border-left-right" autocomplete="off"> &nbsp;

                        <input type="hidden" id="tro_id" name="action_name">
                        <input type="button" class="btn btn-primary" id="search_id" value="搜索">
                        <a class="btn btn-danger" href="{{route('index')}}">清空</a>
                    </form>
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>名称</th>
                            <th >副标题</th>
                            <th  width="80">英文名称</th>
                            <th>首字母</th>
                            <th  width="80">分类名称</th>
                            <th>封面</th>
                            <th>语言</th>
                            <th>地区</th>
                            <th>评分</th>
                            <th>年份</th>
                            <th  width="80">更新时间</th>
                            <th>连载</th>
                            <th>备注</th>
                            <th>演员</th>
                            <th>导演</th>
                            <th>标识</th>
                            <th  width="80">播放url</th>
                            <th>简介</th>
                            <th>下载组</th>
                            <th width="80">下载地址</th>
                            <th  width="80">推送标志</th>
                            <th>上下架</th>
                            <th width="80">审核状态</th>
                            <th width="150">操作</th>
                            <th width="190">审核操作</th>
                        </tr>
                        @foreach($list as $vo)
                            <tr>
                                <td>{{$vo->id}}</td>
                                <td>{{$vo->name}}</td>
                                <td>{{$vo->subname}}</td>
                                <td>{{$vo->enname}}</td>
                                <td>{{$vo->letter}}</td>
                                <td>{{$vo->type_name}}</td>
                                <td>
                                    <img class="mou" src="{{$vo->pic}}" alt="" id="img" ALIGN="MIDDLE" BORDER="0"  onMouseMove="move()" onMouseOut="out()"  style="width: 50px;height: 50px;cursor:pointer;">
                                </td>
                                <td>{{$vo->lang}}</td>
                                <td>{{$vo->area}}</td>
                                <td>{{$vo->score}}</td>
                                <td>{{$vo->year}}</td>
                                <td>{{$vo->last}}</td>
                                <td>{{$vo->state}}</td>
                                <td>{{$vo->note}}</td>
                                <td>{{$vo->actor}}</td>
                                <td>{{$vo->director}}</td>
                                <td>{{$vo->playfrom}}</td>
                                <td >{{$vo->dd}}</td>
                                <td>{{$vo->des}}</td>
                                <td>{{$vo->downfrom}}</td>
                                <td>{{$vo->downurl}}</td>
                                <td>{{$vo->tuisong}}</td>
                                <td>{{$show[$vo->status]}}</td>
                                <td>{{$status[$vo->vod_status]}}</td>

                                <td>
                                    <a href="{{ route('vodEdit',$vo->id )}}" class="btn btn-warning" title="编辑"><span class="fa fa-edit"></span> 编辑</a>
                                @if($vo->status == 0 || $vo->status == -5)
                                        <a href="{{ route('vodAbove',$vo->id )}}" class="btn btn-success" title="上架"><span class="fa fa-edit"></span> 上架</a>
                                @elseif($vo->status == 5)
                                    <a href="{{ route('vodBelow',$vo->id )}}" class="btn btn-danger" title="下架" onclick="return confirm('是否下架？');"><span class="fa fa-edit"></span> 下架</a>
                                @endif
                                </td>

                                <td>
                                    <a href="{{route('vodStatus',$vo->id)}}" class="btn btn-success" title="审核通过"><span class="fa fa-edit"></span> 审核通过</a>
                                    <a class="btn btn-danger" href="{{ route('vodDel',$vo->id) }}" title="删除" onclick="return confirm('是否删除？');"><span class="fa fa-trash-o"></span> 删除</a>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $list->links() }}
                </div><!-- /.box-body -->
                <div id="trash" class="tab-pane" >
                    <table class="table table-bordered" >
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>名称</th>
                            <th>副标题</th>
                            <th>英文名称</th>
                            <th>首字母</th>
                            <th>分类名称</th>
                            <th>封面</th>
                            <th>语言</th>
                            <th>地区</th>
                            <th>评分</th>
                            <th>年份</th>
                            <th>更新时间</th>
                            <th>连载</th>
                            <th>备注</th>
                            <th>演员</th>
                            <th>导演</th>
                            <th>过滤文字</th>
                            <th>播放url</th>
                            <th>简介</th>
                            <th>下载组</th>
                            <th>下载地址</th>
                            <th>推送标志</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        @foreach($trash as $vo)
                            <tr>
                                <td>{{$vo->id}}</td>
                                <td>{{$vo->name}}</td>
                                <td>{{$vo->subname}}</td>
                                <td>{{$vo->enname}}</td>
                                <td>{{$vo->letter}}</td>
                                <td>{{$vo->type_name}}</td>
                                <td >
                                    <img src="{{$vo->pic}}" alt="" style="width: 50px;height: 50px">
                                </td>
                                <td>{{$vo->lang}}</td>
                                <td>{{$vo->area}}</td>
                                <td>{{$vo->score}}</td>
                                <td>{{$vo->year}}</td>
                                <td>{{$vo->last}}</td>
                                <td>{{$vo->state}}</td>
                                <td>{{$vo->note}}</td>
                                <td>{{$vo->actor}}</td>
                                <td>{{$vo->director}}</td>
                                <td>{{$vo->playfrom}}</td>
                                <td>{{$vo->dd}}</td>
                                <td>{{$vo->des}}</td>
                                <td>{{$vo->downfrom}}</td>
                                <td>{{$vo->downurl}}</td>
                                <td>{{$vo->tuisong}}</td>
                                <td>{{$status[$vo->vod_status]}}</td>
                                <td>
                                    <a class="btn btn-success" href="{{ route('vodRecover',$vo->id) }}"
                                       title="恢复"
                                       onclick="return confirm('是否恢复？');"><span class="fa fa-trash-o"></span> 恢复</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                    {{ $trash->links() }}
                </div>

                <div id="below" class="tab-pane">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                        <th>ID</th>
                        <th>名称</th>
                        <th>副标题</th>
                        <th>英文名称</th>
                        <th>首字母</th>
                        <th>分类名称</th>
                        <th>封面</th>
                        <th>语言</th>
                        <th>地区</th>
                        <th>评分</th>
                        <th>年份</th>
                        <th>更新时间</th>
                        <th>连载</th>
                        <th>备注</th>
                        <th>演员</th>
                        <th>导演</th>
                        <th>过滤文字</th>
                        <th>播放url</th>
                        <th>简介</th>
                        <th>下载组</th>
                        <th>下载地址</th>
                        <th>推送标志</th>
                        <th>上下架</th>
                        <th>审核状态</th>
                        <th width="150">操作</th>
                        <th width="150">审核操作</th>
                        </tr>
                        @foreach($below as $vo)
                                <tr>
                                    <td>{{$vo->id}}</td>
                                    <td>{{$vo->name}}</td>
                                    <td>{{$vo->subname}}</td>
                                    <td>{{$vo->enname}}</td>
                                    <td>{{$vo->letter}}</td>
                                    <td>{{$vo->type_name}}</td>
                                    <td id="tt">
                                        <img src="{{$vo->pic}}" alt="" style="width: 50px;height: 50px">
                                    </td>
                                    <td>{{$vo->lang}}</td>
                                    <td>{{$vo->area}}</td>
                                    <td>{{$vo->score}}</td>
                                    <td>{{$vo->year}}</td>
                                    <td>{{$vo->last}}</td>
                                    <td>{{$vo->state}}</td>
                                    <td>{{$vo->note}}</td>
                                    <td>{{$vo->actor}}</td>
                                    <td>{{$vo->director}}</td>
                                    <td>{{$vo->playfrom}}</td>
                                    <td>{{$vo->dd}}</td>
                                    <td>{{$vo->des}}</td>
                                    <td>{{$vo->downfrom}}</td>
                                    <td>{{$vo->downurl}}</td>
                                    <td>{{$vo->tuisong}}</td>
                                    <td>{{$show[$vo->status]}}</td>
                                    <td>{{$status[$vo->vod_status]}}</td>

                                    <td>
                                        <a href="#" class="btn btn-default" title="编辑"><span class="fa fa-edit"></span> 编辑</a>
                                        @if($vo->status == 0 || $vo->status == -5)
                                            <a href="{{ route('vodAbove',$vo->id )}}" class="btn btn-success" title="上架"
                                               onclick="return confirm('是否上架？');"><span class="fa fa-edit"></span> 上架</a>
                                        @elseif($vo->status == 5)
                                            <a href="{{ route('vodBelow',$vo->id )}}" class="btn btn-danger" title="下架"><span class="fa fa-edit"></span> 下架</a>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="#" class="btn btn-default" title="审核通过"><span class="fa fa-edit"></span> 审核通过</a>
                                        <a class="btn btn-default" href="#" title="删除" ><span class="fa fa-trash-o"></span> 删除</a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                        {{ $trash->links() }}
                    </table>

                </div>
            </div>
            <div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $("#img").click(function(){
                var width = $(this).width();
                if(width == 50)
                {
                    $(this).width(200);
                    $(this).height(300);
                }
                else
                {
                    $(this).width(100);
                    $(this).height(150);
                }
            });
        });
    </script>
    <script>
        document.getElementById('search_id').onclick = function () {
            document.getElementById('tro_id').value = 'search';
            document.getElementById('form_id').setAttribute('action', "{{route('index')}}");
            document.getElementById('form_id').submit();
        };
    </script>
@stop