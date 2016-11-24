JuiclePHP
=====

JuiclePHP For Coder


JuiclePHP框架,单文件框架
高性能 & 极度轻巧 & 組件化 & 伸縮性
单文件框架，减少IO操作，功能丝毫不减，速度性能是原来的4倍左右！

JuiclePHP框架，惰性加载机制，源码不足100kb，面向开发者，集成来自企业内部实用组件，融合国内外优秀php框架之精华来解决实际问题！

官方支持
git : https://github.com/juicle/juiclephp-master（最新版）

内置WebService,Api通用接口等应用模块，框架三种开发模式让你应对各种项目得心应手。


基本使用：


初始化页面
只要在入口文件里包含JuiclePHP初始化文件 



index.php

代码

include_once juicle/entry.php



访问index.php即可件 hello JuiclePHP!


常用用法

        /*
        // mysql 事物操作
        try {
            // 开启事物
            arComp('db.mysql')->transBegin();

            // 数据库业务逻辑代码。。。。。。。。

            // 提交
            arComp('db.mysql')->transCommit();
        } catch (Exception $e) {
            // 回滚
            arComp('db.mysql')->transRollBack();
        }
        */
        // 跳转到regAction
        // $this->redirect(array('reg', array('a' => 1)), '跳转了', 4);

        // 为 session 设置 变量值
        // arComp('list.session')->set('name', 'value');

        // 清空 session
        // arComp('list.session')->flush();

        // 为 session 获取 变量值
        // arComp('list.session')->get('name');

        // 输出显示模板 默认为index.php
        // $this->display();

        // 设置布局文件
        // $this->setLayoutFile('a');

        // 数据库表table查询一行
        // $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('t1')->queryRow();

        // 用模型实现查询（已定义T1Model）
        // $sp = T1Model::model()->getDb()->where(array('id' => 1))->queryRow();

        // 用模型实现查询（未定义T1Model）
        // $sp = arModel::model('T1')->getDb()->where(array('id' => 1))->queryRow();

        // 数据库查询所有
        // $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('t1')->queryAll();
        // 为 session 获取 变量值
        // arComp('list.session')->get('name');

        // 输出显示模板 默认为index.php
        // $this->display();

        // 设置布局文件
        // $this->setLayoutFile('a');

        // 数据库表table查询一行
        // $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('t1')->queryRow();

        // 用模型实现查询（已定义T1Model）
        // $sp = T1Model::model()->getDb()->where(array('id' => 1))->queryRow();

        // 用模型实现查询（未定义T1Model）
        // $sp = arModel::model('T1')->getDb()->where(array('id' => 1))->queryRow();

        // 数据库查询所有
        // $sp = arComp('db.mysql')->select('id')->where(array('id' => 1))->table('t1')->queryAll();

        // 数据库查询所有以指定键返回数据
        // $sp = arComp('db.mysql')->select('')->where(array('name' => 'wyp'))->table('t1')->queryAll('id');

        // 数据库查询所有以指定键返回原始数据
        // $sp = arComp('db.mysql')->select('')->where(array('name' => 'wyp'))->table('t1')->queryColumn('id');

        // 数据库更新数据
        // $sp = arComp('db.mysql')->where(array('name' => 'wyp'))->table('t1')->update(array('column' => 'value'));

        // 数据库插入数据
        // $sp = arComp('db.mysql')->where(array('name' => 'wyp'))->table('t1')->insert(array('column' => 'value'));

        // 测试HTTP请求参数
        // $result = arRequest('');

        // 网络调用main项目 IndexConterller apiAction
        // $res = arComp('rpc.json')->callApi('/main/index/api');

        // 加密字符串
        // $result = arComp('hash.mcrypt')->encrypt('yunListMan');

        // 解密字符串
        // $dresult = arComp('hash.mcrypt')->decrypt($result);

        // 远程POST数据到指定url
        // $data = array(
        //     'userName' => 'yctest',
        //     'loginTel' => '12345678914',
        //     'token' => '9sZwwC29j_i_aJ2ZGg0DxoPzWrgKBfgz_x_g6vqgfvXnrlRz_x_gg8g=',
        // );

        // 读取配置常量
        // arCfg('CONFIG_KEY');

        // arComp('rpc.source')->method = 'post';
        // $result = arComp('rpc.source')->remoteCall('http://cs.test.cn/yu/reg', $data);

        // 调用内部Service
        // $result = arComp('rpc.service')->WsTest('tt', array('ssd', 3));

        // 执行model方法
        // MyModel::model()->yourFunction();

        // 创建完整的URL
        // echo arComp('url.route')->createUrl('', array('ni' => '33', 'p' => 4));


框架结构：
