<?php
// データベースセッションクラスのインスタンス作成
require_once 'MysqlSessionHandler.php';
$handler = new MysqlSessionHandler();
// クラスを設定
session_set_save_handler(
    array($handler, 'open'),
    array($handler, 'close'),
    array($handler, 'read'),
    array($handler, 'write'),
    array($handler, 'destroy'),
    array($handler, 'gc')
);
// シャットダウンする際にセッション情報を書き込んでクローズ
register_shutdown_function('session_write_close');
// セッション開始
session_start();

var_dump($_SESSION);
echo "<br>";
echo session_encode();
