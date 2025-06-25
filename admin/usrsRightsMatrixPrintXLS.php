<?php
session_start();
include ("../setup/common.php");
BeginProc();
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once '../PhpExcel/Classes/PHPExcel.php';
$TabName='usrs';
$Frm='usrs';

$Fields=array('usr_id','description'
      ,'email', 'Position','Department','Company'
      ,'FirstName','LastName','PatronymicName__c');

$enFields= array();
CheckRight1 ($mysqli, 'ExtProj.Admin');

$BegPos = addslashes ($_REQUEST['BegPos']);
if ($BegPos==''){
  $BegPos=0;
}

$UsrArr   = array();
$RightArr = array();
$URArr    = array();

$ORD = addslashes ($_REQUEST['ORD']);
if ($ORD =='1') {
  $ORD = 'usr_id';
}
else {
  $ORD = 'usr_id';
}

$ORDS = ' order by  '; 
if ($ORD !='') {
  $ORDS = ' order by '.$ORD;
}

$WHS = '(Blocked=0)';
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


$Let = array ('A','B','C','D','E', 'F','G','H','I','J',
                   'K','L','M','N','0', 'P','Q','R','S','T','U');

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
			 ->setLastModifiedBy($_SESSION['login'])
			 ->setTitle("AccPhp usrs")
			 ->setSubject("usrs")
			 ->setDescription("Legrand Russia")
			 ->setKeywords("AccPhp;usrs")
			 ->setCategory("AccPhp;usrs");
  
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$W= array ( 20, 30, 5, 25, 20, 5, 15, 15, 11, 12, 12);

foreach ($W as $i => $Val) {
  $aSheet->getColumnDimension($Let[$i])->setWidth($Val);
};

if ($WHS != '') {
  $WHS = ' where '.$WHS;
};

$row=1;
$col=0;   


$aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, 'UsrRights').' '.GetStr($mysqli, 'List'));
  
$row++;
$col=0;   

$aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, 'Created').
      ": {$_SESSION['login']} ". date("Y-m-d H:i:s"));


$query = "select * FROM usrs ".
         "$WHS $ORDS";

$sql2 = $mysqli->query ($query)
               or die("Invalid query:<br>$query<br>" . $mysqli->error);


while ($dp = $sql2->fetch_assoc()) {
  $Fld='Blocked';
  if ( $dp[$Fld]==0) {
  
  $Fld='usr_id';
  $Usr=$dp[$Fld];
  $UsrArr[$Usr][$Fld]= $dp[$Fld];
  
  $Fld='description';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];

  $Fld='admin';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];
  
  $Fld='email';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];

  $Fld='Position';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];

  $Fld='Department';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];

  $Fld='Company';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];

  $Fld='FirstName';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];

  $Fld='LastName';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];

  $Fld='PatronymicName__c';
  $UsrArr[$Usr][$Fld]= $dp[$Fld];
  }
}

//=============================================================
// Rights
// RightType, RightDescription, HelpLink, NeedLocation, 
// EnumRight, HaveTable, TabName, FieldName
$query = "select * FROM Rights order by RightType  ";

$sql2 = $mysqli->query ($query) 
          or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
while ($dp2 = $sql2->fetch_assoc()) {
  $RightArr[$dp2['RightType']]['RD'] = $dp2['RightDescription'];
  $RightArr[$dp2['RightType']]['ER'] = $dp2['EnumRight'];
  
  $R = $dp2['RightType'];
  
  //UsrRights
  //UsrName, RightType, RightSubType, Val
  $query = "select R.* ".
           "FROM UsrRights R, usrs U where (RightType='$R') and (Val=1) and ".
           "(R.UsrName=U.usr_id) and (Blocked=0) order by UsrName, RightType, RightSubType ";

  $sql22 = $mysqli->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  while ($dp22 = $sql22->fetch_assoc()) {
    if ( empty ($URArr[$dp22['UsrName']][$dp22['RightType']])) {
      if ($dp2['EnumRight']!='') {
        $URArr[$dp22['UsrName']][$dp22['RightType']]= GetEnum($mysqli, $dp2['EnumRight'],$dp22['RightSubType']);
      }
      else {
        $URArr[$dp22['UsrName']][$dp22['RightType']]= $dp22['RightSubType'];
      
      }
    }
    else {
      if ($dp2['EnumRight']!='') {
        $URArr[$dp22['UsrName']][$dp22['RightType']].= ', '.GetEnum($mysqli, $dp2['EnumRight'],$dp22['RightSubType']);
      }
      else {
        $URArr[$dp22['UsrName']][$dp22['RightType']].= ', '.$dp22['RightSubType'];
      }
    }
  }
}

$row++;
$col=0;

foreach ( $Fields as $Fld) {
  $aSheet->setCellValueByColumnAndRow($col, $row, GetStr($mysqli, $Fld));
  $col++; 
}

$StartCol=$col;


foreach ($RightArr as $RT => $Arr1) {
  $aSheet->setCellValueByColumnAndRow($col, $row, $RT);
  
  $CellName = PHPExcel_Cell::stringFromColumnIndex($col).$row;
  
  $aSheet->setCellValueByColumnAndRow($col, 2, $Arr1['RD']);

  //$aSheet->getComment($CellName)->setAuthor($_SESSION['login']);
  $aSheet->getComment($CellName)->getText()->createText($Arr1['RD']);
  $col++; 
}

$SCol = PHPExcel_Cell::stringFromColumnIndex($StartCol).$row;

$MaxCol = $col;

$MCol = PHPExcel_Cell::stringFromColumnIndex($MaxCol).$row;

$aSheet->getStyle("$SCol:$MCol")->getAlignment()->setTextRotation(90);


$n=0;
$Cnt=0;
$row++;
$col=0;

foreach ($UsrArr as $Usr=> $Arr1) {
  $n++;
  foreach ($Fields as $Fld) {
    $aSheet->setCellValueByColumnAndRow($col, $row, $UsrArr[$Usr][$Fld]);
    $col++; 
  }

  //========================================================================

  foreach (  $RightArr as $RT => $Arr2) {
    $HR= $URArr[$Usr][$RT];
    if ($HR == '-') {
      $HR='X';
    }
    $aSheet->setCellValueByColumnAndRow($col, $row, $HR);
    $col++; 
  }

  $row++;
  $col=0;
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
header('Content-Disposition: attachment;filename=Xls-usrs'.$add_str.'.xls');
$writer->save('php://output');
?>