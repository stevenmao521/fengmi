<?php
return array (
  'nickname' => 
  array (
    'id' => 18,
    'iscount' => 0,
    'moduleid' => 1,
    'field' => 'nickname',
    'name' => '昵称',
    'tips' => '',
    'required' => 0,
    'minlength' => 0,
    'maxlength' => 0,
    'pattern' => 'defaul',
    'errormsg' => '',
    'class' => 'uname',
    'type' => 'text',
    'setup' => 'array (
  \'default\' => \'\',
  \'ispassword\' => \'0\',
  \'fieldtype\' => \'varchar\',
)',
    'ispost' => 0,
    'unpostgroup' => '',
    'listorder' => 1,
    'status' => 1,
    'issystem' => 0,
    'islist' => 1,
    'issel' => 1,
    'isedit' => 1,
    'issort' => 0,
    'ishide' => 0,
    'width' => 100,
    'event' => '',
  ),
  'openid' => 
  array (
    'id' => 10,
    'iscount' => 0,
    'moduleid' => 1,
    'field' => 'openid',
    'name' => 'openID',
    'tips' => '',
    'required' => 0,
    'minlength' => 0,
    'maxlength' => 0,
    'pattern' => 'defaul',
    'errormsg' => '',
    'class' => 'openid',
    'type' => 'text',
    'setup' => 'array (
  \'default\' => \'\',
  \'ispassword\' => \'0\',
  \'fieldtype\' => \'varchar\',
)',
    'ispost' => 0,
    'unpostgroup' => '',
    'listorder' => 2,
    'status' => 1,
    'issystem' => 0,
    'islist' => 1,
    'issel' => 1,
    'isedit' => 1,
    'issort' => 0,
    'ishide' => 0,
    'width' => 100,
    'event' => '',
  ),
  'avatarurl' => 
  array (
    'id' => 11,
    'iscount' => 0,
    'moduleid' => 1,
    'field' => 'avatarurl',
    'name' => '头像',
    'tips' => '',
    'required' => 0,
    'minlength' => 0,
    'maxlength' => 0,
    'pattern' => 'defaul',
    'errormsg' => '',
    'class' => 'avatarurl',
    'type' => 'text',
    'setup' => 'array (
  \'default\' => \'\',
  \'ispassword\' => \'0\',
  \'fieldtype\' => \'varchar\',
)',
    'ispost' => 0,
    'unpostgroup' => '',
    'listorder' => 3,
    'status' => 1,
    'issystem' => 0,
    'islist' => 0,
    'issel' => 0,
    'isedit' => 0,
    'issort' => 0,
    'ishide' => 0,
    'width' => 100,
    'event' => '',
  ),
  'json_data' => 
  array (
    'id' => 12,
    'iscount' => 0,
    'moduleid' => 1,
    'field' => 'json_data',
    'name' => 'json字段',
    'tips' => '',
    'required' => 0,
    'minlength' => 0,
    'maxlength' => 0,
    'pattern' => 'defaul',
    'errormsg' => '',
    'class' => 'json_data',
    'type' => 'textarea',
    'setup' => 'array (
  \'fieldtype\' => \'mediumtext\',
  \'default\' => \'\',
)',
    'ispost' => 0,
    'unpostgroup' => '',
    'listorder' => 4,
    'status' => 1,
    'issystem' => 0,
    'islist' => 0,
    'issel' => 0,
    'isedit' => 0,
    'issort' => 0,
    'ishide' => 0,
    'width' => 100,
    'event' => '',
  ),
  'skey' => 
  array (
    'id' => 16,
    'iscount' => 0,
    'moduleid' => 1,
    'field' => 'skey',
    'name' => '三方session',
    'tips' => '',
    'required' => 0,
    'minlength' => 0,
    'maxlength' => 0,
    'pattern' => 'defaul',
    'errormsg' => '',
    'class' => 'skey',
    'type' => 'text',
    'setup' => 'array (
  \'default\' => \'\',
  \'ispassword\' => \'0\',
  \'fieldtype\' => \'varchar\',
)',
    'ispost' => 0,
    'unpostgroup' => '',
    'listorder' => 5,
    'status' => 1,
    'issystem' => 0,
    'islist' => 0,
    'issel' => 0,
    'isedit' => 0,
    'issort' => 0,
    'ishide' => 0,
    'width' => 100,
    'event' => '',
  ),
  'lasttime' => 
  array (
    'id' => 17,
    'iscount' => 0,
    'moduleid' => 1,
    'field' => 'lasttime',
    'name' => '最新登录时间',
    'tips' => '',
    'required' => 0,
    'minlength' => 0,
    'maxlength' => 0,
    'pattern' => 'defaul',
    'errormsg' => '',
    'class' => 'lasttime',
    'type' => 'datetime',
    'setup' => '',
    'ispost' => 0,
    'unpostgroup' => '',
    'listorder' => 16,
    'status' => 1,
    'issystem' => 0,
    'islist' => 0,
    'issel' => 0,
    'isedit' => 0,
    'issort' => 0,
    'ishide' => 0,
    'width' => 100,
    'event' => '',
  ),
  'isfb' => 
  array (
    'id' => 1,
    'iscount' => 0,
    'moduleid' => 1,
    'field' => 'isfb',
    'name' => '是否禁用',
    'tips' => '',
    'required' => 0,
    'minlength' => 0,
    'maxlength' => 0,
    'pattern' => 'defaul',
    'errormsg' => '',
    'class' => 'isfb',
    'type' => 'radio',
    'setup' => 'array (
  \'options\' => \'启用|0
禁用|1\',
  \'fieldtype\' => \'varchar\',
  \'numbertype\' => \'1\',
  \'default\' => \'1\',
)',
    'ispost' => 0,
    'unpostgroup' => '',
    'listorder' => 17,
    'status' => 1,
    'issystem' => 0,
    'islist' => 0,
    'issel' => 0,
    'isedit' => 1,
    'issort' => 0,
    'ishide' => 0,
    'width' => 100,
    'event' => '',
  ),
  'createtime' => 
  array (
    'id' => 21,
    'iscount' => 0,
    'moduleid' => 1,
    'field' => 'createtime',
    'name' => '创建时间',
    'tips' => '',
    'required' => 1,
    'minlength' => 0,
    'maxlength' => 0,
    'pattern' => 'date',
    'errormsg' => '',
    'class' => 'createtime',
    'type' => 'datetime',
    'setup' => '',
    'ispost' => 1,
    'unpostgroup' => '',
    'listorder' => 97,
    'status' => 1,
    'issystem' => 1,
    'islist' => 1,
    'issel' => 1,
    'isedit' => 0,
    'issort' => 0,
    'ishide' => 0,
    'width' => 130,
    'event' => '',
  ),
);
?>