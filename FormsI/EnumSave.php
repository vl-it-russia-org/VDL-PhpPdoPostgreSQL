<?php
session_start();

include ("../setup/common_pg.php");

CheckLogin1();

echo '<br>User: ' . $_SESSION['login'];

$AllLang=array ("EN", "RU", "FR");
$Tab='EnumValues';
$Frm='Enum';


$Flds=array ('EnumName', 'EnumVal', 'Lang', 'EnumDescription');
$PkFlds=array ('EnumName', 'EnumVal', 'Lang');


$FullRef1='';

//print_r($_REQUEST);
//die();

$EnName= $_REQUEST['Enum'];

$HaveRight=0;
if ( substr ($EnName, 0, 4)== 'Clf-') {
  $Pos = strrpos ( $EnName, '-');
  $ClassCode= substr($EnName, 0, $Pos);
  echo (  "<br>$ClassCode<br>");
  if (  HaveRight1($pdo, $ClassCode) ) {
    $HaveRight=1;
    echo (" have ");
  }
}

if ($HaveRight==0) {
  CheckRight1 ($pdo, 'Admin');
}


$CurrType= $_REQUEST['CurrType'];
if ($CurrType=='') {
  die ("<br> Error: Bad Value ");
} 

if ($EnName=='') {
  die ("<br> Error: Bad Enum name ");
} 

$PdoArr = array();
$PdoArr['EnumVal']= $CurrType;
$PdoArr['EnumName']= $EnName;

$Flds=array ('EnumName', 'EnumVal', 'Lang', 'EnumDescription');
$PkFlds=array ('EnumName', 'EnumVal', 'Lang');



foreach ( $AllLang as $Lang) {
  $PdoArr['EnumDescription']= $_REQUEST['Descr_'.$Lang];
  $PdoArr['Lang']= $Lang;
  $Res= UpdateTable($pdo, "EnumValues", $Flds, $PdoArr, $PkFlds, 1);
  
};

echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$Frm.'Frm.php?Enum='.$EnName.'&CurrType='.$CurrType.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
  echo ("<br><a href='{$Frm}Frm.php?Id=$Id'>See card</a>");
  echo ("<br><a href='{$Frm}List.php'>List</a>");
?>
</body>
</html>
				       