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

class Mall extends Common {
    
    protected $helper;
    protected $mem_model;
    protected $product_model;
    protected $cart_model;


    public function _initialize() {
        parent::_initialize();
        $this->assign("title", "商品详情");
        $this->helper = new HelperDao();
        $this->mem_model = model("Members");
        $this->product_model = model("Product");
        $this->cart_model = model("Cart");
    }
    
    public function detail() {
        $id = input("id");
        $uid = session("userid");
        $mem_info = $this->mem_model->where("id='{$uid}'")->find();
        
        $product = $this->product_model->where("id='{$id}'")->find();
        $product['pic'] = mz_pic($product['pics']);
        
        $this->assign("title", "商品详情");
        $this->assign("product", $product);
        $this->assign("info", $mem_info);
        return $this->fetch();
    }
    
    #加入购物车
    public function addcart() {
        $id = input("id");
        $uid = session("userid");
        
        $product = $this->product_model->where("id='{$id}'")->find();
        $member = $this->mem_model->where("id='{$uid}'")->find();
        
        $cart = $this->cart_model->where("uid='{$uid}' and product_id")->find();
        if (!$cart) {
            $ins_data = array();
            $ins_data['product_id'] = $product['id'];
            $ins_data['product_name'] = $product['name'];
            $ins_data['isrebate'] = $product['id'];
            if ($member['level'] == 1) {
                $ins_data['price'] = $product['price'];
            } else {
                $ins_data['price'] = $product['reprice'];
            }
            $ins_data['uid'] = $uid;
            $ins_data['createtime'] = time();
            $ins_data['nums'] = 1;
            $r = $this->cart_model->insert($ins_data);
        } else {
            $updata = array();
            $updata['nums'] = $cart['nums'] + 1;
            $updata['updatetime'] = time();
            $r = $this->cart_model->where(["id"=>$cart['id']])->update($updata);
        }
        if ($r) {
            return mz_apisuc("添加成功");
        } else {
            return mz_apierror("添加失败");
        }
    }
    
    #购物车列表
    public function cart() {
        $uid = session("userid");
        
        $cart_list = $this->cart_model
            ->where("uid='{$uid}'")->order("createtime desc")->select();
        
        if ($cart_list) {
            foreach ($cart_list as $k=>$v) {
                #总价
                $cart_list[$k]['total_price'] = $v['price'] * $v['nums'];
                $product = $this->product_model->where("id='{$v['product_id']}'")->field("pics")->find();
                $cart_list[$k]['pic'] = mz_pic($product['pics']); 
            }
        }
        
        $this->assign("title", "购物车");
        $this->assign('cartlist', $cart_list);
        return $this->fetch();
    }
    
    #创建订单
    public function addorder() {
        $uid = session("userid");
        $data = input("data");
        
        if (!$data) {
            return mz_apierror("订单创建失败");
        } else {
            $data = substr($data,0,-1);
            if (strpos($data, ",")) {
                $data_arr = explode(",", $data);
                
                
            } else {
                
                
            }
            
            
            
        }
        
    }
    
    
}