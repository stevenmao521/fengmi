<?php
namespace app\xq\controller;
use think\Db;
use think\Request;
use think\Controller;
use clt\Form;//表单
use app\xq\controller\Helper as Helper;//工具类

class Cashorder extends Common{
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
        
        $this->controller = "cashorder";
        $this->modname = "提现管理";
        
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
    
    #打款
    public function isCash() {
        $map['id'] = input('post.id');
        #判断当前状态情况
        $info = $this->dao->where($map)->find();
        
        $data['status'] = 2;
        $data['dotime'] = time();
        $r = $this->dao->where($map)->setField($data);
        
        if ($r) {
            #10位老师提现1%
            #日志
            $this->helper->insLog($this->moduleid, 'iscash', session('aid'), session('username'), $map['id']);
            return ['code'=>1,'msg'=>'成功！'];
        } else {
            return ['code'=>0,'msg'=>'失败！'];
        }
        
    }
    
}