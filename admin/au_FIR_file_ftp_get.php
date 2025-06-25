<?php
include ("../setup/config.php");
include "set_passw.php";


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

$BeginStr =  'art_produit_ETM';
$company='FIR';

for ($i = 0; $i < $len; $i++) {
  $fname = $contents [$i];
  $x = strrpos($fname, '.flag');
  if ( $x !== false ) {
    if ( strpos ($fname, $BeginStr) !== false) {   
      $FNLen = strlen ( $fname ); 
      $fname = substr ($fname, 0, -5);
      $part_name=substr ($fname, strlen ($BeginStr)+1 ); 
 
      
      $query = "INSERT INTO file_copy (company_id, file_part_name, file_full_name, status) VALUES 
                  ('$company', '$part_name', '$fname', 1)";
       	
      $sql2 = mysql_query ($query)
                or die("Invalid query:<br>$i $query<br>" . mysql_error()); 
    }  
  }
}

$query = "update file_copy set status=2 where (status=1) and (company_id = '$company')";


$sql2 = mysql_query ($query)
                or die("Invalid query:<br>$i $query<br>" . mysql_error()); 

  $sql3 = "select id, file_full_name from file_copy 
    where (company_id = '$company') and (status=2) 
    order by company_id, status, file_part_name desc" ;
  
  //echo $sql3."<br>";

  $sql = mysql_query ($sql3);
  $fname='';
  $first_time = 0;

  //echo $company."<br>";
  while ($dp = mysql_fetch_object($sql)) {
    //print_r ($dp);
    //echo "<br>";
    if ( $first_time < 1 ) { 
      
      $first_time = $first_time +1 ; 
      //echo $first_time."<br>";
 
      $fname = $dp->file_full_name;
      $local_file=$real_extract.'/'.$fname;

      
      if (ftp_get($conn_id, $local_file, $fname, FTP_BINARY)) {
        echo "Successfully written to $local_file<br>";
        echo "<a href='au_BT_file_integrate.php'>Next step: Integrate BT file</a><br>";

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

ftp_close($conn_id);
