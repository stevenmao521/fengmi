<?php
namespace app\xq\controller;
use think\Db;
use think\Request;
use think\Controller;
use clt\Form;//表单
use app\xq\controller\Helper as Helper;//工具类
use think\Config;

class Memberyear extends Common{
    protected $modname; #模块名称
    protected $dao; #默认模型
    protected $fields; #字段
    protected $lfields;
    protected $controller; #控制器
    protected $log_mod; #日志模型
    protected $logid; #日志模型id
    protected $form;    #表单
    protected $helper;  #工具
    #初始化
    public function _initialize() {
        
        parent::_initialize();
        
        $this->controller = "memberyear";
        $this->modname = "分销年终奖";
        
        $this->moduleid = $this->mod[MODULE_NAME]; #模型id
        $this->logid = 2;
        
        $this->dao = db(MODULE_NAME); #当前模型
        $this->log_mod = db('logs');
        $this->form = new Form();
        $this->helper = new Helper();
        
        #初始化模版赋值
        $this->fields = $this->helper->getEditField($this->moduleid);#编辑字段
        $this->lfields = $this->helper->getLfield($this->moduleid);#列表字段
        
        #是否有子列表
        $mod_info = db("module")->where("name='{$this->controller}'")->find();
        if ($mod_info['olist']) {
            $this->olist = db($mod_info['olist']);
            $this->assign('olist', $mod_info['olist']);
        }
        $this->assign('moduleid', $this->moduleid);
        $this->assign ('fields',$this->fields);#新增编辑字段
        $this->assign('modname', $this->modname);
    }
    
    #年终
    public function index(){
        $ispost = input("ispost");
        #$host = Config::get('host');
        $host = "www.fengmi.com";
        if ($ispost) {
            $start = input("start");
            $end = input("end");
            
            if (!$start || !$end) {
                return mz_apierror("请选择日期");
            }
            $url = "http://".$host."/bee/Year/done";
            $params = array("start"=>$start, "end"=>$end);
            $res_json = mz_http_send($url, $params, "POST");
            
            
            if ($res_json) {
                $res = json_decode($res_json,1);
                $return = array();
                
                $tj_bottles = 0;
                $tj_award = 0;
                
                foreach ($res as $k=>$v) {
                    $tmp = array();
                    $tmp['uid'] = $v['uid'];
                    $tmp['nickname'] = $v['nickname'];
                    $tmp['level'] = "销售总监";
                    $tmp['result'] = $v['bottles'];
                    if ($v['bottles'] >= 100) {
                        #奖励金额
                        $reward = $v['bottles'] * 20;
                        $tmp['status'] = "<font style='color:green;'>达到条件</font>";
                        
                        $tj_bottles += $v['bottles'];
                        $tj_award += $reward;
                        
                    } else {
                        $reward = 0;
                        $tmp['status'] = "<font style='color:red;'>未满足</font>";
                    }
                    
                    #上级是否销售总监
                    $mem_info = db("members")->where("id='{$v['uid']}'")->find();
                    if ($mem_info['parent_id']) {
                        $parent = db("members")->where("id='{$mem_info['parent_id']}'")->find();
                        
                        if ($parent['level'] == 4) {
                            $tmp['reward'] = ($reward/10) * 9;
                            $tmp['father'] = $reward/10;
                        } else {
                            $tmp['reward'] = $reward;
                            $tmp['father'] = 0;
                        }
                    } else {
                        $tmp['reward'] = $reward;
                        $tmp['father'] = 0;
                    }
                    $return[] = $tmp;
                }
                $tmp = array();
                $tmp['uid'] = '统计';
                $tmp['nickname'] = '奖励总瓶数：'.$tj_bottles;
                $tmp['level'] = '总奖金：'.$tj_award;
                $tmp['result'] = "";
                $tmp['status'] = "";
                $tmp['reward'] = "";
                $tmp['father'] = "";
                $return[] = $tmp;
                
                return mz_apisuc("成功", $return);
                
            } else {
                return mz_apierror("没有数据");
            }
        }
        return $this->fetch();
    }
    
    #年终
    public function reward(){
        $ispost = input("ispost");
        #$host = Config::get('host');
        $host = "www.fengmi.com";
        if ($ispost) {
            $start = input("start");
            $end = input("end");
            
            if (!$start || !$end) {
                return mz_apierror("请选择日期");
            }
            $url = "http://".$host."/bee/Year/done";
            $params = array("start"=>$start, "end"=>$end);
            $res_json = mz_http_send($url, $params, "POST");
            
            if ($res_json) {
                $res = json_decode($res_json,1);
                $return = array();
                
                $tj_bottles = 0;
                $tj_award = 0;
                
                foreach ($res as $k=>$v) {
                    if ($v['bottles'] >= 10) {
                        #奖励金额
                        $reward = $v['bottles'] * 20;
                        
                        #上级是否销售总监
                        $mem_info = db("members")->where("id='{$v['uid']}'")->find();
                        if ($mem_info['parent_id']) {
                            $parent = db("members")->where("id='{$mem_info['parent_id']}'")->find();

                            if ($parent['level'] == 4) {
                                $myreward = ($reward/10) * 9;
                                $father = $reward/10;
                                
                                #增加金额
                                db("members")->where("id='{$mem_info['id']}'")->setInc("balance", $myreward);
                                db("members")->where("id='{$mem_info['id']}'")->setInc("total_balance", $myreward);
                                
                                db("members")->where("id='{$mem_info['parent_id']}'")->setInc("balance", $father);
                                db("members")->where("id='{$mem_info['parent_id']}'")->setInc("total_balance", $father);
                                
                                $balance1 = db("members")->where("id='{$mem_info['id']}'")->column("balance");
                                $balance2 = db("members")->where("id='{$mem_info['parent_id']}'")->column("balance");
                                
                                mz_flow($mem_info['id'], "", 5, "+".$myreward, "年终奖", $balance1[0]);
                                mz_flow($mem_info['parent_id'], "", 6, "+".$father, "年终奖感恩", $balance2[0]);
                                
                            } else {
                                $myreward = $reward;
                                
                                db("members")->where("id='{$mem_info['id']}'")->setInc("balance", $myreward);
                                db("members")->where("id='{$mem_info['id']}'")->setInc("total_balance", $myreward);
                                $balance1 = db("members")->where("id='{$mem_info['id']}'")->column("balance");
                                mz_flow($mem_info['id'], "", 5, "+".$myreward, "年终奖", $balance1[0]);
                            }
                        } else {
                            $myreward = $reward;
                            db("members")->where("id='{$mem_info['id']}'")->setInc("balance", $myreward);
                            db("members")->where("id='{$mem_info['id']}'")->setInc("total_balance", $myreward);
                            $balance1 = db("members")->where("id='{$mem_info['id']}'")->column("balance");
                            mz_flow($mem_info['id'], "", 5, "+".$myreward, "年终奖", $balance1[0]);
                        }
                    } 
                }
                
                return mz_apisuc("成功", $return);
                
            } else {
                return mz_apierror("失败，没有数据");
            }
        }
        return $this->fetch();
    }
    
}