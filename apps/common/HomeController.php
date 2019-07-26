<?php
namespace app\common;

use core\basic\Controller;
use core\basic\Config;

class HomeController extends Controller
{

    public function __construct()
    {
        // 自动缓存基础信息
        cache_lg();
        cache_config();
        
        // 手机自适应主题
        if ($this->config('open_wap')) {
            if ($this->config('wap_domain') && $this->config('wap_domain') == get_http_host()) {
                $this->setTheme(get_theme() . '/wap'); // 已绑域名并且一致则自动手机版本
            } elseif (is_mobile() && $this->config('wap_domain') && $this->config('wap_domain') != get_http_host()) {
                if (is_https()) {
                    $pre = 'https://';
                } else {
                    $pre = 'http://';
                }
                header('Location:' . $pre . $this->config('wap_domain') . URL); // 手机访问并且绑定了域名，但是访问域名不一致则跳转
            } elseif (is_mobile()) { // 其他情况手机访问则自动手机版本
                $this->setTheme(get_theme() . '/wap');
            } else { // 其他情况，电脑版本
                $this->setTheme(get_theme());
            }
        } else { // 未开启手机，则一律电脑版本
            $this->setTheme(get_theme());
        }
    }
}