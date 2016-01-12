<?php

session_start();

$mysqli = new mysqli('127.0.0.1', 'phptest', 'phptest', 'phptest');
if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit();
} else {
    $mysqli->set_charset("utf8");
}

$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

$id = session_id();

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

// s”‚ÌŽæ“¾
$stmt->store_result(); // ‚±‚ê–Y‚ê‚é‚Ænum_rows‚Í0
$rows .= "rows=" . $stmt->num_rows . "<br>";

$stmt->close();

// transaction commit
mysqli_commit($link);

// DBÚ‘±‚ð•Â‚¶‚é
$mysqli->close();




$_SESSION['_inject']['browser'] = $mysqldata;
echo '<div>str_replace2<br>';
echo str_replace("\0", '%00', session_encode()) . "\n";
echo '</div>';
echo '<br><br>';
echo '<div>var_dump2<br>';
echo var_dump($_SESSION);
echo '</div>';

session_destroy();


?>


<br>
<a href=phptest.php>back</a>
