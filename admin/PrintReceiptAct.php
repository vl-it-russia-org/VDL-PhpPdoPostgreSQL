<?php
session_start();
isset($_SESSION['login']) or 
  die('You are not login.<br>Вы не вошли<br><br>'.
      '<a href="../admin/index.php">Login page<br>Страничка для входа</a>');
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
//require_once '../Build/PHPExcel.phar';
require_once '../PhpExcel/Classes/PHPExcel.php';
include ("../adv/setup/config.php");
include ("../adv/admin/set_passw.php");
include ('common_lab.php');

ini_set('memory_limit', '1024M');

$StatusArr = array ( 0, 10, 20, 30);
$StsArr =array ();

CheckRight1 ('Receipt.Print');

//SELECT 'DoneId', 'ReceiptId', 'OpDate', 'Description', 'Status' FROM 'WMS_Receipt_Done' WHERE 
$Tab='WMS_Receipt_Done';
$WHS = ''; 
$QAdd='';
$User=$_SESSION['login'];
$CargoState='';

$DoneId = addslashes ($_REQUEST['DoneId']);

$query = "SELECT * ".
         "FROM $Tab ".
         "WHERE DoneId ='$DoneId' ";

$sql2 = mysql_query ($query)
               or die("Invalid query:<br>$query<br>" . mysql_error());


$LL=0;
$ReceiptId='';
$dpd=array();
$FirmId='';
if ($dpd = mysql_fetch_array($sql2)) {
  $ReceiptId =$dpd['ReceiptId'];
  $CargoState=$dpd['CargoState'];;
}
else {
  die ("<br>Error: DoneId = $DoneId ");
}


//SELECT 'ReceiptId', 'AutoNo', 'DateUpload', 'PlanReceipt', 'Description', 
// 'ReceiptStatus', 'SupplierId', 'Warehouse', 
// 'ReceiptCompany', 'AgreementId' FROM 'WMS_Receipt' WHERE
$query = "SELECT * ".
         "FROM WMS_Receipt ".
         "WHERE ReceiptId ='$ReceiptId' ";

$sql2 = mysql_query ($query)
               or die("Invalid query:<br>$query<br>" . mysql_error());
$dpr=array ();

if ($dpr = mysql_fetch_array($sql2)) {
  $FirmId   =$dpr['ReceiptCompany'];
}
else {
  die ("<br>Error: ReceiptId = $ReceiptId ");
}

$add_str=$_SESSION['login'].'-'.date('Ymd-His');

//---------------------------------------------------------------------------------
//SELECT `CustomerId`, `CustomerName`, `CustomerStatus`, `CustomerNameEng`, 
//`INN`, `KPP`, `LegalAddr`, `BIK`, `BankName`, `TransitAccount`, 
//`BankAccount`, `Phone`, `LongName` FROM `MpxCustomer` WHERE 1
$query = "SELECT * ".
         "FROM MpxCustomer ".
         "WHERE CustomerId ='$FirmId' ";

$sql2 = mysql_query ($query)
               or die("Invalid query:<br>$query<br>" . mysql_error());


$CustArr=array();
$AddrTxt ='';
if ($CustArr = mysql_fetch_array($sql2)) {
  $AddrTxt=$CustArr['LegalAddr'];
}

//---------------------------------------------------------------------------------

$objPHPExcel = new PHPExcel();
  
$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
			     ->setLastModifiedBy($_SESSION['login'])
			 ->setTitle("Legrand Russia Receipt Act")
			 ->setSubject("Receipt Act")
			 ->setDescription("Legrand Russia Receipt Act")
			 ->setKeywords("office PHPExcel")
			 ->setCategory("Legrand Receipt Act");


$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$Cols = array ('A','B','C','D','E', 'F', 'G', 'H', 'I', "J");

$LastCol="J";

  $CW = array  (4, 10, 21, 35, 9, 11, 9, 9, 7, 7);
  foreach ( $Cols as $Indx => $C ) {
    $aSheet->getColumnDimension($C)->setWidth($CW[$Indx]);
  }
  
  $aSheet->mergeCells('B2:I2');
  $aSheet->getStyle('B2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  
  $styleArray = array(
  'borders' => array(
    'left' => array(
      'style' => PHPExcel_Style_Border::BORDER_NONE,
    ),
    'right' => array(
      'style' => PHPExcel_Style_Border::BORDER_NONE,
    ),
    'bottom' => array(
      'style' => PHPExcel_Style_Border::BORDER_NONE,
    ),
    'top' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
    ),
  )
  );

  $aSheet->getStyle('B2:I2')->applyFromArray($styleArray);
     
  $aSheet->getStyle('B2:I2')->getFont()->setSize(8);
  $aSheet->getRowDimension(2)->setRowHeight(9);
  
  $aSheet->setCellValue('B1', $dpd['ReceiptOrgTxt'].', '.$AddrTxt);
  $Res= iconv('Windows-1251', 'UTF-8', "организация, адрес"); 
  $aSheet->setCellValue('B2', $Res);

  $aSheet->mergeCells('B4:I4');
  $aSheet->mergeCells('B5:I5');
  $aSheet->mergeCells('B6:I6');

  $aSheet->getStyle('B4:I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  
  $Dt = substr($dpd['OpDate'], 8, 2).'/'.substr($dpd['OpDate'], 5, 2).'/'.substr($dpd['OpDate'], 0, 4);
  $Res= iconv('Windows-1251', 'UTF-8', "Акт N $DoneId от ".$Dt); 
  $aSheet->setCellValue('B4',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'О ПРИЕМКЕ И УСТАНОВЛЕННОМ РАСХОЖДЕНИИ ПО');
  $aSheet->setCellValue('B5',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'КОЛИЧЕСТВУ И КАЧЕСТВУ ИМПОРТНЫХ ТОВАРОВ');
  $aSheet->setCellValue('B6',  $Res);

  $aSheet->getStyle("B4:I6")->getFont()->setBold(true);

  $Res= iconv('Windows-1251', 'UTF-8', 'Место приемки товара:');
  $aSheet->setCellValue('B8',  $Res);
  $aSheet->setCellValue('D8',  $dpd['PlaceReceiptTxt']);

  $Res= iconv('Windows-1251', 'UTF-8', 'Инвойс №');
  $aSheet->setCellValue('B9',  $Res);
  $aSheet->setCellValue('D9',  $dpd['PlaceReceiptTxt']);

  $Res= iconv('Windows-1251', 'UTF-8', 'Грузоотправитель:');
  $aSheet->setCellValue('B10',  $Res);
  $aSheet->setCellValue('D10',  $dpd['ShipperTxt']);

  $Res= iconv('Windows-1251', 'UTF-8', 'Поставщик:');
  $aSheet->setCellValue('B11',  $Res);
  $aSheet->setCellValue('D11',  $dpd['SupplierTxt']);

  $Res= iconv('Windows-1251', 'UTF-8', 'Получатель:');
  $aSheet->setCellValue('B12',  $Res);
  $aSheet->setCellValue('D12',  $dpd['ReceiptOrgTxt']);

  $Res= iconv('Windows-1251', 'UTF-8', 'По договору:');
  $aSheet->setCellValue('B13',  $Res);
  $aSheet->setCellValue('D13',  $dpd['AgreementIdTxt']);

  $Res= iconv('Windows-1251', 'UTF-8', 'Вид транспорта:');
  $aSheet->setCellValue('B14',  $Res);
  $Res= iconv('Windows-1251', 'UTF-8', 'автомобильный');
  $aSheet->setCellValue('D14',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'Дата прихода:');
  $aSheet->setCellValue('B15',  $Res);
  $aSheet->setCellValue('D15',  $dpd['OpDate']);

  $Res= iconv('Windows-1251', 'UTF-8', 'Внутр № документа:');
  $aSheet->setCellValue('B16',  $Res);
  $aSheet->setCellValue('D16',  $dpd['AgreementIdTxt']);

  //=======================================================================

  $aSheet->getStyle('A17:J18')->getAlignment()->setWrapText(true); 
  $aSheet->getStyle('A17:J18')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

  $Res= iconv('Windows-1251', 'UTF-8', '№ П/П');
  $aSheet->mergeCells('A17:A18');
  $aSheet->setCellValue('A17',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'Артикул');
  $aSheet->mergeCells('B17:B18');
  $aSheet->setCellValue('B17',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'Наименование товара');
  $aSheet->mergeCells('C17:D18');
  $aSheet->setCellValue('C17',  $Res);

  $aSheet->getStyle('E17:E18')->getFont()->setSize(9);
  $Res= iconv('Windows-1251', 'UTF-8', 'Единица измерения');
  $aSheet->mergeCells('E17:E18');
  $aSheet->setCellValue('E17',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'Количество');
  $aSheet->mergeCells('F17:J17');
  $aSheet->setCellValue('F17',  $Res);
  
  $aSheet->getStyle('F18:J18')->getFont()->setSize(9);

  $Res= iconv('Windows-1251', 'UTF-8', 'По документам поставщика');
  $aSheet->setCellValue('F18',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'Фактически получено');
  $aSheet->setCellValue('G18',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'Недостача');
  $aSheet->setCellValue('H18',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'Излишки');
  $aSheet->setCellValue('I18',  $Res);

  $Res= iconv('Windows-1251', 'UTF-8', 'Брак и бой');
  $aSheet->setCellValue('J18',  $Res);


  //=======================================================================

  
$i=0;  
$FL=3;
$L=$FL;

  // SELECT `DoneId`, `LineNo`, `ReceiptLineNo`, `Qty`, `GTD`, `GTDLineNo` FROM 
  //`WMS_Receipt_Done_Line` WHERE 1

  $query = "select R.ItemNo, R.InvoiceNo, R.DN, L.Qty, L.QtyDefect, R.QtyPlan ".
         "FROM WMS_Receipt_Done_Line L, WMS_Receipt_Line R ".
         " WHERE (L.DoneId='$DoneId') and (R.ReceiptId='$ReceiptId') and ".
         " (L.ReceiptLineNo=R.LineNo) ".
         " order by DoneId, L.LineNo ";

  // SELECT `ReceiptId`, `LineNo`, `ItemNo`, `QtyPlan`, `QtyReceived`, 
  //`Price`, `Amount`, `PO`, `DN`, `EngName`, `NettoWeight`, `GrossWeight`, 
  //`TotNettoWeight`, `TotGrossWeight`, `CO`, 
  //`InvoiceNo`, `InvoiceLineNo`, `CustomsCode` FROM `WMS_Receipt_Line` WHERE 1


  $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

$L=18;
$i=0;
$FL=17;
$InvArr=array ();
$DNArr=array();
while ($dp1 = mysql_fetch_array($sql2)) {
  $i++;
  $L++;
  $InvArr[$dp1['InvoiceNo']]=1;
  $DNArr[$dp1['DN']]=1;
  
  $aSheet->setCellValue("A$L", $i); 
  //$aSheet->setCellValue("A$L", "'".$dp1['ItemNo']); 
  $j=1;

  $aSheet->setCellValueByColumnAndRow( $j, $L, $dp1['ItemNo'].' '); $j++; 
  
  $aSheet->mergeCells("C$L:D$L");
  $ItemName  = GetItemName ($dp1['ItemNo']);
  $aSheet->setCellValueByColumnAndRow( $j, $L, $ItemName);$j++;

  $j++;
  $Res= iconv('Windows-1251', 'UTF-8', 'ШТ');
  $aSheet->setCellValueByColumnAndRow( $j, $L, $Res );$j++;
   
  $aSheet->setCellValueByColumnAndRow( $j, $L, $dp1['QtyPlan'] );$j++;
  $Pl = $dp1['QtyPlan'];
  $Qty =$dp1['Qty'];

  $aSheet->setCellValueByColumnAndRow( $j, $L, $dp1['Qty'] );$j++;
  
  if ( $Pl > $Qty) {
    $aSheet->setCellValueByColumnAndRow( $j, $L, $Pl - $Qty );
  }
  $j++;
  if ( $Pl < $Qty) {
    $aSheet->setCellValueByColumnAndRow( $j, $L, $Qty-$Pl);
  }
  $j++;
  if ($dp1['QtyDefect'] != 0) {
    $aSheet->setCellValueByColumnAndRow( $j, $L, $dp1['QtyDefect']);
  }
  //$aSheet->setCellValue("E$L", GetEnum ('ItemType',$dp1['ItemType'])); 

  $ItemNo=$dp1['Reference'];

} 


$j=4;
$L2=$L+1;
$aSheet->getStyle("E$L2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$Res= iconv('Windows-1251', 'UTF-8', 'Состояние товара, тары и упаковки в момент осмотра:');
$aSheet->setCellValueByColumnAndRow( $j , $L2, $Res );

$aSheet->setCellValueByColumnAndRow( $j+1 , $L2, $CargoState );


$L2++;
$Res= iconv('Windows-1251', 'UTF-8', 'Правильность количества и качества товара подтверждены');
$aSheet->setCellValueByColumnAndRow( $j , $L2, $Res );
$L2+=3;
$Res= iconv('Windows-1251', 'UTF-8', 'Представитель ООО "Фирэлек" ______________________________________');
$aSheet->setCellValueByColumnAndRow( 3 , $L2, $Res );






$Res='';
foreach ($InvArr as $Inv=>$V) {
  if ($Res!='') $Res.=', ';
  $Inv1= $Inv;
  if ( (substr($Inv1, 0, 3)== 'M15') OR (substr($Inv1, 0, 3)== 'M15')) {
    $Inv1= substr ($Inv1, 0 , 3).'-'.substr($Inv1, 3); 
  }
  $Res.= $Inv1;
}

$aSheet->setCellValue('D9',  $Res.' ');

$Res='';
foreach ($DNArr as $Inv=>$V) {
  if ($Res!='') $Res.=', ';
  $Inv1= $Inv;
  if ( (substr($Inv1, 0, 2)== 'FI') ) {
    $Inv1= substr($Inv1, 2); 
  }
  $Res.= $Inv1;
}

$aSheet->setCellValue('D16',  $Res.' ');
 

$styleArray = array(
    'borders' => array(
          'outline' => array(
  		'style' => PHPExcel_Style_Border::BORDER_THIN 
 		),
        'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_HAIR
         )
		//'color' => array('argb' => 'FFFF0000'),
		
	),
  );

  $styleArray1 = array(
    'borders' => array(
          'outline' => array(
  		'style' => PHPExcel_Style_Border::BORDER_HAIR
 		),
        'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_HAIR
         )
		//'color' => array('argb' => 'FFFF0000'),
		
	),
  );
  
  $aSheet->getStyle("A$FL:$LastCol$L")->applyFromArray($styleArray);
  

  $objPageSetup = new PHPExcel_Worksheet_PageSetup();
  $objPageSetup->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
  
  //$objPageSetup->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
  $objPageSetup->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
  $aSheet->setPageSetup($objPageSetup);

    $pageMargins = $aSheet->getPageMargins();

    // margin is set in inches (0.5cm)
    $margin = 0.5 / 2.54;
    $pageMargins->setTop($margin);
    $pageMargins->setBottom($margin);
    $pageMargins->setLeft($margin * 6);
    $pageMargins->setRight($margin);
    $objPageSetup->setFitToWidth(1);
    $objPageSetup->setFitToHeight(1);  
   
   
  //echo '<br>'.$L.' lines to XLS<br>';

  MakeAdminRec ($_SESSION['login'], 'PL_ITEM', 'PRN ITM', 
                        'PRINT', "Print XLS item list $i lines");


  $writer = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007');
  //$writer->save($real_dir.'Lab'.$add_str.'.xls');
  header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
  header('Cache-Control: no-store, no-cache, must-revalidate');
  header('Cache-Control: post-check=0, pre-check=0', false);
  header('Pragma: no-cache');
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename=ReceiptAct-'.$add_str.'.xlsx');
  $writer->save('php://output');

?>