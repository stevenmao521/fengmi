<?php
 /* 
  * Copyright (C) 2017 All rights reserved.
 *   
 * @File UserTest.php
 * @Brief 
 * @Author 毛子
 * @Version 1.0
 * @Date 2017-12-26
 * @Remark 个人中心
 */
namespace app\bee\controller;
use think\Config;
use think\Db;
use app\bee\service\HelperDao;

class Userinfo extends Common {
    
    protected $helper;
    protected $mem_model;
    protected $addr_model;


    public function _initialize() {
        parent::_initialize();
        $this->assign("title", "个人中心");
        $this->helper = new HelperDao();
        $this->mem_model = model("Members");
        $this->addr_model = model("Address");
    }
    
    public function index() {
        $uid = session("userid");
        $mem_info = $this->mem_model->where("id='{$uid}'")->find();
        
        $this->assign("info", $mem_info);
        return $this->fetch();
    }
    
    #个人中心资料编辑
    public function info() {
        $uid = session("userid");
        $mem_info = $this->mem_model->where("id='{$uid}'")->find();
        $mem_info['level_name'] = mz_gettype($mem_info['level']);
        
        $this->assign("info", $mem_info);
        $this->assign("title", "个人资料");
        return $this->fetch();
    }
    
    #修改昵称
    public function changename() {
        $uid = session("userid");
        $ispost = input("ispost");
        $nickname = input("nickname");
        
        $mem_info = $this->mem_model->where("id='{$uid}'")->find();
        if ($ispost) {
            $res = $this->mem_model->where("id='{$uid}'")->update(['nickname'=>$nickname]);
            if ($res) {
                return mz_apisuc("修改成功");
            } else {
                return mz_apierror("修改失败");
            }
        }
        
        $this->assign("info", $mem_info);
        $this->assign("title", "修改昵称");
        return $this->fetch();
    }
    
    #我的推广
    public function myshare() {
        $uid = session("userid");
        $mem_info = $this->mem_model->where("id='{$uid}'")->find();
        
        $this->assign("info", $mem_info);
        $this->assign("title", "我的推广");
        return $this->fetch();
    }
    
    #我的二维码
    public function qrcode() {
        
    }
    
    #我的收获地址
    public function myaddress() {
        $uid = session("userid");
        $mem_info = $this->mem_model->where("id='{$uid}'")->find();
        
        $address_list = $this->addr_model->where("uid='{$uid}'")->select();
        
        $this->assign("list", $address_list);
        $this->assign("info", $mem_info);
        $this->assign("title", "我的收获地址");
        return $this->fetch();
    }
    
    #添加新收获地址
    public function addaddress() {
        $uid = session("userid");
        $ispost = input("ispost");
        $uname = input("uname");
        $mobile = input("mobile");
        $pro_city_reg = input("pro_city_reg");
        $detail = input("detail");
        $isdef = input("isdef");
        
        if ($ispost) {
            $ins_data = array();
            $ins_data = [
                "uid"=>$uid,
                "uname"=>$uname,
                "mobile"=>$mobile,
                "pro_city_reg"=>$pro_city_reg,
                "detail"=>$detail,
                "isdef"=>$isdef,
                "createtime"=>time()
            ];
            if ($isdef == 1) {
                $this->addr_model->where("uid='{$uid}'")->update(["isdef"=>0]);
            }
            $res = $this->addr_model->insert($ins_data);
            if ($res) {
                return mz_apisuc("添加成功");
            } else {
                return mz_apierror("添加失败");
            }
        }
        $this->assign("title", "添加收货地址");
        return $this->fetch();
    }
    
    #编辑收货地址
    public function editaddress() {
        $uid = session("userid");
        $id = input("id");
        $ispost = input("ispost");
        $uname = input("uname");
        $mobile = input("mobile");
        $pro_city_reg = input("pro_city_reg");
        $detail = input("detail");
        $isdef = input("isdef");
        
        $address = $this->addr_model->where("id='{$id}'")->find();
        
        if ($ispost) {
            $ins_data = array();
            $ins_data = [
                "uname"=>$uname,
                "mobile"=>$mobile,
                "pro_city_reg"=>$pro_city_reg,
                "detail"=>$detail,
                "isdef"=>$isdef,
                "createtime"=>time()
            ];
            if ($isdef == 1) {
                $this->addr_model->where("uid='{$uid}'")->update(["isdef"=>0]);
            }
            $res = $this->addr_model->where("id='{$id}'")->update($ins_data);
            if ($res) {
                return mz_apisuc("修改成功");
            } else {
                return mz_apierror("修改失败");
            }
        }
        
        $this->assign("info", $address);
        $this->assign("title", "编辑收货地址");
        return $this->fetch();
    }
    
    #删除收获地址
    public function deladdress() {
        $id = input("id");
        $res = $this->addr_model->where("id='{$id}'")->delete();
        if ($res) {
            return mz_apisuc("删除成功");
        } else {
            return mz_apierror("删除失败");
        }
    }
    
    #在线客服
    public function service() {
        $this->assign("title", "在线客服");
        return $this->fetch();
    }
    
    #关于我们
    public function aboutus() {
        $this->assign("title", "关于我们");
        return $this->fetch();
    }
    
    #关于我们
    public function myservice() {
        $uid = session("userid");
        $mem = $this->mem_model->where("id='{$uid}'")->find();
        if ($mem['parent_id']) {
            $parent = $this->mem_model->where("id='{$mem['parent_id']}'")->find();
            $this->assign("info", $parent);
        }
        $this->assign("title", "我的服务商");
        return $this->fetch();
    }
    
    #我的团队
    public function myteam() {
        $uid = session("userid");
        $mem_info = $this->mem_model->where("id='{$uid}'")->find();
        $mem_info['level_name'] = mz_gettype($mem_info['level']);
        
        #我的团队
        $child_list = $this->mem_model
                ->where("parent_id='{$mem_info['id']}'")
                ->order("createtime desc")->select();
                
        $child_level1 = [];
        $child_level2 = [];
        $child_level3 = [];
        $child_level4 = [];
                
        if ($child_list) {
            foreach ($child_list as $k=>$v) {
                switch ($v['level']) {
                    case 1:
                        $child_level1[] = $v;
                        break;
                    case 2:
                        $child_level2[] = $v;
                        break;
                    case 3:
                        $child_level3[] = $v;
                        break;
                    case 4:
                        $child_level4[] = $v;
                        break;
                }
            }
        }
        
        $this->assign("level1", $child_level1);
        $this->assign("level2", $child_level2);
        $this->assign("level3", $child_level3);
        $this->assign("level4", $child_level4);
        $this->assign("info", $mem_info);
        $this->assign("title", "我的团队");
        return $this->fetch();
    }
    
}