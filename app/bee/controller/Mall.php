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
    protected $order_model;
    protected $order_detail_model;
    protected $address_model;

    public function _initialize() {
        parent::_initialize();
        $this->assign("title", "商品详情");
        $this->helper = new HelperDao();
        $this->mem_model = model("Members");
        $this->product_model = model("Product");
        $this->cart_model = model("Cart");
        $this->order_model = model("Order");
        $this->order_detail_model = model("Orderdetail");
        $this->address_model = model("Address");
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
        $member = $this->mem_model->where("id='{$uid}'")->find();
        
        
        if (!$data) {
            return mz_apierror("订单创建失败");
        } else {
            $data = substr($data,0,-1);
            
            $address = $this->address_model->where("uid='{$uid}' and isdef")->find();
            if (!$address) {
                $address = $this->address_model->where("uid='{$uid}'")->order("id desc")->find();
            }
            
            
            
            Db::startTrans();
            try {
                
                
                if (strpos($data, ",")) {
                
                    #插入订单
                    $order_data = array();
                    $order_data['orderid'] = mz_get_order_sn();
                    if ($address) {
                        $order_data['address_id'] = $address['id'];
                        $order_data['addrname'] = $address['uname'];
                        $order_data['addrmobile'] = $address['mobile'];
                        $order_data['addrdetail'] = $address['pro_city_reg'].$address['detail'];
                    }
                    $order_data['createtime'] = time();
                    $res = Db::name('Order')->insert($order_data);
                    
                    
                    $total_price = 0;
                    $data_arr = explode(",", $data);
                    foreach ($data_arr as $k=>$v) {
                        $data_exp = explode("_", $v);
                        $product_info = $this->product_model->where("id='{$data_exp[0]}'")->find();
                        if ($product_info) {
                            if ($member['level'] == 1) {
                                $total_price += $product_info['price'] * $data_exp[1];
                            } else {
                                $total_price += $product_info['reprice'] * $data_exp[1];
                            }
                        }
                        
                        $detail_data = array();
                        $detail_data['oid'] = $res;
                        $detail_data['product_id'] = $res;
                        $detail_data['product_name'] = $res;
                        $detail_data['nums'] = $res;
                        $detail_data['price'] = $res;
                        $detail_data['isrebate'] = $res;
                        
                    }
                    
                    
                } else {


                }
                
                
                
                
                
                
                
                
                
                
                
                
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

            
            
            
            
        }
        
    }
    
    
}