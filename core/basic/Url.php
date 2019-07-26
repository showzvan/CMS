<?php
/**
 * @copyright (C)2016-2099 Hnaoyun Inc.
 * @author XingMeng
 * @email hnxsh@foxmail.com
 * @date 2017年11月6日
 *  生成指定模块下控制器方法的跳转路径
 */
namespace core\basic;

class Url
{

    // 存储已经生成过的地址信息
    private static $urls = array();

    // 接收控制器方法完整访问路径，如：/home/Index/index /模块/控制器/方法/.. 路径，生成可访问地址
    public static function get($path, $addExt = true)
    {
        if (strpos($path, 'http') === 0 || ! $path) {
            return $path;
        }
        
        $path = trim_slash($path); // 去除两端斜线
        
        if (! isset(self::$urls[$path])) {
            $path_arr = explode('/', $path); // 地址数组
            if ($addExt) {
                $url_ext = Config::get('url_suffix'); // 地址后缀
            } else {
                $url_ext = '';
            }
            
            // 路由处理
            if (! ! $routes = Config::get('url_route')) {
                foreach ($routes as $key => $value) {
                    // 去除两端斜线
                    $value = trim_slash($value);
                    $key = trim_slash($key);
                    
                    // 替换原来正则为替换内容
                    if (preg_match_all('/\(.*?\)/', $key, $source)) {
                        foreach ($source[0] as $kk => $vk) {
                            $key = str_replace($vk, '$' . ($kk + 1), $key);
                        }
                    }
                    
                    // 替换原来替换内容为正则
                    if (preg_match_all('/\$([0-9]+)/', $value, $destination)) {
                        foreach ($destination[1] as $kv => $vv) {
                            $value = str_replace($destination[0][$kv], $source[0][$vv - 1], $value);
                        }
                    }
                    
                    // 执行匹配替换
                    if (preg_match('{' . $value . '$}i', $path)) {
                        $path = preg_replace('{' . $value . '$}i', $key, $path);
                    } elseif (preg_match('{' . $value . '\/}i', $path)) {
                        $path = preg_replace('{' . $value . '\/}i', $key . '/', $path);
                    }
                }
            }
            
            // 入口文件绑定匹配
            if (defined('URL_BLIND') && $path_arr[0] == M) {
                $cut_str = trim_slash(URL_BLIND);
            } else {
                $cut_str = '';
            }
            
            // 域名绑定处理匹配
            if (! ! $domains = Config::get('app_domain_blind')) {
                foreach ($domains as $key => $value) {
                    $value = trim_slash($value); // 去除两端斜线
                    if (strpos($path, $value . '/') === 0) {
                        // 域名绑定的长度大于入口绑定的长度，则替换裁剪长度
                        if ($cut_str && strpos($value, $cut_str) === false && strpos($cut_str, $value) === 0) {
                            $cut_str = $value;
                        }
                        $server_name = get_http_host();
                        if ($server_name != $key) { // 绑定的域名与当前域名不一致时，添加主机地址
                            $host = is_https() ? 'https://' . $key : 'http://' . $key;
                        } else {
                            $host = '';
                        }
                        break;
                    }
                }
            }
            
            // 执行URL简化
            if ($cut_str) {
                $path = substr($path, strlen($cut_str) + 1);
            }
            
            // 保存处理过的地址
            if ($path) {
                if ($path_arr[0] != M && $path_arr[0] == 'home') { // 对于后台处理home模块链接做特殊处理
                    $path = substr($path, 5);
                    if (Config::get('url_type') == 2) {
                        self::$urls[$path] = $host . SITE_DIR . '/' . $path . $url_ext;
                    } else {
                        self::$urls[$path] = $host . SITE_DIR . '/index.php/' . $path;
                    }
                } else {
                    if (is_rewrite()) {
                        self::$urls[$path] = $host . self::getPrePath() . '/' . $path . $url_ext;
                    } else {
                        self::$urls[$path] = $host . self::getPrePath() . '/' . $path;
                    }
                }
            } else {
                self::$urls[$path] = $host . self::getPrePath(); // 获取根路径前置地址
            }
        }
        return self::$urls[$path];
    }

    // 获取地址前缀
    private static function getPrePath()
    {
        if (is_rewrite()) {
            $pre_path = SITE_DIR;
        } else {
            $pre_path = $_SERVER["SCRIPT_NAME"];
        }
        return $pre_path;
    }
}