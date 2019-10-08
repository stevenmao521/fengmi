<?php
 /* 
  * Copyright (C) 2017 All rights reserved.
 *   
 * @File UserTest.php
 * @Brief 
 * @Author 毛子
 * @Version 1.0
 * @Date 2017-12-26
 * @Remark 年终分红
 */
namespace app\bee\controller;
use think\Config;
use think\Db;
use app\bee\service\HelperDao;

class Year extends Common {
    
    protected $helper;
    protected $mem_model;
    protected $addr_model;
    protected $order_model;
    protected $orderdetail_model;
    protected $product_model;
    protected $memberresult_model;
    protected $membership_model;
    protected $sysconfig_model;
    protected $levellog_model;
    
    protected $start;
    protected $end;

    public function _initialize() {
        parent::_initialize();
        $this->assign("title", "个人中心");
        $this->helper = new HelperDao();
        $this->mem_model = model("Members");
        $this->addr_model = model("Address");
        $this->order_model = model("Order");
        $this->orderdetail_model = model("Orderdetail");
        $this->product_model = model("Product");
        $this->memberresult_model = model("Memberresult");
        $this->membership_model = model("Membership");
        $this->sysconfig_model = model("Sysconfig");
        $this->levellog_model = model("Levellog");
        
        $this->start = "2018-4";
        $this->end = "2019-10";
        
        
        #检查登陆
        #$this->checklogin();
    }
    
    public function done() {
        $level3_list = db('members')->where("level=4")->select();
        
        $start = "2018-09";
        $end = "2019-10";
        
        foreach ($level3_list as $k=>$v) {
            #需要减去的瓶数
            $result = array();
            $result = $this->recurrence($v['id'], $result);
            $level3_list[$k]['res'] = $result;
        }
        
        print_r($level3_list);
    }
    
    
    public function recurrence($uid, &$result=array()) {
        $child = $this->mem_model->where("parent_id='{$uid}'")->select();
        #是总监 分支截断
        #查询当前总监的总推销数
        foreach ($child as $k=>$v) {
            if ($v['level'] == 4) {
                $mem_res = $this->memberresult_model->where("uid='{$v['id']}'")->select();
                if ($mem_res) {
                    $nums = 0;
                    foreach ($mem_res as $k1=>$v1) {
                        $start = $this->start;
                        $end = $this->end;
                        $start_str = strtotime($start."-01 00:00");
                        $end_str = strtotime($end."-29 00:00");
                        
                        $result_time = strtotime($v1['year'].$v1['month']."-01 00:00");
                        if ($result_time>=$start && $result_time<=$end_str) {
                            $nums += $v1['direct_nums'] + $v1['redirect_nums'];
                        }
                    }
                }
                $result[] = $nums;
            } else {
                $this->recurrence($v['id'], $result);
            }
        }
        return $result;
    }
    
    
}