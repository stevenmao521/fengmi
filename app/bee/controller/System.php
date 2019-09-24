<?php
 /* 
  * Copyright (C) 2017 All rights reserved.
 *   
 * @File UserTest.php
 * @Brief 
 * @Author 毛子
 * @Version 1.0
 * @Date 2017-12-26
 * @Remark 分销处理
 */
namespace app\bee\controller;
use think\Config;
use think\Db;
use app\bee\service\HelperDao;

class System extends Common {
    
    protected $helper;
    protected $mem_model;
    protected $addr_model;
    protected $order_model;
    protected $orderdetail_model;
    protected $product_model;

    public function _initialize() {
        parent::_initialize();
        $this->assign("title", "个人中心");
        $this->helper = new HelperDao();
        $this->mem_model = model("Members");
        $this->addr_model = model("Address");
        $this->order_model = model("Order");
        $this->orderdetail_model = model("Orderdetail");
        $this->product_model = model("Product");
    }
    
    public function done() {
        $uid = session("uid");
        $id = input("id");
        
        #更新订单状态
        $this->order_model
            ->where("id='{$id}'")
            ->update(array("status"=>4,"finishtime"=>time()));
            
        $order_info = $this->order_model->where("id='{$id}'")->find();
        $order_detail = $this->orderdetail_model->where("oid='{$id}'")->select();
        
        if ($order_detail) {
            foreach ($order_detail as $k=>$v) {
                
                
                
            }
        }
        
    }
    
}