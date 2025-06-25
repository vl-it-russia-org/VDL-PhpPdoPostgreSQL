<?php
session_start();
include ("../setup/common.php");
BeginProc();
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once '../../PhpExcel/Classes/PHPExcel.php';
$TabName='CurrencyExchRate';
$Frm='CurrencyExchRate';
$Fields=array('CurrencyCode','StartDate','Multy'
      ,'Rate','FullRate');
$enFields= array('CurrencyCode'=>'Currency');
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $BegPos = addslashes ($_REQUEST['BegPos']);
if ($BegPos==''){
$BegPos=0;
}

$ORD = addslashes ($_REQUEST['ORD']);
if ($ORD =='1') {
$ORD = 'CurrencyCode,StartDate';
  }
  else {
    $ORD = 'CurrencyCode,StartDate';
  }

  $ORDS = ' order by  '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  }
  
  $WHS = '';
  $FullRef='?ORD='.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=addslashes($_REQUEST['Fltr_'.$Fld]);
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      if ($enFields[$Fld]!='') {
        $WHS.='('.$Fld." = '$Fltr')"; 
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr );
      }
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
    }
  }


$Let = array ( 'A','B','C','D','E', 'F','G','H','I','J',
                   'K','L','M','N','0', 'P','Q','R','S','T','U');

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
			 ->setLastModifiedBy($_SESSION['login'])
			 ->setTitle("AccPhp CurrencyExchRate")
			 ->setSubject("CurrencyExchRate")
			 ->setDescription("Legrand Russia")
			 ->setKeywords("AccPhp;CurrencyExchRate")
			 ->setCategory("AccPhp;CurrencyExchRate");
  
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$W= array ( 10, 10, 10, 10, 10, 10, 10, 11, 11, 12, 12);

foreach ($W as $i => $Val) {
  $aSheet->getColumnDimension($Let[$i])->setWidth($Val);
};

if ($WHS != '') {
  $WHS = ' where '.$WHS;
};

$row=1;
$col=0;   


$aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, 'CurrencyExchRate').
      ' '.  GetStr($mysqli, 'List'));
  
$row++;
$col=0;   

$aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, 'Created').
      ": {$_SESSION['login']} ". date("Y-m-d H:i:s"));


$query = "select * ".
       "FROM CurrencyExchRate ".
       " $WHS $ORDS";

$sql2 = $mysqli->query ($query)
               or die("Invalid query:<br>$query<br>" . $mysqli->error);


$row++;
$col=0;

foreach ( $Fields as $Fld) {
  $aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, $Fld));
  $col++; 
}

$n=0;
$Cnt=0;
$row++;

while ($dp = $sql2->fetch_assoc()) {
  $col=0;

  $Fld='CurrencyCode';
  $aSheet->setCellValueByColumnAndRow($col, $row, 
               GetEnum($mysqli, 'Currency', $dp[$Fld]));
  $col++;

  $Fld='StartDate';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='Multy';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='Rate';
  $OW=number_format($dp[$Fld], 4, ".", "'");
    $aSheet->setCellValueByColumnAndRow($col, $row, $OW);
  $col++;

  $Fld='FullRate';
  $OW=number_format($dp[$Fld], 4, ".", "'");
    $aSheet->setCellValueByColumnAndRow($col, $row, $OW);
  $col++;
  $row++;
}

  $objPageSetup = new PHPExcel_Worksheet_PageSetup();
  $objPageSetup->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


  $objPageSetup->setOrientation(
  PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT );
  //PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
  $aSheet->setPageSetup($objPageSetup);

  $pageMargins = $aSheet->getPageMargins();

  // margin is set in inches (0.5cm)
  $margin = 0.5 / 2.54;
  $pageMargins->setTop($margin);
  $pageMargins->setBottom($margin);
  $pageMargins->setLeft($margin * 6);
  $pageMargins->setRight($margin);
  $objPageSetup->setFitToWidth(1);
  $objPageSetup->setFitToHeight(10);

  $add_str=date('-Ymd_His');


  //MakeAdminRec ($_SESSION['login'], 'EDI_ORD', $OrdId, 
  //                      'Out XLS', "Out file $add_str.XLS: $LineNo lines, amount $TotAmount");

  $writer = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5');


header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename=Xls-CurrencyExchRate'.$add_str.'.xls');
$writer->save('php://output');
?>