<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:60:"D:\phpstudy\WWW\pro_201805\xq/app/xq\view\members\index.html";i:1532414442;s:58:"D:\phpstudy\WWW\pro_201805\xq/app/xq\view\common\head.html";i:1527570493;s:58:"D:\phpstudy\WWW\pro_201805\xq/app/xq\view\common\foot.html";i:1527570493;}*/ ?>
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
            <li class="layui-this"><a href='<?php echo url("index"); ?>'><?php echo lang('list'); ?></a></li>
            <li><a href='<?php echo url("log"); ?>'><?php echo lang('loglist'); ?></a></li>
            <li><a href='<?php echo url("trash"); ?>'><?php echo lang('trash'); ?></a></li>
        </ul>
    </div>
    
    <!-- 筛选列表 -->
    <div class="demoTable layui-form">
        <div class="layui-form-item" id="search_box" >
            <?php echo $html_str; ?>
        </div>
        <blockquote class="layui-elem-quote">
            <button type="button" class="layui-btn layui-btn-small layui-btn-danger" id="delAll"><?php echo lang('delall'); ?></button>
            <button type="button" class="layui-btn layui-btn-small layui-btn-primary" id="export"><?php echo lang('export'); ?></button>
            <a href="<?php echo url('add'); ?>" class="layui-btn layui-btn-small layui-bg-blue"><?php echo lang('add'); ?></a>
            <button class="layui-btn layui-btn-small" id="search" data-type="reload" style="float:right;"><?php echo lang('search'); ?></button>
            <a href="<?php echo url('index'); ?>" class="layui-btn layui-btn-small" style="float:right;"><?php echo lang('clear'); ?></a>
        </blockquote>
    </div>
    <?php if($count_html1): ?>
        <table class="layui-table" lay-size="sm">
            <thead>
                <tr>
                    <th width="50">合计项</th>
                    <?php echo $count_html1; ?>
                </tr> 
            </thead>
            <tbody>
                <td>合计</td>
                <?php echo $count_html2; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <!--列表-->
    <table class="layui-table" id="list" lay-filter="list">
    </table>
</div>
<!--action 操作-->
<script type="text/html" id="action">
    <?php if($olist): ?>
    <a class="layui-btn layui-btn-mini" lay-event="olist" >清单列表</a>
    <?php endif; ?>
    <a href="<?php echo url('edit'); ?>?id={{d.id}}" class="layui-btn layui-btn-mini"><?php echo lang('edit'); ?></a>
    <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="del"><?php echo lang('del'); ?></a>
</script>
<!--字段js模版-->
<?php echo $js_tmp; ?>

<script type="text/javascript" src="__STATIC__/plugins/layui/layui.js"></script>


<script>
    layui.use(['table','laydate','element','layer'], function() {
        var laydate = layui.laydate;
        var element = layui.element;
        var layer = layui.layer;
        <?php echo $js_date; ?>
        var table = layui.table, $ = layui.jquery;
        var tableIn = table.render({
            id: 'user',
            elem: '#list',
            url: '<?php echo url("index"); ?>',
            method: 'post',
            page: true,
            cols: [[
                {checkbox:true,fixed: true},
                //服务端赋值字段
                <?php echo $js_str; ?>
                {width: 180, align: 'center', toolbar: '#action',fixed:'right'}
            ]
            ],
            done: function(res, curr, count){
                <?php echo $count_js; ?>
            },
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
        
        //导出
        $('#export').on('click', function() {
            <?php echo $js_val; ?>
            $.ajax({
                url:'<?php echo url("Export/index"); ?>',
                data:{<?php echo $js_ewhere; ?>,id:<?php echo $moduleid; ?>},
                type:"post",
                dataType:"json",
                success:function(data){
                    location.href = '<?php echo url("Export/index"); ?>&from=1';
                },
                error:function(data){
                    location.href = '<?php echo url("Export/index"); ?>&from=1';
                }
            });
        });
        
        //表格事件
        table.on('tool(list)', function(obj) {
            var data = obj.data;
            if (obj.event === 'del') {
                layer.confirm('您确定要删除该数据吗？', function(index){
                    var loading = layer.load(1, {shade: [0.1, '#fff']});
                    $.post("<?php echo url('listDel'); ?>",{id:data.id},function(res){
                        layer.close(loading);
                        if(res.code===1){
                            layer.msg(res.msg,{time:1000,icon:1});
                            tableIn.reload();
                        }else{
                            layer.msg('操作失败！',{time:1000,icon:2});
                        }
                    });
                    layer.close(index);
                });
            }
        });

        $('#delAll').click(function(){
            layer.confirm('确认要删除选中信息吗？', {icon: 3}, function(index) {
                layer.close(index);
                var checkStatus = table.checkStatus('user'); //test即为参数id设定的值
                var ids = [];
                $(checkStatus.data).each(function (i, o) {
                    ids.push(o.id);
                });
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.post("<?php echo url('delall'); ?>", {ids: ids}, function (data) {
                    layer.close(loading);
                    if (data.code === 1) {
                        layer.msg(data.msg, {time: 1000, icon: 1});
                        tableIn.reload();
                    } else {
                        layer.msg(data.msg, {time: 1000, icon: 2});
                    }
                });
            });
        });
    });
</script>
</body>
</html>
