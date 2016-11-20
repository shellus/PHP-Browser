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
    /** @var Crawler $crawler */
    protected $crawler = null;

    protected $protocol = 'http';
    protected $domain = '';
    protected $port = 80;
    protected $form = [];
    protected $currentUrl;

    public function url(){
        return $this -> currentUrl;
    }

    /**
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
    public function go($url){
        $body = $this -> request($url);
        $this -> change($body, $url);
    }
    public function change($body, $url){
        $this -> currentUrl = $url;
        $this -> crawler = new Crawler($body);
        return true;
    }

    public function getCrawler(){
        return $this -> crawler;
    }
    /**
     * @return Form
     */
    public function form($selector){
        /** @var Crawler $node */
        $node = $this -> crawler -> filter($selector) -> first();
        return new Form($node -> getNode(0), $this -> currentUrl, $node -> attr('method') ?: 'GET', $node -> attr('href'), $this);

        $nodes = $this -> crawler -> filter($selector)->each(function (Crawler $node, $i) {
            return new Form($node -> getNode(0), $this -> currentUrl, $node -> attr('method') ?: 'GET', $node -> attr('href'), $this);
        });
        var_dump($nodes);die();
        return $nodes -> addNode();
    }


}