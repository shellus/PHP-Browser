<?php
/**
 * Created by PhpStorm.
 * User: shellus
 * Date: 2016-11-20
 * Time: 21:52
 */
require './functions.php';
require '../vendor/autoload.php';


$initUrl = 'https://console.tenxcloud.com/login';

$browser = new Browser\Browser();

$browser -> go ($initUrl);

// 从首页html里面拿csrf token
$csrf_key = $browser -> crawler() -> filter('meta[name="csrf-param"]') -> attr('content');
$csrf_value = $browser -> crawler() -> filter('meta[name="csrf-token"]') -> attr('content');

// 拿到form元素，并填写账号密码
/** @var Browser\Form $from */
$from = $browser -> form('[id=signup]');

$from -> setText('username', env('USERNAME'));

$from -> setText('password', env('PASSWORD'));

// 增加csrf的input
$from -> addText($csrf_key, $csrf_value);

// 提交表单
// browser window.location.href(xxx); FOLLOW LOCATION
$from -> submit();

// 登陆后会跳转到首页，如果当前url不是首页，说明登录失败
if($browser -> url() !== "https://console.tenxcloud.com/"){
    throw new Exception('login fail');
};

// 本来想通过DOM拿到余额，结果发现DOM上的金额是js赋值的。。
//$money = $browser -> getCrawler() -> filter('span[id="thisMonthconsumptionSum"]') ->text();

// 获取账户信息的接口
$infoUrl = 'https://console.tenxcloud.com/account/getconsumamount?_=' . time();

// 调用浏览器的方法去GET，会自动加上Cookies，Referer之类的信息
$json = $browser -> request($infoUrl, 'GET');

$arr = json_decode($json, true);

$money = $arr[0]['balance'];

var_dump($money);