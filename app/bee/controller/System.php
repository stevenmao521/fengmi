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
    protected $memberresult_model;
    protected $membership_model;
    protected $sysconfig_model;
    protected $levellog_model;

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
    }
    
    public function done() {
        $uid = session("userid");
        $id = input("id");
        
        #更新订单状态
        $this->order_model
            ->where("id='{$id}'")
            ->update(array("status"=>4,"finishtime"=>time()));
            
        $order_info = $this->order_model->where("id='{$id}'")->find();
        $order_detail = $this->orderdetail_model->where("oid='{$id}'")->select();
        
        #业绩加成
        if ($order_detail) {
            foreach ($order_detail as $k=>$v) {
                #瓶数
                $bottles = $v['bottles'];
                
                #处理提成
                $this->doachieve($uid, $v['isrebate'], $v);
                
                #处理业绩
                $this->doresult($uid, $bottles);
            }
        }
    }
    
    #处理提成
    #isrebate 2：参与分佣
    public function doachieve($uid, $isrebate, $orderdetail) {
        #
        if ($isrebate == 2) {
            
        }
        
    }
    
    #递归处理业绩
    public function doresult($uid, $bottles) {
        #当前年份
        $year = date('Y', time());
        #当前月份
        $month = date('m', time());
        $uids_link = array();
        #递归整个上级链
        $uids = $this->recurrence($uid, $uids_link);
        
        if ($uids) {
            foreach ($uids as $k=>$v) {
                $has_result = $this->memberresult_model->where("uid='{$v}' and year='{$year}' and month='{$month}'")->find();
                if ($has_result) {
                    if ($k == 0) {
                        $this->memberresult_model->where("id='{$has_result['id']}'")->setInc("direct_nums",$bottles);
                    } else {
                        $this->memberresult_model->where("id='{$has_result['id']}'")->setInc("redirect_nums",$bottles);
                    }
                    
                    #升级判断处理
                    $this->dolevel($v);
                    
                } else {
                    if ($k == 0) {
                        #直接上级 增加直销量
                        $ins_data = array();
                        $ins['uid'] = $v;
                        $ins['year'] = $year;
                        $ins['month'] = $month;
                        $ins['direct_nums'] = $bottles;
                        $ins['redirect_nums'] = 0;
                    } else {
                        #直接上级 增加直销量
                        $ins_data = array();
                        $ins['uid'] = $v;
                        $ins['year'] = $year;
                        $ins['month'] = $month;
                        $ins['direct_nums'] = 0;
                        $ins['redirect_nums'] = $bottles;
                    }
                    $this->memberresult_model->insert($ins);
                    
                    #升级判断处理
                    $this->dolevel($v);
                }
            }
        }
    }
    
    #升级判断处理
    public function dolevel($uid) {
        #系统配置参数
        $sysconfig = $this->sysconfig_model->order("level desc")->select();

        $member_info = $this->mem_model->where("id='{$uid}'")->find();
        #已是最高等级
        if ($member_info['level'] == 4) {
            return false;
        }
        
        #当前用户推销战绩
        #直销战绩
        $level_1 = 2;
        $level_2 = 2;
        
        #直销最高等级
        $direct_nums = $this->memberresult_model->where("uid='{$uid}'")->sum("direct_nums");
        foreach ($sysconfig as $k=>$v) {
            if ($direct_nums >= $v['directnums']) {
                $level_1 = $v['level'];
                break;
            }
        }
        
        #分销最高等级
        $redirect_nums = $this->memberresult_model->where("uid='{$uid}'")->sum("redirect_nums");
        foreach ($sysconfig as $k=>$v) {
            if ($redirect_nums >= $v['indirect_nums']) {
                $level_2 = $v['level'];
                break;
            }
        }
        
        $level = max($level_1, $level_2);
        switch ($level) {
            case 2:
                $level_name = "业务员";
                break;
            case 3:
                $level_name = "销售主管";
                break;
            case 4:
                $level_name = "销售总监";
                break;
        }
        
        if ($level > $member_info['level']) {
            #进行升级，并记录日志
            $ins_data = array();
            $ins_data['uid'] = $uid;
            $ins_data['direct_nums'] = $direct_nums;
            $ins_data['indirect_nums'] = $redirect_nums;
            $ins_data['des'] = "达到 {$level_name} 等级进行升级";
            $ins_data['createtime'] = time();
            $this->levellog_model->insert($ins_data);
            #更新用户等级
            $this->mem_model->where("id='{$uid}'")->update(array("level"=>$level));
        }
    }
    
    #递归求链
    public function recurrence($uid, &$result=array()) {
        $uinfo = $this->membership_model->where("uid='{$uid}'")->find();
        if ($uinfo['parentid']) {
            $result[] = $uinfo['parentid'];
            $this->recurrence($uinfo['parentid'],$result);
        }
        return $result;
    }
    
    #递归分佣链
    public function recurrence_achieve($uid, &$result=array()) {
        $uinfo = $this->membership_model->where("uid='{$uid}'")->find();
        if ($uinfo['parentid']) {
            $result[] = $uinfo['parentid'];
            $this->recurrence($uinfo['parentid'],$result);
        }
        return $result;
    }
    
    
}