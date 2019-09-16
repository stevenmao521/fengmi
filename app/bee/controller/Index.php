<?php
 /* 
  * Copyright (C) 2017 All rights reserved.
 *   
 * @File UserTest.php
 * @Brief 
 * @Author 毛子
 * @Version 1.0
 * @Date 2017-12-26
 * @Remark 首页
 */
namespace app\bee\controller;
use think\Config;
use think\Db;

class Index extends Common {
    
    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        
        $this->assign("title", "蜂蜜商城");
        return $this->fetch('index');
    }
}