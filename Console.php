<?php

/**
 * Class Console
 * author: dean
 */

class Console{
    protected static $_instance;
    protected static $_config = array(
        'host' => '172.21.104.60',//chrome 插件 服务器 即你的浏览器所在的ip
        'port' => '3003'//chrome 插件监听的端口
    );
    public $logs = array();

    protected function __construct(){

    }

    public static function setConfig($host, $port = '3003'){
        self::$_config = array(
            'host' => $host,
            'port' => $port,
        );
    }

    public static function getInstance(){
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function log($msg){
        self::getInstance()->addLog($msg);
    }

    public static function error($msg){
        self::getInstance()->addLog($msg, 'error');
    }

    public static function info($msg){
        self::getInstance()->addLog($msg, 'info');
    }

    public static function warn($msg){
        self::getInstance()->addLog($msg, 'warn');
    }
    
    public static function trace(){
        $trace = self::getBacktrace();
        self::log($trace);
    }
    
    public static function logSql($sql){
        self::getInstance()->addLog($sql, 'groupCollapsed');
        self::trace();
        self::getInstance()->addLog($sql, 'groupEnd');
    }
    
    public static function getBacktrace($ignore = 2){
        $trace = '';
        foreach (debug_backtrace() as $k => $v) {
            if ($k < $ignore) {
                continue;
            }
            $file = isset($v['file']) ? $v['file'] : 'unknown file';
            $line = isset($v['line']) ? '[:' . $v['line'] . ']: ' : '[:unknown line]';
            $class = isset($v['class']) ? $v['class'] . '->' : '';
            $func = isset($v['function']) ? $v['function'] . '()' : '';
            $trace .= '#' . ($k - $ignore) . ' ' . $file . $line . $class . $func . "\n";
        }

        return $trace;
    }

    public function addLog($msg = '', $type = 'log', $css = ''){
        if (is_array($msg) || is_object($msg)) {
            $msg = var_export($msg, true);
        }
        $this->logs[] = array(
            'type' => $type,
            'msg' => $msg,
            'css' => $css
        );
    }

    //发送到chrome
    protected function _socketSend(){
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n"); // 创建一个Socket
        $connection = socket_connect($socket, self::$_config['host'], self::$_config['port']) or die("Could not connet chrome server\n");    //  连接
        $msg = json_encode($this->logs);
        $msg .= "|EOM";//以特定字符串结尾
        socket_write($socket, $msg, strlen($msg)) or die("Write failed\n"); // 数据传送 向服务器发送消息
        socket_close($socket);
    }

    public function __destruct(){
        if (!empty($this->logs)) {
            $this->_socketSend();
        }
    }

}
