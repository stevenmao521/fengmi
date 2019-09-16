<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:62:"D:\phpstudy\WWW\pro_201805\xq/app/admin\view\module\field.html";i:1532412896;s:61:"D:\phpstudy\WWW\pro_201805\xq/app/admin\view\common\head.html";i:1527570493;s:61:"D:\phpstudy\WWW\pro_201805\xq/app/admin\view\common\foot.html";i:1527570493;}*/ ?>
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
        <legend>字段<?php echo lang('list'); ?></legend>
    </fieldset>
    <blockquote class="layui-elem-quote">
        <a href="<?php echo url('fieldAdd',array('moduleid'=>input('id'))); ?>" class="layui-btn layui-btn-small"><?php echo lang('add'); ?><?php echo lang('field'); ?></a>
        <a href="<?php echo url('index'); ?>" class="layui-btn layui-btn-small layui-btn-primary pull-right">模型列表</a>
    </blockquote>
    <table class="layui-table" id="list" lay-filter="list"></table>
</div>
<script type="text/javascript" src="__STATIC__/plugins/layui/layui.js"></script>


<script type="text/html" id="isEdit">
    {{# if(d.isedit==1){ }}
    <a href="javascript:" class="icon icon-checkmark green" lay-event="edityes"></a>
    {{# }else{  }}
    <a href="javascript:" class="icon icon-cross red" lay-event="edityes"></a>
    {{# } }}
</script>
<script type="text/html" id="isList">
    {{# if(d.islist==1){ }}
    <a href="javascript:" class="icon icon-checkmark green" lay-event="listyes"></a>
    {{# }else{  }}
    <a href="javascript:" class="icon icon-cross red" lay-event="listyes"></a>
    {{# } }}
</script>
<script type="text/html" id="isSel">
    {{# if(d.issel==1){ }}
    <a href="javascript:" class="icon icon-checkmark green" lay-event="selyes"></a>
    {{# }else{  }}
    <a href="javascript:" class="icon icon-cross red" lay-event="selyes"></a>
    {{# } }}
</script>
<script type="text/html" id="isSort">
    {{# if(d.issort==1){ }}
    <a href="javascript:" class="icon icon-checkmark green" lay-event="sortyes"></a>
    {{# }else{  }}
    <a href="javascript:" class="icon icon-cross red" lay-event="sortyes"></a>
    {{# } }}
</script>
<script type="text/html" id="isCount">
    {{# if(d.iscount==1){ }}
    <a href="javascript:" class="icon icon-checkmark green" lay-event="countyes"></a>
    {{# }else{  }}
    <a href="javascript:" class="icon icon-cross red" lay-event="countyes"></a>
    {{# } }}
</script>
<script type="text/html" id="isHide">
    {{# if(d.ishide==1){ }}
    <a href="javascript:" class="icon icon-checkmark green" lay-event="hideyes"></a>
    {{# }else{  }}
    <a href="javascript:" class="icon icon-cross red" lay-event="hideyes"></a>
    {{# } }}
</script>

<script type="text/html" id="action">
    {{# if(d.disable==1){ }}
        <a href="javascript:" class="layui-btn layui-btn-mini layui-btn-danger" lay-event="stateyes">已禁用</a>
    {{# }else if(d.disable==2){  }}
        <a href="#" class="layui-btn layui-btn-mini layui-btn-disabled">已禁用</a>
    {{# }else{  }}
        <a href="javascript:" class="layui-btn layui-btn-mini layui-btn-warm" lay-event="stateyes">已启用</a>
    {{# } }}
    <a href="<?php echo url('fieldEdit'); ?>?moduleid={{d.moduleid}}&id={{d.id}}" class="layui-btn layui-btn-mini"><?php echo lang('edit'); ?></a>

    {{# if(d.delStatus==1){ }}
        <a href="#" class="layui-btn layui-btn-mini layui-btn-disabled">删除</a>
    {{# }else{  }}
        <a href="#" class="layui-btn layui-btn-mini layui-btn-danger" lay-event="del">删除</a>
    {{# } }}
</script>
<script type="text/html" id="order">
    <input name="{{d.id}}" data-id="{{d.id}}" class="list_order layui-input order" value=" {{d.listorder}}" size="10" />
</script>
<script type="text/html" id="width">
    <input name="{{d.id}}" data-id="{{d.id}}" class="list_order layui-input width" value="{{d.width}}" size="10"/>
</script>
<script type="text/html" id="event">
    <input name="{{d.id}}" data-id="{{d.id}}" class="list_order layui-input event" value="{{d.event}}" size="10"/>
</script>
<script>
    layui.use('table', function() {
        var table = layui.table, $ = layui.jquery;
        var tableIn=table.render({
            elem: '#list',
            url: '<?php echo url("field"); ?>',
            where: { //设定异步数据接口的参数
                id: '<?php echo input("id"); ?>'
            },
            method: 'post',
            cols: [[
                {field: 'field', title: '字段名', width: 120,fixed: true},
                {field: 'name', title: '别名', width: 100},
                {field: 'type', title: '字段类型', width: 100},
                {field: 'isedit', align: 'center', title: '编辑', width: 80, toolbar: '#isEdit'},
                {field: 'islist', align: 'center', title: '列表', width: 80, toolbar: '#isList'},
                {field: 'issel', align: 'center', title: '筛选', width: 80, toolbar: '#isSel'},
                {field: 'issort', align: 'center', title: '排序', width: 80, toolbar: '#isSort'},
                {field: 'iscount', align: 'center', title: '统计', width: 80, toolbar: '#isCount'},
                {field: 'ishide', align: 'center', title: '隐藏', width: 80, toolbar: '#isHide'},
                {field: 'event', align: 'center', title: '绑定事件', width: 80, toolbar: '#event'},
                {field: 'width', title: '列表宽', width: 100, templet: '#width'},
                {field: 'listorder', title: '<?php echo lang("order"); ?>', width: 100, sort: true,templet: '#order'},
                {width: 190, align: 'center', toolbar: '#action'}
            ]],
            size: 'sm'
        });
        table.on('tool(list)', function(obj) {
            var data = obj.data;
            if (obj.event === 'stateyes') {
                loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post('<?php echo url("fieldStatus"); ?>', {id: data.id}, function () {
                    window.location.href = "<?php echo url('field'); ?>?id=<?php echo input('id'); ?>"
                });
            }else if(obj.event === 'del'){
                layer.confirm('你确定要删除该字段吗', function(index){
                    $.post("<?php echo url('fieldDel'); ?>",{id:data.id},function(res){
                        if(res.code==1){
                            layer.msg(res.msg,{time:1000,icon:1});
                            obj.del();
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    });
                    layer.close(index);
                });
            }else if(obj.event === 'edityes') {
                loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post('<?php echo url("editStatus"); ?>', {id: data.id}, function () {
                    window.location.href = "<?php echo url('field'); ?>?id=<?php echo input('id'); ?>"
                });
            }else if(obj.event === 'listyes') {
                loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post('<?php echo url("listStatus"); ?>', {id: data.id}, function () {
                    window.location.href = "<?php echo url('field'); ?>?id=<?php echo input('id'); ?>"
                });
            }else if(obj.event === 'selyes') {
                loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post('<?php echo url("selStatus"); ?>', {id: data.id}, function () {
                    window.location.href = "<?php echo url('field'); ?>?id=<?php echo input('id'); ?>"
                });
            }else if(obj.event === 'sortyes') {
                loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post('<?php echo url("sortStatus"); ?>', {id: data.id}, function () {
                    window.location.href = "<?php echo url('field'); ?>?id=<?php echo input('id'); ?>"
                });
            }else if(obj.event === 'countyes') {
                loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post('<?php echo url("countStatus"); ?>', {id: data.id}, function () {
                    window.location.href = "<?php echo url('field'); ?>?id=<?php echo input('id'); ?>"
                });
            }else if(obj.event === 'hideyes') {
                loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post('<?php echo url("hideStatus"); ?>', {id: data.id}, function () {
                    window.location.href = "<?php echo url('field'); ?>?id=<?php echo input('id'); ?>"
                });
            }
        });
        $('body').on('blur','.order',function() {
            var id = $(this).attr('data-id');
            var listorder = $(this).val();
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.post('<?php echo url("listOrder"); ?>',{id:id,listorder:listorder,moduleid:"<?php echo input('id'); ?>"},function(res){
                layer.close(loading);
                if(res.code===1){
                    layer.msg(res.msg,{time:1000,icon:1});
                    tableIn.reload();
                }else{
                    layer.msg(res.msg,{time:1000,icon:2});
                }
            })
        });
        $('body').on('blur','.width',function() {
            var id = $(this).attr('data-id');
            var width = $(this).val();
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.post('<?php echo url("editWidth"); ?>',{id:id,width:width,moduleid:"<?php echo input('id'); ?>"},function(res){
                layer.close(loading);
                if(res.code===1){
                    layer.msg(res.msg,{time:1000,icon:1});
                    tableIn.reload();
                }else{
                    layer.msg(res.msg,{time:1000,icon:2});
                }
            })
        });
        $('body').on('blur','.event',function() {
            var id = $(this).attr('data-id');
            var event = $(this).val();
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.post('<?php echo url("editEvent"); ?>',{id:id,event:event,moduleid:"<?php echo input('id'); ?>"},function(res){
                layer.close(loading);
                if(res.code===1){
                    layer.msg(res.msg,{time:1000,icon:1});
                    tableIn.reload();
                }else{
                    layer.msg(res.msg,{time:1000,icon:2});
                }
            })
        })
    });
</script>