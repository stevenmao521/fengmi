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

class Passport extends Common {
    
    protected $mem_model;


    public function _initialize() {
        parent::_initialize();
        
        $this->mem_model = model("Members");
    }
    
    public function index() {
        
        return $this->fetch('index');
    }
    
    #绑定手机号
    public function bindPhone() {
        
        $this->assign("title", "微信登录-绑定手机号");
        return $this->fetch();
    }
    
    #手机号是否已绑定
    public function sendMsg() {
        $mobile = mz_checkfield('phone', true, '缺少手机号');
        $type = mz_checkfield('type', true, '缺少type');
        
        $hasPhone = $this->mem_model
                ->where(["mobile"=>$mobile])
                ->find();
        
        if ($hasPhone) {
            return mz_apierror("手机号已绑定");
        } else {
           
            
            
        }
        
    }
}