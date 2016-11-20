<?php
/**
 * Created by PhpStorm.
 * User: shellus
 * Date: 2016-11-20
 * Time: 18:49
 */

namespace Browser;

use Symfony\Component\DomCrawler\Field\InputFormField;

class Form extends \Symfony\Component\DomCrawler\Form
{
    /** @var Browser $browser */
    protected $browser = null;

    public function __construct(\DOMElement $node, $currentUri, $method, $baseHref, $browser)
    {
        parent::__construct($node, $currentUri, $method, $baseHref);

        $this -> browser = $browser;
    }

    public function setText($name, $value){
        $this ->get($name) ->setValue($value);
    }
    
    public function addText($name, $value){
        $el = new \DOMElement('input');
        $dom = new \DOMDocument();
        $dom ->appendChild($el);
        $el -> setAttribute('name', $name);
        $el -> setAttribute('value', $value);
        $this -> set(new InputFormField($el));
    }

    public function submit(){
        $fields = $this -> all();

        $inputs = [];
        foreach ($fields as $field){
            $inputs[$field ->getName()] = $field -> getValue();
        }



        $ch = curl_init($this -> currentUri);


        $headers = [
            'Origin: https://console.tenxcloud.com',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: zh-CN,zh;q=0.8',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36',
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Cache-Control: max-age=0',
            'Referer: https://console.tenxcloud.com/login',
            'Connection: keep-alive',
        ];

        $cookies = [
            ['gr_user_id', '0a751fdd-c70b-4b3f-a4c9-f83b96004c91'],
            ['TENXDASHBOARD', 'dashboard-2'],
            ['connect.sid', 's%3AZwx-u2MbXNct577fv8v7eX2fesTB7zcj.aL8%2Bo9E9gEXgtYeYJSvhmtNEI9Dj%2F7IfsfuKSnOaQ84'],
            ['_ga', 'GA1.2.933827485.1479383272'],
            ['Hm_lvt_2a8acf24f1137fb8646971b8f18c98ee', '1479383273,1479392907,1479557211,1479632427'],
            ['Hm_lpvt_2a8acf24f1137fb8646971b8f18c98ee', '1479638568'],
            ['gr_session_id_e023a44d060e4f998ac33da01abf8ca6', '879edd10-8deb-4b4f-8475-7c58c1fb13c4'],
        ];

        foreach ($cookies as $cookie){
            $cs[] = implode('=', $cookie);
        }
        curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cs));

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0); // 接收headers
        curl_setopt($ch, CURLOPT_NOBODY, 0); // !不接收body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 不直接输出
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); // 自适应gzip
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 跟随跳转
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 5秒超时

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($inputs));

        $body = curl_exec($ch);

        if(curl_errno($ch)){
            throw new \Exception(curl_error($ch));
        }
        $url = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
        curl_close($ch);


        $this -> browser -> change($body, $url);


        return true;
    }
}