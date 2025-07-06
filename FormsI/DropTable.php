<?php
session_start();

include ("../setup/common_pg.php");

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>!!!DROP Table!!!</title></head>
<body>
<?php

include ("TableFunc.php");

$TabNo= addslashes ($_REQUEST['TabNo']);
//$FldNo= addslashes ($_REQUEST['FldNo']);

if ($TabNo == '') {
  die ("<br> Error table code ");
}

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

try {
$query = "select * FROM \"AdmTabNames\" where \"TabCode\"=:TabNo";

//echo ($query);

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$dp=array();
$VDL_TabName='';
if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {

  $VDL_TabName=$dp['TabName'];
  
  $Fld= 'TabName';
  echo ( "<h1> Drop table {$dp[$Fld]} from DATABASE ??? </H1>");

  $Fld= 'TabDescription';
  echo (DivTxt ($dp[$Fld]). 
       "<br><a href='DropTableDo.php?TabName=$VDL_TabName' ".
              "onclick='confirm (\"Are You sure drop table $VDL_TabName from DataBase?\");'>Drop table from DB</a>");


  echo ("<br><br><br>Clean up table <a href='CleanUpTable.php?TabNo=$TabNo'>from LIST</a>"); 
}
else {
    die ("<br> Error bad Table $TabNo "); 
}
}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>
</body></html>