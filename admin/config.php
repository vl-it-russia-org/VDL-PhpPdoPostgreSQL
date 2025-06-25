<?php
define ('BASE_DIR', '/home/vladlev/public_html/legrand');
$TmpDir = '/var/var/macro';
$TmpFtp = '/var/var/ftp';


date_default_timezone_set('Europe/Moscow');

ini_set('display_errors', 1);


$BaseHost='https://project.kontaktor.ru/legrand/';

$db_host = "localhost";
$db_base = "legrand";
$sf_base='https://eu1.salesforce.com/';


$dp = mysql_connect ( $db_host , "vladlev_adv" , "@Go.m.-UD%L~")
 or die("Could not connect: " . mysql_error());
 
mysql_select_db ( $db_base, $dp);
mysql_query("SET NAMES 'utf8'")
 or die("Could not set UTF8: " . mysql_error());

mysql_query("SET time_zone = '+3:00'")
 or die("Could not set UTF8: " . mysql_error());


$default_lang = "RU";


function vdlGetStr ($str_name, $default_lang) {
  $result = "";
 
  $sql = mysql_query ("select str_val from VDL_MESSAGES 
    where lang_id ='$default_lang' and str_name = '$str_name'");
  if ($dp = mysql_fetch_object($sql)) 
    $result = $dp -> str_val;
  return $result;    
};




function ShowParams ($art, $def_lang) {

  $param_qry = " SELECT PN.param_name as PNAME, PV.param_no as PNO, PV.param_val as PVAL
FROM VDL_PARAM_VALS PV, VDL_PARAM_NAMES PN
WHERE (PV.item_no = '$art') AND 
      (PN.PARAM_NO = PV.PARAM_NO) AND 
      (PN.lang_id = '$def_lang')";
//  echo ($param_qry);
  
  $sql = mysql_query ($param_qry);
  $i = 0;
  while ($dp = mysql_fetch_object($sql)) {
    if ($i == 0 ) {
      $i = 1;
      echo (" <table width=\"50%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\">");
    };
    echo (" <tr> <td>$dp->PNO</td> <td>$dp->PNAME</td>
                 <td>$dp->PVAL</td></tr>");
  }	
  if ($i==1) {
    echo ("</table>");
  };    

}


?>