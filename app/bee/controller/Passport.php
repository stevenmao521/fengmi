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
    
    public function index() {
        
        return $this->fetch('index');
    }
    
    #绑定手机号
    public function bindPhone() {
        $ispost = input("ispost");
        $phone = input("phone");
        $code = input("code");
        
        #$uid = session("userid");
        $uid = 12;
        
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
}