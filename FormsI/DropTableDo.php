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
<title>Drop table</title></head>
<body>
<?php

include ("TableFunc.php");

$TableName= addslashes ($_REQUEST['TabName']);
//$FldNo= addslashes ($_REQUEST['FldNo']);

if ($TableName=='') {
  die ("<br> Error: Empty Table Name ");
} 


$TabNo=0;
$PdoArr = array();
$PdoArr['TableName']= $TableName;
try {
// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$query = "select * from \"AdmTabNames\" ". 
         "where (\"TabName\" = :TableName)"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabNo = $dp2['TabCode'];
}
else {
  die ("<br> Error: Table $TableName not found");
}



$query = "drop table \"$TableName\"";

//echo ($query);

$STH = $pdo->prepare($query);
$STH->execute();

$PdoArr = array();

    // SqlLog
    // Id, User, OpDate, TabNo, 
    // Description, SqlText

    $PdoArr['Usr']= $_SESSION['login'];
    $PdoArr['q']= $query;
    $PdoArr['TabNo']= $TabNo;
    $PdoArr['Msg']= "Drop table $VDL_TabName";

    $query = "insert into \"SqlLog\" (\"User\", \"OpDate\", \"TabNo\", \"Description\", \"SqlText\") ". 
             "values (:Usr, now(), :TabNo, :Msg, :q)"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);


echo ("<br> Done table $TableName has been dropped"); 

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


?>
</body></html>