<?php
include ("../setup/config.php");
include "set_passw.php";


$dir= BASE_DIR;
$real_name = "$dir/admin/tmp/file_tmp_fir.xx";
$real_extract = "$dir/files";

$company= 'KNT';


if (1 == 1 ) {
  echo ("Document downloaded<br>");

  $files1 = scandir($real_extract);
  
  foreach ($files1 as $f) {
    if ( !( ($f=='.')    || 
            ($f=='..')   ||
            ($f=='index.php') )) { 
      $x= strpos ( $f,  'free_txt');
      if ( $x !== false) {
      $sql = mysql_query ("select MAX(ord) as LG from reservation");
      $log_id = 0;
      if ($dp = mysql_fetch_object($sql)) {
        $log_id = $dp->LG + 10;
      };	

      $XML_file= $real_extract.'/'. $f; 
      $real_name = $XML_file;
      print ('file '.$f.'<br>');
  
      $handle = @fopen($real_name, "r");
      if ($handle) {
        $i = 1;
        $j = 0;
        while (!feof($handle)) {
          $buffer = fgets($handle, 4096);
          if (strlen ($buffer) !=0) {
	   list($itemNo,$Qty1,$Qty2) =  split(";", $buffer, 4);
      
           $itemNo = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($itemNo));
      
           $query = "INSERT INTO reservation (ord, reference, Company, Qty1, Qty2) VALUES 
	      ($log_id, '$itemNo', '$company', $Qty1, $Qty2)";
	
	  $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$i $query<br>" . mysql_error()); 
          }
  	  $i = $i + 1;
	}
      };
	
      echo("uploaded $i lines<br>");
     

      fclose($handle);
     
     $query = "DELETE from reservation WHERE (ord < $log_id) and (company='$company')";
	
     $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$i $query<br>" . mysql_error());

        
     MakeAdminRec ($_SESSION['admin_login'], 'UPLD_FL', $name_temp, 
                        $log_id, 'Upload Kontaktor file '.$i.' lines');

     unlink ($XML_file);
	
        
    }
    }

  };
  
};


?>