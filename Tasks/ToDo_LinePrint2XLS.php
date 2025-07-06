<?php
session_start();
require '../../composer/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
include ("../setup/common_pg.php");
BeginProc();
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$TabName='ToDo_Line';
$Frm='ToDo_Line';
$Fields=array('Id','ParentId','ToDoCode'
      ,'Description','DateBeg','DateEnd','Status');
$enFields= array('Status'=>'ToDoStatus');

CheckRight1 ($pdo, 'Task');

$ORD = $_REQUEST['ORD1'];

$FR='1';
if ($ORD =='1') {
  $ORD = '"ToDoCode"';
}
else 
if ($ORD =='2') {
  $ORD = '"Id"';
  $FR='2';
}
else 
{
  $ORD = '"ToDoCode"';
}

  $ORDS = ' order by  '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  }

  $PdoArr = array();
  $WHS = '';
  $FullRef='?ORD='.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST['Fltr_'.$Fld];
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      if ($enFields[$Fld]!='') {
        $WHS.='("'.$Fld."\" = :$Fld)";
        $PdoArr[$Fld]= $Fltr;
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $pdo );
      }
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
    }
  }

//=========================================================================
function CalcLevel(&$pdo, $ParentId, &$PredArr) {
  if (empty ($PredArr[$ParentId])) {
    if ( $ParentId== 0) {
      $PredArr[$ParentId]=1;  
    }
    else {
      //-------------------------------
      // \"ToDo_Line\"
      // \"Id\", \"ParentId\", \"ToDoCode\", \"Description\", 
      // \"DateBeg\", \"DateEnd\", \"Status\"
      // Enum: Status [ ToDoStatus ] : [0] New, [10] Planned, [20] Done
      //-------------------------------
      $PdoArr = array();
      $PdoArr['Id']= $ParentId;

      try {
        $query = "select * from \"ToDo_Line\" ". 
                 "where (\"Id\" = :Id)"; 

        $STH = $pdo->prepare($query);
        $STH->execute($PdoArr);

        if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
          $Val = $dp2['ParentId'];
          if ($Val==0) {
            $PredArr[$ParentId]=2;  
          }
          else {
            $Lev = CalcLevel($pdo, $Val, $PredArr);
            $PredArr[$ParentId]=$Lev+1;  
          }
        }
        else {
          die ("<br> Error: ToDo_Line id=$ParentId is not found");
        }

      }
      catch (PDOException $e) {
        echo ("<hr> Line ".__LINE__."<br>");
        echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
        print_r($PdoArr);
        die ("<br> Error: ".$e->getMessage());
      }      
    }
  }
  return $PredArr[$ParentId]; 

}

//=========================================================================
$Let = array ( 'A','B','C','D','E', 'F','G','H','I','J',
                   'K','L','M','N','0', 'P','Q','R','S','T','U');

$objPHPExcel = new Spreadsheet();
$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
             ->setLastModifiedBy($_SESSION['login'])
             ->setTitle("AccPhp ToDo_Line")
             ->setSubject("ToDo_Line")
             ->setDescription("VDL PHP+PDO+PostgreSQL")
             ->setKeywords("AccPhp;ToDo_Line")
             ->setCategory("AccPhp;ToDo_Line");
  
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$W= array ( 5, 5, 5, 5, 12, 12, 10, 11, 11, 25, 12, 12, 12);
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


$aSheet->setCellValue([$col, $row], GetStr($pdo, 'ToDo_Line').
      ' '.  GetStr($pdo, 'List'));
  
$row++;
$aSheet->setCellValue([$col, $row], GetStr($pdo, 'Created').
      ": {$_SESSION['login']} ". date("Y-m-d H:i:s"));


$query = "select * FROM \"ToDo_Line\" ".
         "$WHS $ORDS";

try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);



$row++;
$col=1;

$FL=$row;

$MaxLevels=7;

foreach ( $Fields as $Fld) {
  if ($Fld=='ToDoCode') {
    $i=0;
    while ($i<$MaxLevels) {
      $i++;
      $aSheet->setCellValue([$col, $row], 'L'.$i);
      $col++;

    }
    
  }
  else {
    $aSheet->setCellValue([$col, $row], GetStr($pdo, $Fld));
    $col++;
  }   
}

$n=0;
$Cnt=0;
$row++;

$PredLevel=array();


while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $col=1;

  $Fld='Id';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='ParentId';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  //=================================================
  // Calc level:
  $Level = CalcLevel($pdo, $dp['ParentId'], $PredLevel);

  if ($Level > $MaxLevels) {
    echo ("<br> <hr> DP = ");
    print_r($dp);
    die ("<br>Error: More than $MaxLevels levels"); 
  }

  $col+=$Level-1;

  //=================================================

  $Fld='ToDoCode';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col+=$MaxLevels-$Level+1;


  $Fld='Description';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='DateBeg';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='DateEnd';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='Status';
  $aSheet->setCellValue([$col, $row], GetEnum($pdo, 'ToDoStatus', $dp[$Fld]));
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


  //MakeAdminRec ($pdo, $_SESSION['login'], 'EDI_ORD', $OrdId, 
  //                      'Out XLS', "Out file $add_str.XLS: $LineNo lines, amount $TotAmount");

  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel , 'Xlsx');

 }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }



header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Xls-ToDo_Line'.$add_str.'.xlsx');
$writer->save('php://output');
?>