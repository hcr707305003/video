<?php

namespace App\Http\Controllers;


class ApiController extends Controller
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseNotFound($message = 'Not fount')
    {
        return $this->setStatusCode(404)->responseError($message);
    }

    /**
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    private function responseError($message)
    {
        return $this->apiResponse([
            'status' => 'failed',
            'code' => $this->getStatusCode(),
            'message' => $message
        ]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiResponse($data)
    {
        return response()->json($data, $this->getStatusCode());
    }
}