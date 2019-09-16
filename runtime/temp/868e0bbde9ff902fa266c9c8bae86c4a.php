<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:68:"E:\new\PHPTutorial\WWW\GIT\fengmi/app/admin\view\auth\adminForm.html";i:1527570493;s:65:"E:\new\PHPTutorial\WWW\GIT\fengmi/app/admin\view\common\head.html";i:1527570493;s:65:"E:\new\PHPTutorial\WWW\GIT\fengmi/app/admin\view\common\foot.html";i:1527570493;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo config('sys_name'); ?>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="__STATIC__/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="__ADMIN__/css/global.css" media="all">
    <link rel="stylesheet" href="__STATIC__/common/css/font.css" media="all">
</head>
<body class="skin-0">
<div class="admin-main layui-anim layui-anim-upbit" ng-app="hd" ng-controller="ctrl">
    <fieldset class="layui-elem-field layui-field-title">
        <legend><?php echo $title; ?></legend>
    </fieldset>
    <form class="layui-form layui-form-pane">
        <div class="layui-form-item">
            <label class="layui-form-label">所属用户组</label>
            <div class="layui-input-4">
                <select name="group_id" lay-verify="required" ng-model="selected" ng-options="v.group_id as v.title for v in group track by v.group_id">
                    <option value="">请选择用户组</option>
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><?php echo lang('username'); ?></label>
            <div class="layui-input-4">
                <input type="text" name="username" ng-model="field.username" lay-verify="required" placeholder="<?php echo lang('pleaseEnter'); ?>登录用户名" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">
                用户名必须是以字母开头，数字、符号组合。
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"><?php echo lang('pwd'); ?></label>
            <div class="layui-input-4">
                <input type="password" name="pwd" placeholder="<?php echo lang('pleaseEnter'); ?>登录密码" <?php if(ACTION_NAME == 'adminadd'): ?>lay-verify="required"<?php endif; ?> class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">
                密码必须大于6位，小于15位.
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">真实姓名</label>
            <div class="layui-input-4">
                <input type="text" name="realname" ng-model="field.realname" placeholder="<?php echo lang('pleaseEnter'); ?>真实姓名" class="layui-input">
            </div>
        </div>
        <!--
        <div class="layui-form-item">
            <label class="layui-form-label"><?php echo lang('email'); ?></label>
            <div class="layui-input-4">
                <input type="text" name="email" ng-model="field.email" lay-verify="email" placeholder="<?php echo lang('pleaseEnter'); ?>用户邮箱" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">
                用于密码找回，请认真填写.
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><?php echo lang('tel'); ?></label>
            <div class="layui-input-4">
                <input type="text" name="tel" ng-model="field.tel" lay-verify="phone" value="" placeholder="<?php echo lang('pleaseEnter'); ?>手机号" class="layui-input">
            </div>
        </div>
        -->
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="button" class="layui-btn" lay-submit="" lay-filter="submit"><?php echo lang('submit'); ?></button>
                <a href="<?php echo url('adminList'); ?>" class="layui-btn layui-btn-primary"><?php echo lang('back'); ?></a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="__STATIC__/plugins/layui/layui.js"></script>


<script src="__STATIC__/common/js/angular.min.js"></script>
<script>
    var m = angular.module('hd',[]);
    m.controller('ctrl',['$scope',function($scope) {
        $scope.field = '<?php echo $info; ?>'!='null'?<?php echo $info; ?>:{group_id:'',username:'',email:'',tel:''};
		console.log($scope.field);
        $scope.group = <?php echo $authGroup; ?>;
        $scope.selected = <?php echo $selected; ?>;
        layui.use(['form', 'layer'], function () {
            var form = layui.form, layer = layui.layer,$= layui.jquery;
			form.render();
            form.on('submit(submit)', function (data) {
                loading =layer.load(1, {shade: [0.1,'#fff']});
                data.field.admin_id = $scope.field.admin_id;
                $.post("", data.field, function (res) {
                    layer.close(loading);
                    if (res.code > 0) {
                        layer.msg(res.msg, {time: 1800, icon: 1}, function () {
                            location.href = res.url;
                        });
                    } else {
                        layer.msg(res.msg, {time: 1800, icon: 2});
                    }
                });
            })
        });
    }]);
</script>