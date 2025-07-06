<?php  
session_start();
include ("../setup/common.php");
BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Upload xlsx to CurrencyExchRate</title></head>
<body>
<?php
echo '<br>User: ' . $_SESSION['login'].'<br>';

//print_r($_FILES);
//echo ("<br>");

//print_r($_REQUEST);
//echo ("<br>");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include "common_func.php";
require '../../composer/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;


CheckRight1 ($pdo, 'ExtProj.Admin');

$FileName='CurrencyExchRate';

$real_name = "$TmpFilesDir/SIUpl/$FileName.xlsx";

echo ("<br>File $real_name<br>");
ini_set('memory_limit', '2048M');

//=============================================================================================
// Copy file to temp dir 

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];

$dir = "$TmpFilesDir/SIUpl";

$sizeStr='';
if ($size> (1024*1024) ) {
  $sizeStr = round($size/1024/1024, 1).'M';
  if ($Size > 10000000 ) {
    die ("<br> file size $sizeStr is not Allowed try upload less");
  }
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

   MakeAdminRec ($pdo, $_SESSION['login'], 'UploadXlsx', $sizeStr, 
                        $FileName, 'Uploaded xlsx file');
  
}
else {
  die ("<br> Error: Uploading is not ok file:".__FILE__." line:".__LINE__);
}

//=============================================================================================

// Запоминаем параметры (в какой колонке хранятся)
$ColsArr=array ();

$PkIndx='';

$L=3;

$HeadersArr=array ();

$objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($real_name);

//$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
//  Get worksheet dimensions


echo("<hr><h4> Upload Xlsx file for ".GetStr($pdo, 'CurrencyExchRate')."</h4>");

$FldsIndxArr= array ( 'CurrencyCode'=>-1,  'StartDate'=>-1,  'Multy'=>-1,  'Rate'=>-1, 
           'FullRate'=>-1);

$ColHeader= array ( 'CurrencyCode'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=CurrencyCode\' target=Translate>_</a>CurrencyCode',  'StartDate'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=StartDate\' target=Translate>_</a>StartDate',  'Multy'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Multy\' target=Translate>_</a>Multy',  'Rate'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Rate\' target=Translate>_</a>Rate', 
           'FullRate'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=FullRate\' target=Translate>_</a>FullRate');

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, false);

$Cnt = count($sheetData);
echo ("<br>Rows= $Cnt<br>");

$EnumArr=array('CurrencyCode'=>'Currency');
$DateArr=array('StartDate'=>1);

$BegLine=7;
$HeadLine=$sheetData[$BegLine];

foreach ( $HeadLine as $Col=> $Val) {
  $FindIdnx='';
  foreach ( $ColHeader as $ColName => $ColDescr ) {
    if ($FindIndx== "") {
      if ($Val == $ColDescr) {
        $FindIndx=$Col;
        $FldsIndxArr[$ColName]=$Col;       
      }
    }
  }
}

$PKArr=array ();
$PKArr['CurrencyCode']= $FldsIndxArr['CurrencyCode'];
$PKArr['StartDate']= $FldsIndxArr['StartDate'];


$Err=0;
foreach ($FldsIndxArr as $Fld=>$Indx) {
  if ($Indx==-1) {
    $Err++;
    echo ("<br> Error $Err: $Fld is not defined ");
  }
}

if ($Err>0) {
  die ("<br> Have $Err errors. Upload stopped ");
}


foreach ($sheetData as $L=> $Arr) {
  echo ("<hr> $L : ");
  print_r($Arr);


  if ($L> $BegLine ) {
    $Vals=array();

    foreach ( $FldsIndxArr as $Fld=>$Col ) {
      $Val = addslashes(trim($Arr[$Col]));
      if ($Val=='#NULL!') {
        $Val='';
      }


      $Vals[$Fld]=$Val;
    }

  }


}


if ($FldsIndxArr['CurrencyCode']==-1) {
  die ("<br> Error: field CurrencyCode: {$ColHeader['CurrencyCode']} is not found ");
}
if ($FldsIndxArr['StartDate']==-1) {
  die ("<br> Error: field StartDate: {$ColHeader['StartDate']} is not found ");
}

?>
</body></html>