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
include ("../setup/config.php");
include ("../admin/set_passw.php");



//echo('<br>');
//print_r ($_REQUEST);
//die ();

$DateBeg  = addslashes($_REQUEST['DateBeg']);
$DateEnd  = addslashes($_REQUEST['DateEnd']);
$Who      = addslashes($_REQUEST['Who']);
 
if ( ($DateBeg=='') OR ($DateEnd=='')){
    die ('<br>'.GetStr('No params').'</body></html>');
};

$add_str=$_SESSION['login'].date('Ymd-His');

//---------------------------------------------------------------
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
			     ->setLastModifiedBy($_SESSION['login'])
			 ->setTitle("Legrand Russia report:Web statistic")
			 ->setSubject("Web statistic")
			 ->setDescription("Requests to Stock levels Web site")
			 ->setKeywords("office PHPExcel")
			 ->setCategory("Web Statistic");


  $objPHPExcel->setActiveSheetIndex(0);
  $aSheet = $objPHPExcel->getActiveSheet();

    $aSheet->getColumnDimension('A')->setWidth(33);
    $aSheet->getColumnDimension('B')->setWidth(10);
    $aSheet->getColumnDimension('C')->setWidth(11);
    $aSheet->getColumnDimension('D')->setWidth(11);
    
    $aSheet->setCellValue('A1', 'Stock levels web site statistic');
    $aSheet->setCellValue('A2', "Filters: Dates $DateBeg:$DateEnd $Who");
    $aSheet->setCellValue('A3', "Created by ". $_SESSION['login'].
                     ' '.date('Y/m/d H:i:s'));




  $Query = "select ip_addr, reference, SUM(qty) QT, SUM(answer='YES') Y, 
                       usr, COUNT(*) CNT 
           from protocol where (op_date >='$DateBeg') and (op_date <='$DateEnd')";

  if ($Who != '') {
    $Query .= " and (usr = '$Who')";
  }

  $Query .= " group by usr, ip_addr, reference "; 
  $sql = mysql_query ($Query)
        or die("Invalid query:<br>$Query<br>" . mysql_error());

  
  $aSheet->setCellValue('A4', "Who did");
  $aSheet->setCellValue('B4', "IP address");
  $aSheet->setCellValue('C4', "Reference");
  $aSheet->setCellValue('D4', "QtyOfReq");
  $aSheet->setCellValue('E4', "Avg Qty");
  $aSheet->setCellValue('F4', "Answer Yes");
  $l=4;

  while ($dp = mysql_fetch_object($sql)) {
    $l++;
    $aSheet->setCellValue('A'.$l, $dp->usr);
    $aSheet->setCellValue('B'.$l, $dp->ip_addr);
    $aSheet->setCellValue('C'.$l, $dp->reference);
    $aSheet->setCellValue('D'.$l, $dp->CNT);
    $aSheet->setCellValue('E'.$l, $dp->QT);
    $aSheet->setCellValue('F'.$l, $dp->Y);
  };
  
  $aSheet->setCellValue('H'.$l, 'END');

  $dir= BASE_DIR;
  $real_dir = "$dir/files/";
  $real_name = "Rep";


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
  
  MakeAdminRec ($_SESSION['login'], 'XLS_STAT', '', 
                        'PRINT', "Print XLS statistic ");
 

  $writer = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel5');
  //$writer->save($real_dir.'Lab'.$add_str.'.xls');
  header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
  header('Cache-Control: no-store, no-cache, must-revalidate');
  header('Cache-Control: post-check=0, pre-check=0', false);
  header('Pragma: no-cache');
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename=Rep-'.$add_str.'.xls');
  $writer->save('php://output');
?>