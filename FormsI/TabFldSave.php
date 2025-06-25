<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>TabFld Save</title></head>
<?php

CheckLogin1();

CheckRight1 ($pdo, 'Admin');

//print_r ($_REQUEST);
//die();

$TabName='AdmTabFields';
$Frm='Tab';


$TabNo= $_REQUEST['TabCode'];
$FldNo= $_REQUEST['FldNo'];

$PdoArr = array();


$Fld1= array ('ParamNo', 'ParamName', 'DocParamType', 
               'Ord', 'AddParam',   'AutoInc', 
               'Description', 'ShortInfo', 'EnumLong');
$Fields=$Fld1;

$IntArr=array ( 'DocParamsUOM', 'AutoInc','ShortInfo', 'EnumLong');

foreach ( $IntArr as $F ) {
  if ($_REQUEST[$F]=='') {
    $_REQUEST[$F]='0';
  }
}

try {


if ($FldNo !='' ) {
  $PdoArr['TabNo']= $TabNo;
  $PdoArr['FldNo']= $FldNo;
  
  $Proc='Updated';
  $query="update \"$TabName\" set ";
  $Div='';
  foreach ( $Fields as $Fld) {
    $PdoArr[$Fld]= $_REQUEST[$Fld];
    $query.=$Div." \"$Fld\"=:$Fld";
    $Div=',';
  }
  $query.=" where (\"TypeId\"=:TabNo) AND (\"ParamNo\"=:FldNo)";
}
else {
  $PdoArr = array();
  $Str1='"TypeId"';
  $Str2=":TabNo";
  $PdoArr['TabNo']= $TabNo;
  foreach ( $Fields as $Fld) {

    $PdoArr[$Fld]=$_REQUEST[$Fld];
    
    $Str1.=$Div.", \"$Fld\"";
    $Str2.=$Div.", :$Fld";
  }
  $query="insert into \"$TabName\" ($Str1) values ($Str2)";
}

//echo ("<br> Line ".__LINE__.": $query<br>");
//              print_r($PdoArr);
//              echo ("<br>");

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$query = "update \"AdmTabNames\" set  \"ChangeDt\"=now() ". 
         "where (\"TabCode\" = :TabNo)";

  $PdoArr = array();
  $PdoArr['TabNo']= $TabNo;
         
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


$FldNo= $_REQUEST['ParamNo'];





echo('
<META HTTP-EQUIV="REFRESH" CONTENT="2;URL='.$Frm.'Card.php?TabCode='.
      $TabNo.'#Fld'.$FldNo.'">');

echo ('<body><br><b>'.GetStr($pdo, 'Edit'). '</b> ') ;
echo ($Proc);

?>
</body>
</html>
                       