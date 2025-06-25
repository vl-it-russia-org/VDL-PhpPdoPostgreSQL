<?php
session_start();

include ("../setup/common_pg.php");

CheckLogin1();

CheckRight1 ($pdo, 'Admin');

echo '<br>User: ' . $_SESSION['login'];

$AllLang=array ("EN", "RU", "FR");

$Tab='EnumValues';
$Frm='Enum';


$FullRef1='';

//print_r($_REQUEST);
//die();

$EnName= $_REQUEST['Enum'];
$CurrType= $_REQUEST['CurrType'];
if ($CurrType=='') {
  die ("<br> Error: Bad Value ");
} 

if ($EnName=='') {
  die ("<br> Error: Bad Enum name ");
}


$PdoArr = array();
$PdoArr['EnName']= $EnName;
$PdoArr['CurrType']= $CurrType;
try {

// EnumValues
// EnumName, EnumVal, Lang, EnumDescription
$query = "delete from \"EnumValues\" ". 
         "where (\"EnumName\"=:EnName) and (\"EnumVal\"=:CurrType)"; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$Frm.'Frm.php?Enum='.$EnName.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
  echo ("<br><a href='{$Frm}Frm.php?Id=$Id'>See card</a>");
  echo ("<br><a href='{$Frm}List.php'>List</a>");
?>
</body>
</html>
				       