<?php
session_start();
include ("../setup/common_pg.php");
//BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Item Family Edit</title></head>
<?php

CheckRight1 ($pdo, 'Admin');


//print_r ($_REQUEST);
//die();

$TabName='AdmTabFields';
$Frm='Tab';


$TabNo= $_REQUEST['TabNo'];
$FldNo= $_REQUEST['FldNo'];

$Fld1= array ('ParamNo', 'ParamName', 'NeedSeria', 'NeedBrand','DocParamType', 
              'DocParamsUOM',   
               'Ord', 'AddParam',  'CalcFormula');
$Fields=$Fld1;

$Proc='Delete';

if ($FldNo !='' ) {
  $query="delete from \"$TabName\" where (\"TypeId\"=:TabNo) AND (\"ParamNo\"=:FldNo)";
  
  $PdoArr = array();
  $PdoArr['TabNo']= $TabNo;
  $PdoArr['FldNo']= $FldNo;
  
  try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

}

echo('
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$Frm.'Card.php?TabCode='.$TabNo.'">');

echo ('<body><br><b>'.GetStr($pdo, 'Edit'). '</b> ') ;
echo ($Proc);

?>
</body>
</html>
				       