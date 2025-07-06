<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();

$AllLang=array ("EN", "RU", "FR");

$Tab='EnumValues';
$Frm='Enum';


$FullRef1='';

//print_r($_REQUEST);

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

  if (!HaveRight1 ($pdo, 'Translation')) {
    CheckRight1 ($pdo, 'Admin');
  }
}





$EnumML= $_REQUEST['EnumML'];
if ($EnumML=='') {
  die ("<br> Error: Bad Value ");
} 

if ($EnName=='') {
  die ("<br> Error: Bad Enum name ");
}

$separator = "\r\n";
$line = strtok($EnumML, $separator);
$i=0;

$InsCnt=0;
$UpdCnt=0;

try {
$PdoArr = array();

//EnumValues
$AllArr=array('EnumName', 'EnumVal', 'Lang', 'EnumDescription');
$PKArr=array('EnumName', 'EnumVal', 'Lang');


while ($line !== false) {
    # do something with $line
    $i++;
     
    list($Code, $Lang, $Val) = explode("\t", $line);
    $Code=trim($Code)+0;
    $CurrType=$Code;
    $Lang=trim($Lang);
    $Val=trim($Val);
    if (!in_array ($Lang,$AllLang)) {
      die ("<br>Lang line $i: $Code $Lang not ok Lang (expect RU, EN, FR)");  
    } 

  if ($Val!='') {
    $Val1=$Val; //addslashes($Val);

    $Arr = array ();
    $Arr['EnumName'] = $EnName;
    $Arr['Lang'] = $Lang;
    $Arr['EnumVal'] = $CurrType;
    $Arr['EnumDescription'] = $Val;

    $Res = UpdateTable ($pdo, $Tab, $AllArr, $Arr, $PKArr, 1);

    if ($Res=='I') {
      $InsCnt++;
      echo ("| $CurrType Inserted |");
    }
    else 
    if ($Res=='U') {
      $UpdCnt++;
      echo ("| $CurrType Updated |");
    }
  }

    $line = strtok( $separator );
}

echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="3;URL='.$Frm.'Frm.php?Enum='.$EnName.'&CurrType='.$CurrType.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');

  echo ("<br>$i lines, $InsCnt added, $UpdCnt changed");

  echo ("<br><a href='{$Frm}Frm.php?Id=$Id'>See card</a>");
  echo ("<br><a href='{$Frm}List.php'>List</a>");

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>
</body>
</html>
				       