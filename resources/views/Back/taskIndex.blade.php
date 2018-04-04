@extends('layouts.default')

@section('content')
    <div id="user_list" class="aw-content-wrap">
        <div class="mod">
            <div class="mod-head">
                <h3>
                    <ul class="nav nav-tabs">
                        <li>
                            <a data-toggle="tab" href="#listss">
                                任务列表
                            </a>
                        </li>
                        <li>
                            <a href="{{route('taskList')}}">
                                进行中的任务
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('taskVod') }}">
                                采集到的任务视频
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#newtask">
                                新任务
                            </a>
                        </li>
                    </ul>
                </h3>
            </div>
            <div class="box-body tab-content">
                <div id="listss" class="tab-pane active">
                    <form class="well form-search" method="get" id="form_id" action="{{route('taskIndex')}}">
                        任务名称:
                        <input type="text" id="" name="title" value="" placeholder="查找任务名称"
                               style="width: 120px;height: 30px;border-radius:5px ;" class="border-left-right">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        任务状态：
                        <select name="task_status" id="" style="width: 120px;height: 30px;border-radius:5px ;"
                                class="border-left-right">
                            <option value="">请选择</option>
                            <option value="0">待完成</option>
                            <option value="5">已完成</option>
                        </select>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;完成时间：&nbsp;
                        <input type="datetime-local" id="" name="" style="width: 120px;height: 30px;border-radius:5px ;"
                               class="border-left-right" autocomplete="off">&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="datetime-local" id="" name="" style="width: 120px;height: 30px;border-radius:5px ;"
                               class="border-left-right" autocomplete="off"> &nbsp;
                        <input type="hidden" id="tro_id" name="action_name">

                        <input type="button" class="btn btn-primary" id="search_id" value="搜索">
                        <a class="btn btn-danger" href="{{route('index')}}">清空</a>
                    </form>
                    <table class="table table-bordered">
                        <tbody>
                        <tr style="border: #979797;font-size: 15px" :>
                            <th>ID</th>
                            <th>任务名称</th>
                            <th>任务描述</th>
                            <th>创建时间</th>
                            <th>修改时间</th>
                            <th>任务状态</th>
                            <th>操作</th>
                        </tr>
                        @foreach($data as $vo)
                            <tr>
                                <td>{{$vo->id}}</td>
                                <td>{{$vo->name}}</td>
                                <td>{{$vo->desc}}</td>
                                <td>{{$vo->created_at}}</td>
                                <td>{{$vo->updated_at}}</td>
                                <td>{{$status[$vo->task_status]}}</td>
                                <td>
                                    {{--<a class="btn btn-success" href="{{route('taskEdit',$vo->id)}}">编辑</a>--}}
                                    <a class="btn btn-danger" href="{{route('taskDelete',$vo->id)}}" title="删除"
                                       onclick="return confirm('是否删除？');"><span class="fa fa-trash-o"></span>删除</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                    {{ $data->links() }}
                </div>
                <div id="newtask" class="tab-pane ">
                    <table class="table table-hover table-bordered " style="align-content: center">
                        <tr style="border:1px solid #979797;font-size: 15px">
                            <th>ID</th>
                            <th>任务名称</th>
                            <th>任务描述</th>
                            <th>创建时间</th>
                            <th>修改时间</th>
                            <th>任务状态</th>
                            <th>操作</th>
                        </tr>
                        @foreach($new as $vos)
                            <tr>
                                <td>{{$vos->id}}</td>
                                <td>{{$vos->name}}</td>
                                <td>{{$vos->desc}}</td>
                                <td>{{$vos->created_at}}</td>
                                <td>{{$vos->updated_at}}</td>
                                <td>{{$status[$vos->task_status]}}</td>
                                <td>
                                    <a class="btn btn-success" href="{{route('taskNew',$vos->id)}}" title="任务"
                                       onclick="return confirm('是否移进任务区？');"><span class="fa fa-adn"></span>点击加入任务区</a>
                                    <a class="btn btn-danger" href="{{route('taskDelete',$vos->id)}}" title="取消"
                                       onclick="return confirm('是否取消？');"><span class="fa fa-trash-o"></span>点击取消</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('search_id').onclick = function () {
            document.getElementById('tro_id').value = 'search';
            document.getElementById('form_id').setAttribute('action', "{{route('taskIndex')}}");
            document.getElementById('form_id').submit();
        };
    </script>
@stop