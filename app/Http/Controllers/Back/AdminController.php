<?php

namespace App\Http\Controllers\Back;

use App\Models\Vods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * @var array
     */
    protected $status = [
        '0' => '待审核',
        '5' => '通过审核',
        '-5' => '未过审核',
        '99' => '废除'
    ];
    /**
     * @var array
     */
    protected $show = [
        '0' => '待上架',
        '5' => '已上架',
        '-5' => '已下架'
    ];

    /**
     * @param Request $request
     * @return mixed
     * 展示页index
     */
    public function index(Request $request)
    {
        $where = '';//条件
        $like = '';  //查询
        //名称
        if ($request->name) {
            $where = 'name';
            $like = $request->name;
        }
        //类别
        if ($request->type_name) {
            $where = 'type_name';
            $like = $request->type_name;
        }
        //主演
        if ($request->actor) {
            $where = 'actor';
            $like = $request->actor;
        }
        //时间
        if ($request->last) {
            $where = 'last';
            $like = $request->last;
        }

        if ($where !== '') {
            $list = Vods::where($where, 'like', '%' . $like . '%')->paginate(20);
        } else {
            $list = Vods::where('vod_status', 0)->paginate(30);  //获取上架数据
        }
        ////获取软删除数据
        $vodTrash = Vods::onlyTrashed()->paginate(30);

        $below = Vods::where('status', [-5])->paginate(30);  //获取下架架数据
        return view('Back.index')
            ->with('list', $list)
            ->with('show', $this->show)
            ->with('status', $this->status)
            ->with('trash', $vodTrash)//软删除数据分配
            ->with('below', $below) //下架数据
            ;
    }

    /**
     * 审核通过列表
     */
    public function list(Request $request)
    {
        $where = '';//条件
        $like = '';  //查询
        //名称
        if ($request->name) {
            $where = 'name';
            $like = $request->name;
        }
        //类别
        if ($request->type_name) {
            $where = 'type_name';
            $like = $request->type_name;
        }
        //主演
        if ($request->actor) {
            $where = 'actor';
            $like = $request->actor;
        }
        //时间
        if ($request->last) {
            $where = 'last';
            $like = $request->last;
        }
        if ($where !== '') {
            $list = Vods::where($where, 'like', '%' . $like . '%')->paginate(20);  //搜索
        }else{
            $list = Vods::where('vod_status', 5)->paginate(30);  //获取上架数据
        }

        return view('Back.list')
            ->with('list', $list)
            ->with('show', $this->show)
            ->with('status', $this->status);
    }

    /**
     * @param $id
     * @return $this
     * 编辑
     */
    public function edit($id)
    {
        $edit = Vods::find($id);

        return view('Back.edit')
            ->with('edit', $edit);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * 编辑提交
     */
    public function update(Request $request, $id)
    {
        $vod = Vods::find($id);
        $vod->fill($request->all());
        $row = $vod->save();
        if ($row == true) {
            $data = [
                'id' => $id,
                'vod' => 'save'  //编辑标识
            ];
            $url = 'http://www.feifei.com/index.php?s=Admin-Push-saveid';
            curl($url, $data);  //编辑
        }
        return redirect()->route('index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * 软删除
     */
    public function delete($id)
    {
        $vod = Vods::find($id);

        if ($vod) {
            $vod->where('id', $id)->update(['vod_status' => '99']);   //99状态`.废除的数据
            $vod->delete();  //软删除
            $data = [
                'id' => $vod->id,
            ];
            $url = '';
            curl($url, $data); //发送curl请求
            return redirect()->route('index');
        }


        return redirect()->route('index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * 从软删除中,恢复视频数据
     */
    public function recover($id)
    {
        //还原之前改变该数据的状态
        //0状态`.待审核
        Vods::withTrashed()->where('id', $id)->update(['vod_status' => '0']);
        #还原
        $vodrecover = Vods::withTrashed()->find($id);
        if ($vodrecover) {
            $data = [
                'id' => $vodrecover->id,
                'downurl' => $vodrecover->downurl,
            ];
            $url = '';
            curl($url, $data);
            $vodrecover->restore();

        }


        return redirect()->route('index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * 审核
     */
    public function status($id)
    {
        $data = [
            'id' => $id,    //id
            'vod' => 'add'  //数据标识
        ];
        $url = "http://www.feifei.com/index.php?s=Admin-Push-getid";
        $status = Vods::find($id);
        if ($status['vod_status'] == 0 || $status['vod_status'] == -5) {
            //curl传输数据
            $res = curl($url, $data);
            dump($res);

            $status->where('id', $id)->update(['vod_status' => '5', 'status' => 5]);  //审核通过.直接上架

            return redirect()->route('index');
        }
        return redirect()->route('index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * 上架
     */
    public function above($id)
    {
        $status = Vods::find($id);
        if ($status['status'] == 0 || $status['status'] == -5) {
            $status->where('id', $id)->update(['status' => '5']);
            $data = [
                'id' => $id,
                'vod' => 'reup'
            ];
            $url = 'http://www.feifei.com/index.php?s=Admin-Push-reup';
            curl($url, $data);
            return redirect()->route('index');
        }
        return redirect()->route('index');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * 下架
     */
    public function below($id)
    {
        $status = Vods::find($id);
        if ($status['status'] == 5) {
            $status->where('id', $id)->update(['status' => '-5']);
            //下架成功 发送id,标识
            $data = [
                'id' => $id,
                'vod' => 'down'
            ];
            $url = 'http://www.feifei.com/index.php?s=Admin-Push-down';
            curl($url, $data);
            return redirect()->route('index');
        }
        return redirect()->route('index');
    }

}


























