<?php


namespace App\Http\Response;


trait ResponseJson
{
    /**
     * 处理成功后返回json的方法
     * @param $message
     * @param $data
     */
    public function success($data=[],$message='success')
    {
        $this->jsonResponse(0, $message, $data);
    }

    /**
     * 处理失败后返回json的方法
     * @param $code
     * @param $message
     * @param $data
     */
    public function error($code, $message='error', $data=[])
    {
        $this->jsonResponse($code, $message, $data);
    }

    /**
     * 当前类封转的私有方法
     * @param $code
     * @param $message
     * @param $data
     */
    private function jsonResponse($code, $message, $data)
    {
        $arr = [
            'code' => $code,
            'meg' => $message,
            'data' => $data
        ];
        echo json_encode($arr);
        die;
    }
}