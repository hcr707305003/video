<?php

namespace App\Http\Controllers\Back;

use App\Http\Requests\SonRequest;
use App\Models\SonStand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SonController extends Controller
{
    /**
     * @return $this列表
     */
    public function sonIndex(Request $request)
    {
        $status = [
            '0' => '待审核',
            '5' => '通过审核',
            '-5' => '未过审核',
        ];
        $where = '';//条件
        $like = '';  //查询
        //名称
        if ($request->name) {
            $where = 'name';
            $like = $request->name;
        }
        //类别
        if ($request->status) {
            $where = 'status';
            $like = $request->status;
        }
        //时间
        if ($request->last) {
            $where = 'last';
            $like = $request->last;
        }

        if ($where !== '') {
            $data = SonStand::where($where, 'like', '%' . $like . '%')->paginate(20);
        } else {
            $data = SonStand::paginate(20);  //获取数据
        }

        return view('Back.sonIndex')
            ->with('data', $data)
            ->with('status', $status);  //分配数据
    }

    /**
     * 添加列表
     */
    public function sonAddList()
    {
        return view('Back.sonAdd');  //展示模板
    }

    /**
     * 数据入库
     */
    public function sonAdd(SonRequest $request)
    {
        //创建并保持数据
        SonStand::create($request->all());
        //重定向到子站列表
        return redirect()->route('sonIndex');
    }

    /**
     * 编辑展示
     */
    public function sonEdit($id)
    {
        $edit = SonStand::find($id);
        return view('Back.sonEdit')
            ->with('edit', $edit);  //分配数据库数据,展示
    }

    /*
     * 编辑提交
     */
    public function sonUpdate(SonRequest $request, $id)  //获取id
    {
        $result = SonStand::find($id); //查询一条数据
        $result->fill($request->all());  //过滤字段
        $result->save();   //保存修改数据

        return redirect()->route('sonIndex');  //
    }

    /**
     * 删除
     */
    public function sonDelete($id)
    {
        $vod = SonStand::find($id);
        $vod->delete();
        return redirect()->route('sonIndex');
    }

    /**
     *
     */
    public function sonStatus($id)
    {
        $status = SonStand::find($id);  //查询是否有这条数据

        if ($status['status'] == 0 || $status['status'] == -5) {
            $status->where('id', $id)->update(['status' => '5']);
            return redirect()->route('sonIndex');
        } elseif ($status['status'] == 0 || $status['status'] == 5) {
            $status->where('id', $id)->update(['status' => '-5']);
            return redirect()->route('sonIndex');
        }

    }
}
