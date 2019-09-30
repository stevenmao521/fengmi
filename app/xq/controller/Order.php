<?php
namespace app\xq\controller;
use think\Db;
use think\Request;
use think\Controller;
use clt\Form;//表单
use app\xq\controller\Helper as Helper;//工具类

class Order extends Common{
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
        
        $this->controller = "order";
        $this->modname = "订单";
        
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
    
    #领用列表
    public function orderdetail(){
        #筛选字段
        $post = input("request.");
        $parentid = $post['id'];
        #列表
        $page =input('page')?input('page'):1;
        $pageSize =input('limit')?input('limit'):config('pageSize');
        $list = db("orderdetail")
            ->where("istrash=0 and oid='{$parentid}'")
            ->order('id desc')
            ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
            ->toArray();

        #时间转换
        $lfields = $this->lfields;
        if ($lfields) {
            foreach ($lfields as $k=>$v) {
                if ($v['type'] == 'datetime') {
                    $list['data'] = mz_formattime($list['data'], $v['field'], 2);
                }
            }
        }
        $this->assign("list", $list);
        return $this->fetch();
    }
    
    #发货
    public function isSend() {
        $map['id'] = input('post.id');
        #判断当前状态情况
        $info = $this->dao->where($map)->find();
        
        if ($info['haspay'] != 1) {
            return ['code'=>0,'msg'=>'此订单还未付款，不能发货！'];
        } else {
            if (!$info['express'] || !$info['expresscode']) {
                return ['code'=>0,'msg'=>'请再详情里编辑快递信息再发货'];
            }
        }
        
        if ($info['status'] != 4) {
            $data['issend'] = 2;
            $data['status'] = 3;
            $data['sendtime'] = time();
            #自动收货时间
            $data['autotime'] = time() + 3600*24*5;
            
            $r = $this->dao->where($map)->setField($data);
            if ($r) {
                #日志
                $this->helper->insLog($this->moduleid, 'issend', session('aid'), session('username'), $map['id']);
                return ['code'=>1,'msg'=>'成功！'];
            } else {
                return ['code'=>0,'msg'=>'失败！'];
            }
        } else {
            return ['code'=>0,'msg'=>'此订单已完成'];
        }
    }
    
}