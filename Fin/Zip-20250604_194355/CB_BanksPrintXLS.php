<?php
session_start();
include ("../setup/common.php");
BeginProc();
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once '../PhpExcel/Classes/PHPExcel.php';
$TabName='CB_Banks';
$Frm='CB_Banks';
$Fields=array('BIK','BankName','BankTransitAcc'
      ,'City');
$enFields= array();
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $BegPos = addslashes ($_REQUEST['BegPos']);
if ($BegPos==''){
$BegPos=0;
}

$ORD = addslashes ($_REQUEST['ORD']);
if ($ORD =='1') {
$ORD = 'BIK';
  }
  else {
    $ORD = 'BIK';
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
			 ->setTitle("AccPhp CB_Banks")
			 ->setSubject("CB_Banks")
			 ->setDescription("Legrand Russia")
			 ->setKeywords("AccPhp;CB_Banks")
			 ->setCategory("AccPhp;CB_Banks");
  
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$W= array ( 10, 10, 10, 10, 10, 10, 10, 11, 11, 12, 12);
$LastCol="";
foreach ($W as $i => $Val) {
  $aSheet->getColumnDimension($Let[$i])->setWidth($Val);
  $LastCol=$Let[$i];
};

if ($WHS != '') {
  $WHS = ' where '.$WHS;
};

$row=1;
$col=0;   


$aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, 'CB_Banks').
      ' '.  GetStr($mysqli, 'List'));
  
$row++;
$col=0;   

$aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, 'Created').
      ": {$_SESSION['login']} ". date("Y-m-d H:i:s"));


$query = "select * ".
       "FROM CB_Banks ".
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

  $Fld='BIK';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='BankName';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='BankTransitAcc';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='City';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;
  $row++;
}

  
  $aSheet->setAutoFilter("A3:{$LastCol}3");
  $aSheet->freezePane("C4");

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
header('Content-Disposition: attachment;filename=Xls-CB_Banks'.$add_str.'.xls');
$writer->save('php://output');
?>