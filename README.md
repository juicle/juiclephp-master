JuiclePHP
=====

JuiclePHP For Coder


JuiclePHP框架,单文件框架
高性能 & 极度轻巧 & 組件化 & 伸縮性
单文件框架，减少IO操作，功能丝毫不减，速度性能是原来的4倍左右！

JuiclePHP框架，惰性加载机制，集成来自企业内部实用组件，融合国内外优秀php框架之精华来解决实际问题！

官方支持
git : https://github.com/juicle/juiclephp-master（最新版）

内置WebService,Api通用接口等应用模块，框架三种开发模式让你应对各种项目得心应手。


基本使用：


初始化页面
只要在入口文件里包含JuiclePHP初始化文件 



index.php

代码

include_once juicle/entry.php

访问index.php即可 hello JuiclePHP!



系统内置常用函数

        // 读取配置常量
        // setA(); 储存值
        // a(); 获取值
        // getConfig(); 获取组件配置
        // setConfig(); 设置组件配置
        // c(); 加载组件
        // setC(); 引入组件内部函数
        // import(); 引入类方法
        // importPath(); 引入类路径
        // Comp(); 加载组件同 c()是一样的
        // Cfg();获得组件配置项
        // generateUrl(); 生成URL 等同Comp('url.route')->createUrl($name, $params, $urlMode);
        // Module();中间件 控制器和Model之间穿梭，完成公共迭代功能。
        // Get(); $_GET的一层封装防止注入
        // Post(); $_Post的一层封装防止注入
        // Request(); $_Request的一层封装防止注入

        

