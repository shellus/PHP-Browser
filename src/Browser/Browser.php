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
    /** @var string 临时路径 */
    protected $tmp_path = '';
    /** @var string Cookie存放文件 */
    protected $cookie_file = '';
    /** @var resource curl句柄 */
    protected $ch = null;
    /** @var array 缺省Header */
    protected $headers = [
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: zh-CN,zh;q=0.8',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Connection: keep-alive',
        //'Cache-Control: max-age=0',
        //'Content-Type: application/x-www-form-urlencoded',
    ];
    /** @var Crawler $crawler DOM对象 */
    protected $crawler = null;
    /** @var string 当前url（完整） */
    protected $currentUrl = '';
    /** @var string 当前页面body */
    protected $body = '';

    public function __construct($url = '')
    {
        $this -> tmp_path = __DIR__;
        $this -> cookie_file = $this -> tmp_path . '/cookie_' . getmypid() . '.txt';

        if(!is_writable($this -> tmp_path)){
            throw new \Exception();
        }

        touch($this -> cookie_file);

        $this -> ch = curl_init();

        curl_setopt($this -> ch, CURLOPT_COOKIEJAR, $this -> cookie_file); // 写到这
        curl_setopt($this -> ch, CURLOPT_COOKIEFILE, $this -> cookie_file); // 读取这
        curl_setopt($this -> ch, CURLOPT_FORBID_REUSE, false); // 长连接
        curl_setopt($this -> ch, CURLOPT_HEADER, 0); // 接收headers
        curl_setopt($this -> ch, CURLOPT_NOBODY, 0); // !不接收body
        curl_setopt($this -> ch, CURLOPT_RETURNTRANSFER, 1); // 不直接输出
        curl_setopt($this -> ch, CURLOPT_ENCODING, 'gzip'); // 自适应gzip
        curl_setopt($this -> ch, CURLOPT_FOLLOWLOCATION, 1); // 跟随跳转
        curl_setopt($this -> ch, CURLOPT_TIMEOUT, 30); // 5秒超时

        if ($url){
            $this -> go($url);
        }

    }

    function __destruct()
    {
        curl_close($this -> ch);
        unlink($this -> cookie_file);
    }

    /**
     * 网络请求
     * @param $url
     * @param string $method
     * @param null $data
     * @return mixed
     * @throws \Exception
     */
    public function request(&$url, $method = "GET", $data = null)
    {
        $extandHeaders = [];
        if($this -> currentUrl){
            $extandHeaders['Referer'] = $this -> currentUrl;
        }

        curl_setopt($this -> ch, CURLOPT_URL, $url);
        curl_setopt($this -> ch, CURLOPT_HTTPHEADER, $this -> headers + $extandHeaders);

        if ($method == "POST") {
            curl_setopt($this -> ch, CURLOPT_POST, 1);
            curl_setopt($this -> ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $body = curl_exec($this -> ch);

        if (curl_errno($this -> ch)) {
            throw new \Exception(curl_error($this -> ch));
        }
        $url = curl_getinfo($this -> ch, CURLINFO_EFFECTIVE_URL);
        return $body;
    }

    /**
     * 跳转，点击a链接、浏览器输入地址这种
     * @param $url
     */
    public function go($url)
    {
        $body = $this->request($url);
        $this->change($body, $url);
    }

    /**
     * 改变页面内容
     * @param $body
     * @param $url
     * @return bool
     */
    public function change($body, $url)
    {
        $this->body = $body;
        $this->currentUrl = $url;
        $this->crawler = new Crawler($body);
        return true;
    }


    /**
     * @param string $selector
     * @return Form
     */
    public function form($selector)
    {
        /** @var Crawler $node */
        $node = $this->crawler->filter($selector)->first();
        return new Form($node->getNode(0), $this->currentUrl, $node->attr('method') ?: 'GET', $node->attr('href'), $this);
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

    /**
     * @return Crawler
     */
    public function getCrawler()
    {
        return $this->crawler;
    }
}