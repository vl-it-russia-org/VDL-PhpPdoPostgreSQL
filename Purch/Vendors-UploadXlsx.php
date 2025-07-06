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
<title>Upload xlsx to Vendors</title></head>
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

$FileName='Vendors';

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


echo("<hr><h4> Upload Xlsx file for ".GetStr($pdo, 'Vendors')."</h4>");

$FldsIndxArr= array ( 'Id'=>-1,  'VendorType'=>-1,  'VendorName'=>-1,  'ShortName'=>-1, 
           'INN'=>-1,  'KPP'=>-1,  'Country'=>-1,  'PostIndx'=>-1,  'City'=>-1, 
           'Address'=>-1,  'Phone'=>-1,  'WebLink'=>-1,  'DefaultDeliveryPoint'=>-1,  'Description'=>-1, 
           'Status'=>-1,  'Holding'=>-1,  'Position'=>-1,  'Director'=>-1,  'Accountant'=>-1, 
           'GeneralBusinessGroup'=>-1,  'TaxBusinessGroup'=>-1,  'Blocked'=>-1);

$ColHeader= array ( 'Id'=>'Id',  'VendorType'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=VendorType\' target=Translate>_</a>VendorType',  'VendorName'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=VendorName\' target=Translate>_</a>VendorName',  'ShortName'=>'Короткое название', 
           'INN'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=INN\' target=Translate>_</a>INN',  'KPP'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=KPP\' target=Translate>_</a>KPP',  'Country'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Country\' target=Translate>_</a>Country',  'PostIndx'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=PostIndx\' target=Translate>_</a>PostIndx',  'City'=>'Город', 
           'Address'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Address\' target=Translate>_</a>Address',  'Phone'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Phone\' target=Translate>_</a>Phone',  'WebLink'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=WebLink\' target=Translate>_</a>WebLink',  'DefaultDeliveryPoint'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=DefaultDeliveryPoint\' target=Translate>_</a>DefaultDeliveryPoint',  'Description'=>'Описание', 
           'Status'=>'Статус',  'Holding'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Holding\' target=Translate>_</a>Holding',  'Position'=>'Должность',  'Director'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Director\' target=Translate>_</a>Director',  'Accountant'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=Accountant\' target=Translate>_</a>Accountant', 
           'GeneralBusinessGroup'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=GeneralBusinessGroup\' target=Translate>_</a>GeneralBusinessGroup',  'TaxBusinessGroup'=>'<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=TaxBusinessGroup\' target=Translate>_</a>TaxBusinessGroup',  'Blocked'=>'Заблокирован');

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, false);

$Cnt = count($sheetData);
echo ("<br>Rows= $Cnt<br>");

$EnumArr=array('VendorType'=>'VendorType',
        'Status'=>'StatusNUZ',
        'GeneralBusinessGroup'=>'GeneralBusinessGroup',
        'TaxBusinessGroup'=>'TaxBusinessGroup');
$DateArr=array();

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
$PKArr['Id']= $FldsIndxArr['Id'];


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


if ($FldsIndxArr['Id']==-1) {
  die ("<br> Error: field Id: {$ColHeader['Id']} is not found ");
}

?>
</body></html>