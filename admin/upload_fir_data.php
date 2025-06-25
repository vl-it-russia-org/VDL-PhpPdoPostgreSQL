<?php
session_start();
isset($_SESSION['login']) or die('вы не вошли');
isset($_SESSION['admin_login']) or die('вы не вошли');
?>

<html>
<head><title>Upload Items free stock from XML file</title></head>
<body>
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




echo "Здравствуйте, ".$_SESSION['login']."<br>";

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];
$dir = BASE_DIR;
$real_name = "$dir/admin/tmp/file_tmp_fir.zip";
$real_extract = "$dir/admin/tmp/fir_dir";

$company="FIR";

echo ("$real_name<br>");

if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded<br>");

  $zip = new ZipArchive;
  if ($zip->open($real_name) === TRUE) {
      $zip->extractTo($real_extract);
      $zip->close();
      echo 'ok<br>';
  } else {
      echo 'failed<br>';
  }

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

  AdminFooter ();
?>
</body>
</html>
