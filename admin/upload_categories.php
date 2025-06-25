<?php
session_start();
isset($_SESSION['login']) or die('вы не вошли');
isset($_SESSION['admin_login']) or die('вы не вошли');
?>

<html>
<head><title>Upload Items free stock from CSV file</title></head>
<body>
<?php
include ("../setup/config.php");
include "set_passw.php";

echo "Здравствуйте, ".$_SESSION['login']."<br>";

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];
$dir = BASE_DIR;
$real_name = "$dir/admin/tmp/file_tmp.csv";
$company="KNT";

echo ("$real_name<br>");


if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded<br>");

  $sql = mysql_query ("select MAX(ord) as LG from reservation");
  $log_id = 0;
  if ($dp = mysql_fetch_object($sql)) {
    $log_id = $dp->LG + 10;
  };	

  
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
                        $log_id, 'Upload file '.$i.' lines');

  
};

  AdminFooter ();
?>
</body>
</html>
				       