<?php

namespace App\Http\Controllers\Back;

use App\Models\Task;
use App\Models\Vods;
use App\Models\VodTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $status = [
        '0' => '新任务',
        '1' => '待完成',
        '5' => '已完成',
        '-5' => '已取消'
    ];

    /**
     * 任务展示
     */
    public function taskIndex(Request $request)
    {

        $where = '';//条件
        $like = '';  //查询
        //名称
        if ($request->title) {
            $where = 'title';
            $like = $request->title;
        }
        //类别
        if ($request->task_status) {
            $where = 'task_status';
            $like = $request->task_status;
        }

        if ($where !== '') {
            $data = Task::where($where, 'like', '%' . $like . '%')->get();
        } else {
            $data = Task::where(['task_status' => 5])->paginate(20);  //已完成
        }

        $new = Task::where(['task_status' => 0])->get();  //新任务

        return view('Back.taskIndex')
            ->with('data', $data)
            ->with('new', $new)
            ->with('status', $this->status);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function taskList()
    {
        $down = Task::where(['task_status' => 1])->paginate(20);  //进行的任务
        return view('Back.taskList')
            ->with('down', $down)
            ->with('status', $this->status);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function taskEdit($id)  //完成
    {
        $data = Task::find($id);
        $data->where('id', $id)->update(['task_status' => '5']);
        return redirect()->route('taskIndex');
    }

    /**
     * 新任务点击
     */
    public function taskNew($id)
    {
        $data = Task::find($id);
        $data->where('id', $id)->update(['task_status' => '1']);
        return redirect()->route('taskList');
    }

    /**
     * 删除/取消任务
     */
    public function taskDelete($id)
    {
        if ($id !== '') {
            $del = Task::find($id);
            $del->delete();
            return redirect()->route('taskIndex');
        }
        return redirect()->route('taskIndex');
    }

    /**
     *发送采集任务 参数name
     */
    public function taskGather($id)
    {

        if ($id !== '') {
            $row = Task::find($id);

            $data = [
                'name' => $row->name
            ];
            $url = '';
            $ids = file_get_contents(curl($url, $data));
            foreach ($ids as $k => $v) {
                for ($i = 0; $i < count($v); $i++) {
                    VodTask::create(['id' => '', 'vods_id' => $v]);
                }
            }
            return redirect()->route('taskIndex');
        }
        return redirect()->route('taskIndex');  //已防止过度点击页面报错
    }

    /**
     * 采集到的视频展示
     */
    public function taskVod()
    {
        $status = [
            '0' => '待审核',
            '5' => '通过审核',
            '-5' => '未过审核',
            '99' => '废除'
        ];
        /**
         * @var array
         */
        $show = [
            '0' => '待上架',
            '5' => '已上架',
            '-5' => '已下架'
        ];
        $list = DB::table('vods_task as t')
            ->leftJoin('vods as v', 't.vods_id', '=', 'v.id')
            ->select('v.*')
            ->get();
        return view('Back.taskVod')
            ->with('list', $list)
            ->with('status', $status)
            ->with('show', $show);
    }

    /**
     * 发送采集的数据审核通过,数据给子站 curl
     */
    public function taskCheck($id)
    {
        $data = [

        ];
        $url = '';
        $status = Vods::find($id);
        if ($status['vod_status'] == 0 || $status['vod_status'] == -5) {
            //curl传输数据
            $res = curl($url, $data);
            dump($res);

            $status->where('id', $id)->update(['vod_status' => '5', 'status' => 5]);  //审核通过.直接上架

            return redirect()->route('taskVod');
        }
        return redirect()->route('taskVod');
    }
}
