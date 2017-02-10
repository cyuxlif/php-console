## 使用
- 下载文件
- 直接加载插件源码（方便修改）， 直接加载chrome-extension-src/gaki目录即可， 点击背景页即可打开插件console面板
![image](https://raw.githubusercontent.com/cyuxlif/php-console/master/screenshots/QQ%E5%9B%BE%E7%89%8720161109115516.png)
- 引入Console.php使用， 可直接include, 或者根据框架的autoloader使用
  
```
include 'Console.php'

Console::log(array(4, 5,6));
Console::error("xxxxxxx");
Console::info("kkkkkkkk");
Console::warn("ttttttttt");
```
![image](https://raw.githubusercontent.com/cyuxlif/php-console/master/screenshots/QQ%E5%9B%BE%E7%89%8720161109115951.png)
## 配置

- 直接修改Console.php, 找到$_config 将host指向你的电脑

```
    protected static $_config = array(
        'host' => '172.21.104.60',//chrome 插件 服务器 即你的浏览器所在的ip
        'port' => '3003'//chrome 插件监听的端口 无需修改
    );
```
- 或者通过Console::setConfig("你的ip")配置

## 示例
- 找到框架的db处理地方打印sql, 可在执行sql处执行Console::logSql($sql)打印执行栈; 

## 说明

- 代码文件只有两个， Console.php， chrome插件源码在chrome-extension-src/js里



