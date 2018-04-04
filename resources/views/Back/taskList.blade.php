@extends('layouts.default')

@section('content')
    <div id="user_list" class="aw-content-wrap">
        <div class="mod">
            <div class="mod-head">
                <h3>
                    <ul class="nav nav-tabs">
                        <li>
                            <a href="{{route('taskIndex')}}">
                                任务列表
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#progress">
                                进行中的任务
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('taskVod') }}">
                                采集到的任务视频
                            </a>
                        </li>
                    </ul>
                </h3>
            </div>

            <div id="progress" class="tab-pane ">
                <table class="table table-hover table-bordered " style="align-content: center">
                    <tr style="border:1px solid #979797;font-size: 15px" :>
                        <th>ID</th>
                        <th>任务名称</th>
                        <th>任务描述</th>
                        <th>创建时间</th>
                        <th>修改时间</th>
                        <th>任务状态</th>
                        <th width="300">操作</th>
                    </tr>
                    @foreach($down as $vos)
                        <tr>
                            <td>{{$vos->id}}</td>
                            <td>{{$vos->name}}</td>
                            <td>{{$vos->desc}}</td>
                            <td>{{$vos->created_at}}</td>
                            <td>{{$vos->updated_at}}</td>
                            <td>{{$status[$vos->task_status]}}</td>
                            <td>
                                <a class="btn btn-success" href="{{route('taskEdit',$vos->id)}}" title="完成" onclick="return confirm('是否完成？');"><span class="fa fa-adjust"></span>点击完成</a>
                                <a class="btn btn-success" href="{{route('taskGather',$vos->id)}}" title="采集" onclick="return confirm('是否采集？');"><span class="fa fa-archive"></span>点击采集该任务</a>
                                <a class="btn btn-danger" href="{{route('taskDelete',$vos->id)}}" title="取消" onclick="return confirm('是否取消？');"><span class="fa fa-trash-o"></span>点击取消</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
                {{ $down->links() }}
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