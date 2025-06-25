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


$ftp_server = "ftp.fr.grpleg.com";
$ftp_user = "etm_ru";
$ftp_pass = "HDkhj563";
$dir= BASE_DIR;
$real_name = "$dir/admin/tmp/file_tmp_fir.xx";
$real_extract = "$dir/admin/tmp/fir_dir";
//echo $dir."<br>";

$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server");

// try to login
if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
    //echo "Connected to $ftp_server as user $ftp_user<br>";
  ftp_pasv($conn_id, true);
    } else {
        echo "Couldn't connect as $ftp_user to $ftp_server\n";
        exit (1);

}

if (!ftp_chdir ($conn_id , 'recep' ))  {
  echo "Can't change dir to recep";
  exit (2);
}


$contents = ftp_nlist ($conn_id, ".");
$len = count ($contents);

$BeginArr = Array ( 'art_produit_ETM', 'nav_ETM');
$FirmArr  = Array ( 'FIR', 'KNT');
$ArrLen = 2;


for ($i = 0; $i < $len; $i++) {
  $fname = $contents [$i];
  $x = strrpos($fname, '.flag');
  if ( $x !== false ) {
    $j = 0;
    while (( $j < $ArrLen ) && 
           ( strpos ($fname, $BeginArr[$j]) === false)) {   
      $j++;
    }

    if ( $j < $ArrLen ) { 
      $FNLen = strlen ( $fname ); 
      $fname = substr ($fname, 0, -5);
      $company=$FirmArr[ $j ];
      $part_name=substr ($fname, strlen ($BeginArr [ $j ]) +1); 
 
      
      $query = "INSERT INTO file_copy (company_id, file_part_name, file_full_name, status) VALUES 
                  ('$company', '$part_name', '$fname', 1)";
       	
      $sql2 = mysql_query ($query)
                or die("Invalid query:<br>$i $query<br>" . mysql_error()); 
    };
  }
}

$query = "update file_copy set status=2 where (status=1) and (company_id = '$company')";


$sql2 = mysql_query ($query)
                or die("Invalid query:<br>$i $query<br>" . mysql_error()); 



for ( $i=0; $i < $ArrLen; $i++) {
  $company=$FirmArr[ $i ];

  $sql = mysql_query ("select id, file_full_name from file_copy 
    where (company_id = '$company') and (status=2) 
    order by company_id, status, file_part_name desc");
  $fname='';
  $first_time = 0;
  while ($dp = mysql_fetch_object($sql)) {
    if ( $first_time < 2 ) { 
      $first_time++; 
      $fname = $dp->file_full_name;
      $first_time=false;
      $local_file=$real_extract.'/'.$fname;
      
      if (ftp_get($conn_id, $local_file, $fname, FTP_BINARY)) {
        //echo "Successfully written to $local_file<br>";
        $query = "delete from file_copy where id=".$dp->id;
        $sql3 = mysql_query ($query)
                or die("Invalid query:<br>$i $query<br>" . mysql_error()); 

        ftp_delete ($conn_id, $fname);
        ftp_delete ($conn_id, $fname.'.flag');

      } else {
        echo "There was a problem ftp_get $fname<br>";
        $query = "delete from file_copy where id=".$dp->id;
        $sql3 = mysql_query ($query)
                or die("Invalid query:<br>$i $query<br>" . mysql_error()); 

      }      
    }
    else {
      $fname = $dp->file_full_name;  
      $query = "delete from file_copy where id=".$dp->id;
      $sql3 = mysql_query ($query)
                or die("Invalid query:<br>$i $query<br>" . mysql_error()); 
      ftp_delete ($conn_id, $fname);
      ftp_delete ($conn_id, $fname.'.flag');


    }
  };	
};

ftp_close($conn_id);

if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (1 == 1 ) {
  echo ("Document downloaded<br>");

  $files1 = scandir($real_extract);
  
  foreach ($files1 as $f) {
    if ( !( ($f=='.')    || 
            ($f=='..')   ||
            ($f=='index.php') )) {  
      $sql = mysql_query ("select MAX(ord) as LG from reservation");
      $log_id = 0;
      if ($dp = mysql_fetch_object($sql)) {
        $log_id = $dp->LG + 10;
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
          if (strlen ($buffer) !=0) {
            $itemNo=GetXMLItem ('PDT_CNUMART', $buffer);
            if (!empty($itemNo)) {
	      $Qty1=GetXMLItem ('PDT_QTESTK', $buffer);
              $Qty2='0';
              
            //list($itemNo,$Qty1,$Qty2) =  split(";", $buffer, 4);
            //$itemNo = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($itemNo));
      
              $query = "INSERT INTO reservation (ord, reference, Company, Qty1, Qty2) VALUES 
                  ($log_id, '$itemNo', '$company', $Qty1, $Qty2)";
	
            $sql2 = mysql_query ($query)
                or die("Invalid query:<br>$i $query<br>" . mysql_error()); 
            };
          }
  	  $i = $i + 1;
	}
	
	
        echo("File $f uploaded $i lines<br>");
     

        fclose($handle);
        unlink ($XML_file);
        $query = "DELETE from reservation WHERE (ord < $log_id) and (company='$company')";

	
	$sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$i $query<br>" . mysql_error());

        
        MakeAdminRec ($_SESSION['admin_login'], 'UPLD_FL', $f, 
                        $log_id, 'Upload Firelec XML file '.$i.' lines');
        };
     }

  };
  
};

?>
