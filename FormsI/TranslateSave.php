<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();

//include "common_lab.php";


if (!HaveRight1 ($pdo, 'Translation')) {
  CheckRight1 ($pdo, 'Admin');
}

$AllLang=array ("EN", "RU", "FR");

$Tab='TranslationText';
$Frm='Translate';


$FullRef1='';

//print_r($_REQUEST);
//die();

$EnName= addslashes($_REQUEST['Enum']);

if ($EnName=='') {
  die ("<br> Error: Bad Value ");
} 

$FldsArr=array ( 'ID', 'Lang', 'TextVal');
$PKArr = array ( 'ID', 'Lang');

foreach ( $AllLang as $Lang) {
  $Val = $_REQUEST['Descr_'.$Lang];
  if ($Val!='') {
    $Arr=array();
    $Arr['ID']=$EnName;
    $Arr['Lang']=$Lang;
    $Arr['TextVal']=$Val;

    UpdateTable ($pdo, $Tab, $FldsArr, $Arr, $PKArr, 1);

  }
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
				       