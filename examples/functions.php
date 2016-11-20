<?php
/**
 * Created by PhpStorm.
 * User: shellus
 * Date: 2016-11-20
 * Time: 22:01
 */

function env($key, $defult = ""){
    $env_str = file_get_contents(__DIR__ . '/.env');

    // 兼容windows换行符
    $env_str = str_replace("\r\n", "\n", $env_str);

    $env_str = trim($env_str);

    $env_arr = explode("\n", $env_str);

    $envs = [];
    foreach ($env_arr as $item){
        list($k, $v) = explode('=', $item);
        $envs[$k] = $v;
    }
    return key_exists($key, $envs) ? $envs[$key] : $defult;

}
