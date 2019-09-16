<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:69:"E:\new\PHPTutorial\WWW\GIT\fengmi/app/admin\view\users\userGroup.html";i:1527570493;s:65:"E:\new\PHPTutorial\WWW\GIT\fengmi/app/admin\view\common\head.html";i:1527570493;s:65:"E:\new\PHPTutorial\WWW\GIT\fengmi/app/admin\view\common\foot.html";i:1527570493;}*/ ?>
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
<div class="admin-main layui-anim layui-anim-upbit">
    <fieldset class="layui-elem-field layui-field-title">
        <legend>会员组<?php echo lang('list'); ?></legend>
    </fieldset>
    <blockquote class="layui-elem-quote">
        <a href="<?php echo url('groupAdd'); ?>" class="layui-btn layui-btn-small">
            <?php echo lang('add'); ?>会员组
        </a>
    </blockquote>
    <table class="layui-table" id="list" lay-filter="list"></table>
</div>
<script type="text/javascript" src="__STATIC__/plugins/layui/layui.js"></script>


<script type="text/html" id="bom">
    {{d.bomlimit}}-{{d.toplimit}}
</script>
<script type="text/html" id="sort">
    <input name="{{d.level_id}}" data-id="{{d.level_id}}" class="list_order layui-input" value=" {{d.sort}}" size="10"/>
</script>
<script type="text/html" id="action">
    <a href="<?php echo url('groupEdit'); ?>?level_id={{d.level_id}}" class="layui-btn layui-btn-mini"><?php echo lang('edit'); ?></a>
    <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="del"><?php echo lang('del'); ?></a>
</script>
<script>
    layui.use('table', function() {
        var table = layui.table, $ = layui.jquery;
        table.render({
            id: 'user',
            elem: '#list',
            url: '<?php echo url("userGroup"); ?>',
            method: 'post',
            cols: [[
                {field: 'level_id', title: '<?php echo lang("id"); ?>', width: 80, fixed: true, sort: true},
                {field: 'level_name', title: '名称', width: 120},
                {field: 'bomlimit', title: '满足积分条件', width: 150,templet:'#bom'},
                {field: 'sort',align: 'center',title: '<?php echo lang("order"); ?>', width: 120,templet:'#sort',sort: true},
                {width: 160, align: 'center', toolbar: '#action'}
            ]]
        });
        table.on('tool(list)', function(obj){
            var data = obj.data;
            if(obj.event === 'del'){
                layer.confirm('您确定要删除该记录吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('groupDel'); ?>",{level_id:data.level_id},function(res){
                        layer.close(loading);
                        if(res.code==1){
                            layer.msg(res.msg,{time:1000,icon:1});
                            obj.del();
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    });
                    layer.close(index);
                });
            }
        });
        $('body').on('blur','.list_order',function() {
            var level_id = $(this).attr('data-id');
            var sort = $(this).val();
            $.post('<?php echo url("groupOrder"); ?>',{level_id:level_id,sort:sort},function(res){
                if(res.code==1){
                    layer.msg(res.msg,{time:1000,icon:1},function(){
                        location.href = res.url;
                    });
                }else{
                    layer.msg(res.msg,{time:1000,icon:2});
                }
            })
        })
    });
</script>