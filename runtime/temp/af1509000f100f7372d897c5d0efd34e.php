<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:58:"D:\phpstudy\WWW\pro_201805\xq/app/xq\view\members\log.html";i:1527570493;s:58:"D:\phpstudy\WWW\pro_201805\xq/app/xq\view\common\head.html";i:1527570493;s:58:"D:\phpstudy\WWW\pro_201805\xq/app/xq\view\common\foot.html";i:1527570493;}*/ ?>
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
    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li><a href='<?php echo url("index"); ?>'><?php echo lang('list'); ?></a></li>
            <li class="layui-this"><a href='<?php echo url("log"); ?>'><?php echo lang('loglist'); ?></a></li>
            <li><a href='<?php echo url("trash"); ?>'><?php echo lang('trash'); ?></a></li>
        </ul>
    </div>
    
    <!-- 筛选列表 -->
    <div class="demoTable layui-form">
        <div class="layui-form-item">
        <?php echo $html_str; ?>
        </div>
        <blockquote class="layui-elem-quote">
            <button class="layui-btn layui-btn-small" id="search" data-type="reload"><?php echo lang('search'); ?></button>
            <a href="<?php echo url('log'); ?>" class="layui-btn layui-btn-small"><?php echo lang('clear'); ?></a>
        </blockquote>
    </div>
    <!--列表-->
    <table class="layui-table" id="list" lay-filter="list">
    </table>
</div>
<!--字段js模版-->
<?php echo $js_tmp; ?>
<script type="text/javascript" src="__STATIC__/plugins/layui/layui.js"></script>


<script>
    layui.use(['table','laydate','element'], function() {
        var laydate = layui.laydate;
        var element = layui.element;
        <?php echo $js_date; ?>
        var table = layui.table, $ = layui.jquery;
        var tableIn = table.render({
            id: 'user',
            elem: '#list',
            url: '<?php echo url("log"); ?>',
            method: 'post',
            page: true,
            cols: [[
                {checkbox:true,fixed: true},
                //服务端赋值字段
                <?php echo $js_str; ?>
            ]],
            limit: 10 //每页默认显示的数量
            ,size: 'sm' //小尺寸的表格
        });
        //搜索
        $('#search').on('click', function() {
            <?php echo $js_val; ?>
            tableIn.reload({
                <?php echo $js_where; ?>
            });
        });
    });
</script>
</body>
</html>