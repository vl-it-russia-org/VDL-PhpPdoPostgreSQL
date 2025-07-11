<?php
session_start();
require '../../composer/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
include ("../setup/common.php");
BeginProc();
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$TabName='SF_User';
$Frm='SF_User';
$Fields=array('Id','UserName','Region'
      ,'FirstName','LastName','Email','IsActive'
      ,'UserRoleId','ManagerId','LastLoginDate','LegrandRegion__c'
      ,'EnglishName__c','Firm__c','SalesTeam__c','PatronymicName__c'
      ,'UnId','Title','CompanyName','Department'
      ,'Phone');
$enFields= array('LastLoginDate'=>'LastLoginDate');
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $BegPos = addslashes ($_REQUEST['BegPos']);
if ($BegPos==''){
$BegPos=0;
}

$ORD = addslashes ($_REQUEST['ORD']);
if ($ORD =='1') {
$ORD = 'UnId';
  }
  else {
    $ORD = 'UnId';
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

$objPHPExcel = new Spreadsheet();
$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
			 ->setLastModifiedBy($_SESSION['login'])
			 ->setTitle("AccPhp SF_User")
			 ->setSubject("SF_User")
			 ->setDescription("Legrand Russia")
			 ->setKeywords("AccPhp;SF_User")
			 ->setCategory("AccPhp;SF_User");
  
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
$col=1;   


$aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, 'SF_User').
      ' '.  GetStr($mysqli, 'List'));
  
$row++;
$aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, 'Created').
      ": {$_SESSION['login']} ". date("Y-m-d H:i:s"));


$query = "select * ".
       "FROM SF_User ".
       " $WHS $ORDS";

$sql2 = $mysqli->query ($query)
               or die("Invalid query:<br>$query<br>" . $mysqli->error);


$row++;
$col=1;

$FL=$row;

foreach ( $Fields as $Fld) {
  $aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, $Fld));
  $col++; 
}

$n=0;
$Cnt=0;
$row++;

while ($dp = $sql2->fetch_assoc()) {
  $col=1;

  $Fld='Id';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='UserName';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='Region';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='FirstName';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='LastName';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='Email';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='IsActive';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='UserRoleId';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='ManagerId';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='LastLoginDate';
  $aSheet->setCellValueByColumnAndRow($col, $row, 
               GetEnum($mysqli, 'LastLoginDate', $dp[$Fld]));
  $col++;

  $Fld='LegrandRegion__c';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='EnglishName__c';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='Firm__c';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='SalesTeam__c';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='PatronymicName__c';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='UnId';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='Title';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='CompanyName';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='Department';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;

  $Fld='Phone';
  $aSheet->setCellValueByColumnAndRow($col, $row, $dp[$Fld]);
  $col++;
  $row++;
}

  $l=$row-1;
  $aSheet->setAutoFilter("A3:{$LastCol}3");
  $aSheet->freezePane("C4");

  $styleArray = array(
      'borders' => array(
          'outline' => array(
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              //'color' => array('argb' => 'FFFF0000'),
          ),
          
          'inside' => array(
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
              //'color' => array('argb' => 'FFFF0000'),
          ),
      ),
  );  

  $aSheet->getStyle("A$FL:$LastCol$l")->applyFromArray($styleArray);



  $aSheet->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                                                                           // ::ORIENTATION_PORTRAIT );
  $aSheet->getPageSetup()
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


  $margin = 0.5 / 2.54;
  $aSheet->getPageMargins()->setTop($margin*5);
  $aSheet->getPageMargins()->setRight($margin);
  $aSheet->getPageMargins()->setLeft($margin*2);
  $aSheet->getPageMargins()->setBottom($margin);

  //$aSheet->getPageSetup()->setScale(80);

  $aSheet->getPageSetup()->setFitToWidth(1);
  $aSheet->getPageSetup()->setFitToHeight(10);  


  $add_str=date('-Ymd_His');


  //MakeAdminRec ($mysqli, $_SESSION['login'], 'EDI_ORD', $OrdId, 
  //                      'Out XLS', "Out file $add_str.XLS: $LineNo lines, amount $TotAmount");

  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel , 'Xlsx');



header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Xls-SF_User'.$add_str.'.xlsx');
$writer->save('php://output');
?>