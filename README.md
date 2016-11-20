# PHP-Browser
PHP模拟浏览器，简化你的数据抓取和WEB应用测试工作

### 特点
 - 自动处理Cookie，Referer、自动跟随跳转，就像你的浏览器那样
 - 浏览器的请求方法，轻松应付Form提交掺杂ajax提交的问题
 - 基于symfony/dom-crawler的css选择器，非常简单的拿到你想要的数据


### 实例代码

```php

$initUrl = 'https://console.tenxcloud.com/login';

$browser = new Browser\Browser();

$browser -> go ($initUrl);

$from = $browser -> form('#form-login');

$from -> setText('username', 'USERNAME');

$from -> setText('password', 'PASSWORD');

// 提交表单
$from -> submit();

// 获取跳转后的url
// $browser -> url()

// 获取到该账户的余额
$money = $browser -> crawler() -> filter('#money') ->text();

```
更多例子请查阅`examples`文件夹

### 本项目参考了以下页面
 - php curl文档 ： http://php.net/manual/en/function.curl-getinfo.php