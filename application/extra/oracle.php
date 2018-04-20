<?php
/**
 * Created by MCY<1991993249@qq.com>
 * User: 勉成翌
 * Date: 2017/9/19
 * Time: 13:04
 */
return [
    'oracle_conn' => [
        // 数据库类型
        'type' => '\think\oracle\Connection',//\think\oracle\Connection
        // 服务器地址
        'hostname' => '172.18.111.61',
        // 数据库名
        'database' => 'OMISCTU',
        // 用户名
        'username' => 'GYJW',
        // 密码
        'password' => 'GYJW',
        // 端口
        'hostport' => '1521',
        // 连接dsn
        'dsn' => '',
        // 数据库连接参数
        'params' => [
            // 与数据库是否是长连接 true,false
            PDO::ATTR_PERSISTENT => true,
            // 表查出来的字段大小写输出。PDO::CASE_LOWER：强制列名小写,PDO::CASE_NATURAL：列明按照原始的方式,PDO::CASE_UPPER：强制列名大写
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            // PDO::ERRMODE_SILENT：不显示错误信息，只显示错误码,PDO::ERRMODE_WARNING：显示警告错误,PDO::ERRMODE_EXCEPTION：抛出异常
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // 字段为空，则返回啥，包括PDO::NULL_NATURAL,PDO::NULL_EmpTY_STRING,PDO::NULL_TO_STRING
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_TO_STRING,
            // 从表查出来的都是字符串格式
            PDO::ATTR_STRINGIFY_FETCHES => false,
            // 防驻入。建议设成false
            PDO::ATTR_EMULATE_PREPARES => false
        ],
        // 数据库编码默认采用utf8
        'charset' => 'zhs16gbk',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy' => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate' => false,
        // 读写分离后 主服务器数量
        'master_num' => 1,
        // 指定从服务器序号
        'slave_no' => '',
        // 是否严格检查字段是否存在
        'fields_strict' => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'array',
        // 是否自动写入时间戳字段
        'auto_timestamp' => true,
        // 是否需要进行SQL性能分析
        'sql_explain' => false
    ]
];