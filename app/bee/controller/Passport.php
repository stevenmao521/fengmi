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
    
    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        
        return $this->fetch('index');
    }
}