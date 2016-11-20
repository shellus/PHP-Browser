<?php
/**
 * Created by PhpStorm.
 * User: shellus
 * Date: 2016-11-20
 * Time: 18:48
 */

namespace Browser;


use Symfony\Component\DomCrawler\Crawler;

class Browser
{
    /** @var Crawler $crawler DOM对象 */
    protected $crawler = null;

    // TODO 解析url结构，虽然暂时没什么卵用
    protected $protocol = 'http';
    protected $domain = '';
    protected $port = 80;

    protected $currentUrl = '';
    protected $body = '';

    /**
     * 返回当前url
     * @return string
     */
    public function url(){
        return $this -> currentUrl;
    }

    /**
     * 返回body内容
     * @return string
     */
    public function body(){
        return $this -> body;
    }
    public function title(){
        return $this -> crawler -> filter('title') -> text();
    }
    /**
     * 刷新当前页面, 如果当前页面是POST而来，会出错
     */
    public function reload(){
        $this -> change($this -> request($this -> currentUrl), $this -> currentUrl);
        // TODO POST刷新页面处理
    }

    /**
     * 网络请求
     * @param $url
     * @param string $method
     * @param null|array $data
     * @return string
     */
    public function request($url, $method = "GET", $data = null){
        $body = '';

        // TODO 请求时带上Cookie、Referer
        // TODO 请求并更新当前url、Cookie
        // TODO 如果响应为跳转，当前url也应该更新为跳转后的url
        return $body;
    }


    /**
     * 监听事件
     * @param $event
     * @param $callback
     */
    public function hook($event, $callback){
        // TODO 例如监听网络请求，例如监听到404就记录日志

    }

    /**
     * 跳转，点击a链接这种
     * @param $url
     */
    public function go($url){
        $body = $this -> request($url);
        $this -> change($body, $url);
    }

    /**
     * 改变页面内容
     * @param $body
     * @param $url
     * @return bool
     */
    public function change($body, $url){
        $this -> body = $body;
        $this -> currentUrl = $url;
        $this -> crawler = new Crawler($body);
        return true;
    }

    /**
     * @return Crawler
     */
    public function crawler(){
        return $this -> crawler;
    }
    /**
     * @param string $selector
     * @return Form
     */
    public function form($selector){
        /** @var Crawler $node */
        $node = $this -> crawler -> filter($selector) -> first();
        return new Form($node -> getNode(0), $this -> currentUrl, $node -> attr('method') ?: 'GET', $node -> attr('href'), $this);
    }
}