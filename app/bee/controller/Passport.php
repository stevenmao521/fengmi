<?php
 /* 
  * Copyright (C) 2017 All rights reserved.
 *   
 * @File UserTest.php
 * @Brief 
 * @Author 毛子
 * @Version 1.0
 * @Date 2017-12-26
 * @Remark 账户
 */
namespace app\bee\controller;
use think\Config;
use think\Db;
use app\bee\service\HelperDao;

class Passport extends Common {
    
    protected $helper;
    protected $mem_model;


    public function _initialize() {
        parent::_initialize();
        $this->helper = new HelperDao();
        $this->mem_model = model("Members");
    }
    
    
    #授权页面
    public function index() {
        $code = $_GET["code"];
        $state = $_GET["state"];
        
        $wx_info = db("wx_user")->find();
        $appid = $wx_info['appid'];
        $secret = $wx_info['appsecret'];
        
        //第一步:取得openid
        $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        $oauth2 = $this->getJson($oauth2Url);

        //第二步:根据全局access_token和openid查询用户信息
        $access_token = $oauth2["access_token"];
        $openid = $oauth2['openid'];
        $get_user_info_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        $userinfo = $this->getJson($get_user_info_url);
        
        if($userinfo['openid']){
            $openid = $userinfo['openid'];
            $nickname = $userinfo['nickname'];
            $headimg = $userinfo['headimgurl'];
            
            #用户是否存在
            $hasuser = $this->mem_model->where("openid='{$userinfo['openid']}'")->find();
            
            if ($hasuser) {
                #更新用户信息
                $this->mem_model
                    ->where(array('id' => $hasuser['id']))
                    ->update(
                        array(
                        'nickname' => $nickname,
                        'avatarurl' => $headimg,
                        'lasttime' => time()
                        )
                    );
                
                session("userid", $hasuser['id'],3600*24*15);
                #是否绑定手机
                if (!$hasuser['mobile']) {
                    $this->redirect("bee/Passport/bindPhone",["state"=>$state]);
                } else {
                    $this->redirect($state);
                }
            }else{
                #创建用户
                $res = $this->mem_model
                    ->insertGetId(
                    array(
                        'nickname' => $nickname, 
                        'openid' => $openid, 
                        'avatarurl' => $headimg, 
                        'regtime' => time(), 
                        'createtime' => time()
                        )
                    );
                session("userid", $res, 3600*24*15);
                $this->redirect("bee/Passport/bindPhone",["state"=>$state]);
            }
        }
    }
    
    #绑定手机号
    public function bindPhone() {
        $ispost = input("ispost");
        $phone = input("phone");
        $code = input("code");
        
        $uid = session("userid");
        if (!$uid) {
            $this->checklogin();
        }
        
        if ($ispost) {
            $checkCode = $this->helper->checkCode($phone, $code, 1);
            if ($checkCode) {
                #绑定成功
                $mem_info = $this->mem_model->where("id='{$uid}'")->find();
                if ($mem_info['mobile']) {
                    return mz_apierror("已经绑定过手机");
                } else {
                    $res = $this->mem_model->where("id='{$uid}'")->update(["mobile"=>$phone]);
                    if ($res) {
                        return mz_apisuc("绑定成功");
                    } else {
                        return mz_apierror("绑定失败");
                    }
                }
            } else {
                return mz_apierror("验证码错误");
            }
        }
        $this->assign("title", "微信登录-绑定手机号");
        return $this->fetch();
    }
    
    #退出登录
    public function logout() {
        session(null);
        $this->redirect('Index/index');
    }
    
    public function getJson($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

}