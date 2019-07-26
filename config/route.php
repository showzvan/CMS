<?php

// =======用户自定义路由规则===========
return array(
    
    'url_route' => array(
        
        // 正则路由示例（生效中）
        'home/list-(\d+)' => 'home/list/index/scode/$1',
        'home/about-(\d+)' => 'home/about/index/scode/$1',
        'home/content-(\d+)' => 'home/content/index/id/$1',
        
        // 单页固定路由
        // 'home/about-us' => 'home/about/index/scode/1',
        
		// 列表页固定路由示例
        // 'home/news' => 'home/list/index/scode/2',
        
		// 详情页固定路由示例
        // 'home/content8' => 'home/content/index/id/8',
    
    )
);