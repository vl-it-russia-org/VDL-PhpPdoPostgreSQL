<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Item Family Edit</title></head>
<?php

CheckLogin1();

CheckRight1 ($pdo, 'Admin');


//print_r ($_REQUEST);
//die();

$TabName='AdmTabIndxFlds';
$Frm='Tab';


$TabNo= $_REQUEST['TypeId'];
$IndxName= $_REQUEST['IndxName'];

$PdoArr = array();

$PdoArr['TabNo']= $TabNo;
$PdoArr['IndxName']= $IndxName;
try {


$query="delete from \"$TabName\" where \"TabCode\"=:TabNo and \"IndxName\"=:IndxName";
 
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$LineNo=0;

if (! empty($_REQUEST['Par'])) {
  foreach ( $_REQUEST['Par'] as $FldNo=>$V) {
    $LineNo++;
    
    $FldNo2=$FldNo;
    $PdoArr['FldNo2']= $FldNo2;
    $OrdF = $_REQUEST['IndxOrd'][$FldNo];
    if (empty ($OrdF) ) {
      $OrdF=0;
    }
    $PdoArr['LineNo']= $LineNo;
    $PdoArr['OrdF']= $OrdF;
    $query="insert into \"$TabName\" (\"TabCode\", \"IndxName\", \"LineNo\", \"FldNo\", \"Ord\") values " .
           " (:TabNo,:IndxName, :LineNo, :FldNo2, :OrdF)"; 
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

    //echo( "<br>$query<br>");
  }

}

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}

echo('
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$Frm.'Card.php?TabCode='.
      $TabNo.'#Indx_'.$IndxName.'">');

echo ('<body><br><b>'.GetStr($pdo, 'Edit'). '</b> ') ;
echo ($Proc);

?>
</body>
</html>
				       