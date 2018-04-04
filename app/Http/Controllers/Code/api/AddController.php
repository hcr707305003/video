<?php
/**
 * Api接口 交互
 */

namespace App\Http\Controllers\Code\api;

use App\Http\Controllers\ApiController;
use App\Models\Task;
use App\Models\Vods;
use app\Transformer\VodsTransformer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class AddController extends ApiController
{
    /**
     * @var VodsTransformer
     */
    protected $vodsTransformer;

    /**
     * AddController constructor.
     * @param VodsTransformer $vodsTransformer
     */
    public function __construct(VodsTransformer $vodsTransformer)
    {
        $this->vodsTransformer = $vodsTransformer;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    //审核接口通过
    public function httpAdd()
    {
        $row = $_POST;
        $data = Vods::find($row);
        if (!$data) {
            return $this->responseNotFound();
        }
        return $this->apiResponse([
            'status' => 'success',
            'data' => $this->vodsTransformer->transformCollection($data->toArray(), '')   //隐藏数据库字段名(重构)
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function editApi()
    {
        if (!$_POST) {
            $data = [
                'stauts' => 'success',
                'code' => 404,
                'data' => 'Not Found'
            ];
            return $this->apiResponse($data);
        }
        $row = $_POST;
        $data = Vods::find($row);
        if (!$data) {
            return $this->responseNotFound();
        }
        return $this->apiResponse([
            'status' => 'success',
            'data' => $this->vodsTransformer->transformCollection($data->toArray(), '')   //隐藏数据库字段名(重构)
        ]);
    }

    /**
     *接收任务接口
     */
    public function taskApi()
    {
        $data = isset($_POST) ? $_POST : '请求失败';

        $result = Task::create($data);
        if ($result !== false) {
            return $this->apiResponse([
                'status' => 200
            ]);
        } else {
            return $this->apiResponse([
                $this->responseNotFound()
            ]);
        }
    }
}
