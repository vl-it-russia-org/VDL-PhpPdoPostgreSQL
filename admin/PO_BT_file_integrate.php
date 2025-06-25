<?php
include ("../setup/config.php");
include "set_passw.php";


function GetXMLItem ($ItemName, $Buf) {
  $pos = strpos ( $Buf, $ItemName.'="');
  $res='';
  $len = strlen ($ItemName.'="'); 
  if ( $pos === false) {
  }
  else {
    $lastchar = strpos ( $Buf, '"', $pos+$len);
    $res= substr ($Buf, $pos+$len, $lastchar- ($pos+$len));
    //print ($res.' '.$pos.' '.$len.'<br>'); 
  }
  return ($res);
};

       
$dir= BASE_DIR;
$real_name = "$dir/admin/tmp/file_tmp_fir.xx";
$real_extract = "$dir/admin/tmp/fir_dir";

$company= 'BT';

$LineNo=0;

if (1 == 1 ) {
  echo ("Document downloaded<br>");
  echo "<a href='PO_FIR_file_integrate.php'>Next step: Integrate FIR file</a><br>";

  $files1 = scandir($real_extract);
  
  foreach ($files1 as $f) {
    if ( !( ($f=='.')    || 
            ($f=='..')   ||
            ($f=='index.php') )) { 
      $x= strpos ( $f,  'lig_appro_ETM_');
      if ( $x !== false) {
      $sql = mysql_query ("select MAX(ord) as LG from reservation");
      $log_id = 0;
      if ($dp = mysql_fetch_object($sql)) {
        $log_id = $dp->LG;
      };	

      $XML_file= $real_extract.'/'. $f; 
      print ('file '.$f.'<br>');
  
      $handle = @fopen($XML_file, "r");
      
      if ( $handle ) {
        $i = 1;
        $j = 0;
        while (!feof($handle)) {
          $buffer = fgets($handle, 4096);
          $itemNo='';
          $LineNo++;
          if (strlen ($buffer) !=0) {
            $itemNo=GetXMLItem ('PDT_CNUMART', $buffer);
            if (!empty($itemNo)) {

	      $Qty1=GetXMLItem ('APP_QTT', $buffer);
	      $ExpDate1=GetXMLItem ('APP_DATE_DISPO', $buffer);
	      $ExpDate = substr ($ExpDate1, 0, 4).'-'.
	                 substr ($ExpDate1, 4, 2).'-'.
	                 substr ($ExpDate1, 6, 2) ;
              $Qty2='0';
              
            //list($itemNo,$Qty1,$Qty2) =  split(";", $buffer, 4);
            //$itemNo = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($itemNo));
      
              $query = "INSERT INTO MapicsPO(LogId, ItemNo, LineNo, Qty, ExpDate) VALUES ".
                       "($log_id, '$itemNo', $LineNo, $Qty1, '$ExpDate')";  
              
              $sql2 = mysql_query ($query)
                       or die("Invalid query:<br>$i $query<br>" . mysql_error()); 
            };
          }
  	  $i = $i + 1;
	}
	
	
        echo("File $f uploaded $i lines<br>");
     

        fclose($handle);
        unlink ($XML_file);
        echo("File $XML_file lines<br>");
        
        $query = "DELETE from reservation WHERE (ord < $log_id) and (company='$company')";

	
	$sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$i $query<br>" . mysql_error());

        
        MakeAdminRec ($_SESSION['admin_login'], 'UPLD_FL', $f, 
                        $log_id, 'Upload Bitichino XML file '.$i.' lines');
        };
        };
     }

  };
  
};

?>
