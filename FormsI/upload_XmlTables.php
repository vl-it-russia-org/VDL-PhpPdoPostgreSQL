<?php
session_start();

include ("../setup/common_pg.php");
include ("../XmlRead.php");
include ("XmlTablesUpload_func.php");
?>
<html>
<head><title>Uploaded Customers XML file</title></head>
<body>
<?php

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

//echo ("<br>");
//print_r( $_FILES);
//echo ("<br><br>");

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];
$dir = '';

$Firm='XmlTables';

$real_name = "$TmpFilesDir/$Firm.txt";

$sizeStr='';
if ($size> (1024*1024) ) {
  $sizeStr = round($size/1024/1024, 1).'M';
}else{
  if ($size>1024) {
    $sizeStr = round ($size/1024, 1).'K';
  }
  else
    $sizeStr = $size.'b';
};

echo ("File: $real_name $sizeStr<br>");


if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded $Firm<br>");

  //MakeAdminRec ($mysqli, $_SESSION['login'], $Firm, $sizeStr, 
  //                      $Firm, "Uploaded $Firm XML file");
  
  //echo("<br><a href='{$Firm}_MakeUpload.php'>Start upload</a>");

//==================================================================================





$Firm='XmlTables';

$InsFldCnt=0;
$InsTabCnt=0;

$UpdFldCnt=0;
$real_name = "$TmpFilesDir/$Firm.txt";

if (!file_exists  ( $real_name )) {
  die ("<br> Error: File is not exists $real_name ") ;
};

// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable

$Flds = array ('TabName', 'TabDescription', 'TabCode', 'TabEditable');

$Flds1 = array ('TableName'=>'TabName', 'TabDescription'=>'TabDescription', 
                'TabEditable'=>'TabEditable', 'ChangeDt'=>'ChangeDt', 'Ver'=>'Ver');

$ConnArr= array ('LineNo', 'TabCond', 'Tab2', 'Field2', 'CondTab2', 'AddConnFldFrom', 'AddConnFldTo', 
                 'AddFldsListTo', 'AddFldsListFrom', 'SelectViewName'); 


$Buf = file_get_contents ($real_name) ;

$I=0;
$Buf1= GetXMLVal ( $Buf, 'VDLTables-Descriptions', $I);

$TabCode='';
$TabName='';

if ($Buf1 != '') {
  $More = 1;
  $BegPos2025=0;
  while ($More==1) {
    $ReadTab = GetXMLVal ( $Buf1, 'Table', $BegPos2025); 
    
    $j=0;
    $IndxStr = GetXMLVal ( $ReadTab, 'Indexes', $j);
    if ($ReadTab != '') {
      
      $TabCode=ReadTab ($pdo, $ReadTab);

      $IndxRes= ReadIndx ($pdo, $TabCode, $IndxStr);
    }
    else {
      $More=0;
    }
  }
}

echo (" Tables $InsTabCnt inserted, Fields inserted $InsFldCnt, Flds updated: $UpdFldCnt");

echo ("<hr><a href='TabCard.php?TabCode=$TabCode'>Table card</a>");














//==============================================================================================

}
else {

  echo ("<br> Error: Upload $real_name");
}

?>
</body>
</html>				       