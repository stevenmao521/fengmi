<?php
return [
    #微信小程序配置
    'wechat' => [
        'wx_appid'=>'wx44cc19b5d035c357',
        'wx_appsec'=>'894c0df9b5c73062328f83a846ca6117',
        #微信登录凭证校验接口
        'wx_loginurl'=>'https://api.weixin.qq.com/sns/jscode2session'
    ],
    #微信支付配置
    'wxpay' => [
        'mch_id'=>'1507466101',
        #统一下单接口
        'pay_url'=>'https://api.mch.weixin.qq.com/pay/unifiedorder',
        'api_sec'=>'Jcnetwork20140606Jcmap2018Jcpass',
        'notify_url'=>'http://http://fmall.yuntim.cn/extend/wxpay/callback.php',
        'after_url'=>'http://http://fmall.yuntim.cn/extend/wxpay/callafter.php'
    ]
];