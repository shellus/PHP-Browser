<?php
/**
 * Created by PhpStorm.
 * User: shellus
 * Date: 2016-11-20
 * Time: 21:52
 */
require __DIR__ . '/functions.php';
require __DIR__ . '/../vendor/autoload.php';

class Shisuyun
{
    protected $initUrl = 'https://console.tenxcloud.com/login';
    protected $browser;

    public function init(){
        $this -> browser = new Browser\Browser($this -> initUrl);
    }
    public function login(){
        // 从首页html里面拿csrf token
        $csrf_key = $this -> browser -> getCrawler() -> filter('meta[name="csrf-param"]') -> attr('content');
        $csrf_value = $this -> browser -> getCrawler() -> filter('meta[name="csrf-token"]') -> attr('content');

        // 拿到form元素，并填写账号密码
        /** @var Browser\Form $from */
        $from = $this -> browser -> form('[id=signup]');

        $from -> setText('username', env('USERNAME'));

        $from -> setText('password', env('PASSWORD'));

        // 增加csrf的input
        $from -> addText($csrf_key, $csrf_value);

        // 提交表单
        $from -> submit();

        // 登陆后会跳转到首页，如果当前url不是首页，说明登录失败
        if($this -> browser -> getCurrentUrl() !== "https://console.tenxcloud.com/"){
            throw new Exception('login fail');
        };
    }
    public function getMoney()
    {
        // 本来想通过DOM拿到余额，结果发现DOM上的金额是js赋值的。。
        //$money = $browser -> getCrawler() -> filter('span[id="thisMonthconsumptionSum"]') ->text();

        // 获取账户信息的接口
        $infoUrl = 'https://console.tenxcloud.com/account/getconsumamount?_=' . time();

        // 调用浏览器的方法去GET，会自动加上Cookies，Referer之类的信息
        $json = $this -> browser -> request($infoUrl, 'GET');

        $arr = json_decode($json, true);

        $money = $arr[0]['balance'];

        return $money;
    }

}


$shisuyun = new Shisuyun();

$shisuyun -> init();

$shisuyun -> login();

var_dump($shisuyun -> getMoney());






