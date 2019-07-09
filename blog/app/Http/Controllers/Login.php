<?php


namespace App\Http\Controllers;


use App\Common\Auth\JWTAuth;
use App\Http\Response\ResponseJson;
use Illuminate\Http\Request;

class Login
{
    use ResponseJson;

    /**
     * 登录生成token
     * @param Request $request
     */
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        // 查询数据库中 该用户是否存在 如果存在就生成token
        $jwtAuth = JWTAuth::getInstance();
        $token = $jwtAuth->setUserId(1)->encode()->getToken();
        $this->success(['token'=>$token],'login success');
    }

}