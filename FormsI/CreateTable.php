<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Create Table</title></head>
<body>
<?php

CheckLogin1();
CheckRight1($pdo, 'Admin');

include ("TableFunc.php");

$TabNo= addslashes ($_REQUEST['TabNo']);
//$FldNo= addslashes ($_REQUEST['FldNo']);

if ($TabNo == '') {
  die ("<br> Error table code ");
}

MakeTable ($pdo, $TabNo);
?>
</body>
</html>