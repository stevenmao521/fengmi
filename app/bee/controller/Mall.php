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
    protected $flow_model;
    protected $levellog_model;
    protected $productcate_model;

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
        $this->flow_model = model("Memberflow");
        $this->levellog_model = model("Levellog");
        $this->productcate_model = model("Productcate");
    }
    
    #列表
    public function lists() {
        $uid = session("userid");
        $cid = input("cid") ? input("cid") : 1;
        
        $where = " cid = '{$cid}'";
        
        $catelist = $this->productcate_model->where("istrash=0")->order("listorder asc")->select();
        $product_list = $this->product_model
            ->where("isnew=2 and istrash=0")
            ->where($where)
            ->limit(20)->order("listorder asc")
            ->select();
        
        if ($product_list) {
            foreach ($product_list as $k=>$v) {
                $product_list[$k]['tag_name'] = mz_gettag($v['tag']);
                $product_list[$k]['pic'] = mz_pic($v['pics']);
            }
        }
        
        $this->assign("catelist", $catelist);
        $this->assign("list", $product_list);
        $this->assign("cid", $cid);
        return $this->fetch();
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
        
        if (!$uid) {
            $this->checklogin();
        }

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
            $r = $this->cart_model->where(["id" => $cart['id']])->update($updata);
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
        
        if (!$uid) {
            $this->checklogin();
        }

        $cart_list = $this->cart_model
            ->where("uid='{$uid}'")->order("createtime desc")->select();

        if ($cart_list) {
            foreach ($cart_list as $k => $v) {
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
        
        if (!$uid) {
            $this->checklogin();
        }
        
        $data = input("data");
        $member = $this->mem_model->where("id='{$uid}'")->find();


        if (!$data) {
            return mz_apierror("订单创建失败");
        } else {
            $data = substr($data, 0, -1);

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
                        $order_data['addressid'] = $address['id'];
                        $order_data['addrname'] = $address['uname'];
                        $order_data['addrmobile'] = $address['mobile'];
                        $order_data['addrdetail'] = $address['pro_city_reg'] . $address['detail'];
                    } else {
                        return mz_apierror("请先添加收获地址");
                    }
                    $order_data['uid'] = $uid;
                    $order_data['createtime'] = time();
                    $res = Db::name('Order')->insert($order_data, false, true);

                    #更新之前未付款的订单未废弃订单 status=2
                    $res_up_order = Db::name('Order')->where("id!='{$res}' and uid='{$uid}' and haspay=0")->update(array("status" => 2));


                    $total_price = 0;
                    $total_nums = 0;
                    $data_arr = explode(",", $data);
                    foreach ($data_arr as $k => $v) {
                        $data_exp = explode("_", $v);
                        $cart_info = Db::name('Cart')->where("id='{$data_exp[0]}'")->find();
                        $product_info = Db::name('Product')->where("id='{$cart_info['product_id']}'")->find();
                        if ($product_info) {
                            if ($member['level'] == 1) {
                                $total_price += $product_info['price'] * $data_exp[1];
                            } else {
                                $total_price += $product_info['reprice'] * $data_exp[1];
                            }
                            $total_nums += $data_exp[1];
                        }

                        $detail_data = array();
                        $detail_data['oid'] = $res;
                        $detail_data['product_id'] = $product_info['id'];
                        $detail_data['product_name'] = $product_info['name'];
                        $detail_data['nums'] = $data_exp[1];
                        if ($member['level'] == 1) {
                            $detail_data['price'] = $product_info['price'];
                        } else {
                            $detail_data['price'] = $product_info['reprice'];
                        }
                        $detail_data['isrebate'] = $product_info['isrebate'];
                        $detail_data['createtime'] = time();
                        $detail_data['bottles'] = $data_exp[1] * $product_info['bottles'];
                        $detail_data['isnew'] = $product_info['isnew'];
                        
                        $res_1 = Db::name('Orderdetail')->insert($detail_data);

                        #删除购物车相应产品
                        $res_2 = Db::name('Cart')->where("product_id='{$product_info['id']}' and uid='{$uid}'")->delete();
                    }
                    Db::name('Order')->where("id='{$res}'")->update(array("total_price" => $total_price, "total_nums" => $total_nums));

                    if ($res && $res_1 && $res_2) {
                        Db::commit();
                        return mz_apisuccess("订单创建成功");
                    } else {
                        return mz_apierror("订单创建失败");
                    }
                } else {
                    #插入订单
                    $order_data = array();
                    $order_data['orderid'] = mz_get_order_sn();
                    if ($address) {
                        $order_data['addressid'] = $address['id'];
                        $order_data['addrname'] = $address['uname'];
                        $order_data['addrmobile'] = $address['mobile'];
                        $order_data['addrdetail'] = $address['pro_city_reg'] . $address['detail'];
                    } else {
                        return mz_apierror("请先添加收获地址");
                    }
                    $order_data['uid'] = $uid;
                    $order_data['createtime'] = time();
                    $res = Db::name('Order')->insert($order_data, false, true);

                    #更新之前未付款的订单未废弃订单 status=2
                    $res_up_order = Db::name('Order')->where("id!='{$res}' and uid='{$uid}' and haspay=0")->update(array("status" => 2));

                    $total_price = 0;
                    $total_nums = 0;

                    $data_exp = explode("_", $data);
                    $cart_info = Db::name('Cart')->where("id='{$data_exp[0]}'")->find();
                    $product_info = Db::name('Product')->where("id='{$cart_info['product_id']}'")->find();

                    if ($product_info) {
                        if ($member['level'] == 1) {
                            $total_price = $product_info['price'] * $data_exp[1];
                        } else {
                            $total_price = $product_info['reprice'] * $data_exp[1];
                        }
                        $total_nums = $data_exp[1];
                    }

                    $detail_data = array();
                    $detail_data['oid'] = $res;
                    $detail_data['product_id'] = $product_info['id'];
                    $detail_data['product_name'] = $product_info['name'];
                    $detail_data['nums'] = $data_exp[1];
                    if ($member['level'] == 1) {
                        $detail_data['price'] = $product_info['price'];
                    } else {
                        $detail_data['price'] = $product_info['reprice'];
                    }
                    $detail_data['isrebate'] = $product_info['isrebate'];
                    $detail_data['bottles'] = $data_exp[1] * $product_info['bottles'];
                    $detail_data['isnew'] = $product_info['isnew'];
                    $detail_data['createtime'] = time();
                    $res_1 = Db::name('Orderdetail')->insert($detail_data);

                    #删除购物车相应产品
                    $res_2 = Db::name('Cart')->where("product_id='{$product_info['id']}' and uid='{$uid}'")->delete();
                    Db::name('Order')->where("id='{$res}'")->update(array("total_price" => $total_price, "total_nums" => $total_nums));
                    if ($res && $res_1 && $res_2) {
                        Db::commit();
                        return mz_apisuc("订单创建成功");
                    } else {
                        return mz_apierror("订单创建失败");
                    }
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return mz_apierror("订单创建失败");
            }
        }
    }

    #立即购买
    public function addordernow() {
        $uid = session("userid");
        if (!$uid) {
            $this->checklogin();
        }
        
        $member = $this->mem_model->where("id='{$uid}'")->find();
        $product_id = input("id");

        $address = $this->address_model->where("uid='{$uid}' and isdef")->find();
        if (!$address) {
            $address = $this->address_model->where("uid='{$uid}'")->order("id desc")->find();
        }

        Db::startTrans();
        try {

            #插入订单
            $order_data = array();
            $order_data['orderid'] = mz_get_order_sn();
            if ($address) {
                $order_data['addressid'] = $address['id'];
                $order_data['addrname'] = $address['uname'];
                $order_data['addrmobile'] = $address['mobile'];
                $order_data['addrdetail'] = $address['pro_city_reg'] . $address['detail'];
            } else {
                return mz_apierror("请先添加收获地址");
            }
            $order_data['uid'] = $uid;
            $order_data['createtime'] = time();
            $res = Db::name('Order')->insert($order_data, false, true);

            #更新之前未付款的订单未废弃订单 status=2
            $res_up_order = Db::name('Order')->where("id!='{$res}' and uid='{$uid}' and haspay=0")->update(array("status" => 2));

            $total_price = 0;
            $total_nums = 0;

            
            $product_info = Db::name('Product')->where("id='{$product_id}'")->find();
            if ($product_info) {
                if ($member['level'] == 1) {
                    $total_price = $product_info['price'];
                } else {
                    $total_price = $product_info['reprice'];
                }
                $total_nums = 1;
            }

            $detail_data = array();
            $detail_data['oid'] = $res;
            $detail_data['product_id'] = $product_info['id'];
            $detail_data['product_name'] = $product_info['name'];
            $detail_data['nums'] = 1;
            if ($member['level'] == 1) {
                $detail_data['price'] = $product_info['price'];
            } else {
                $detail_data['price'] = $product_info['reprice'];
            }
            $detail_data['isrebate'] = $product_info['isrebate'];
            $detail_data['bottles'] = $product_info['bottles'];
            $detail_data['isnew'] = $product_info['isnew'];
            $detail_data['createtime'] = time();
            $res_1 = Db::name('Orderdetail')->insert($detail_data);

            Db::name('Order')->where("id='{$res}'")->update(array("total_price" => $total_price, "total_nums" => $total_nums));
            if ($res && $res_1) {
                Db::commit();
                return mz_apisuc("订单创建成功");
            } else {
                return mz_apierror("订单创建失败");
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return mz_apierror("订单创建失败");
        }
    }

    #订单详情页
    public function orderdetail() {
        $uid = session("userid");
        if (!$uid) {
            $this->checklogin();
        }
        
        $order_info = $this->order_model->where("uid='{$uid}' and status=0")->order("createtime desc")->find();
        #订单商品
        $product_detail = $this->order_detail_model->where("oid='{$order_info['id']}'")->find();
        $product_info = $this->product_model->where("id='{$product_detail['product_id']}'")->find();
        $product_detail['pic'] = mz_pic($product_info['pics']);


        $this->assign("order", $order_info);
        $this->assign("product_detail", $product_detail);
        $this->assign("title", "订单");
        return $this->fetch();
    }

    #订单测试已付款
    public function orderpay() {
        $uid = session("userid");
        if (!$uid) {
            $this->checklogin();
        }
        
        $id = input("id");
        $res = $this->order_model->where("id='{$id}'")->update(array('haspay' => 1, 'paytime' => time(), "status" => 1));
        if ($res) {
            #更新流水记录
            $order_info = $this->order_model->where("id='{$id}'")->find();
            $member_info = $this->mem_model->where("id='{$uid}'")->find();
            mz_flow($uid, $id, 4, "-".$order_info['total_price'], "购买蜂蜜支出", $member_info['balance']);
            
            #增加商品销量
            $order_detail = $this->order_detail_model->where("oid='{$order_info['id']}'")->select();
            foreach ($order_detail as $k=>$v) {
                $this->product_model->where("id='{$v['product_id']}'")->setInc("selnums", $v['nums']);
            }
            
            #更新用户等级
            if ($member_info['level'] == 1) {
                #进行升级，并记录日志
                $ins_data = array();
                $ins_data['uid'] = $uid;
                $ins_data['direct_nums'] = 0;
                $ins_data['indirect_nums'] = 0;
                $ins_data['des'] = "达到 业务员 等级进行升级";
                $ins_data['createtime'] = time();
                $this->levellog_model->insert($ins_data);
                $this->mem_model->where("id='{$uid}'")->update(array("level"=>2));
            }
            
            return mz_apisuc("支付成功");
        } else {
            return mz_apierror("支付失败");
        }
    }

}
