<?php
include ("../setup/config.php");
include "set_passw.php";


$dir= BASE_DIR;
$real_dir = "$dir/files/";
$real_name = "ord_corresp_back.csv";

$handle = @fopen($real_dir.$real_name, "r");
if ($handle) {
  $i = 1;
  $j = 0;
  while (!feof($handle)) {
    $buffer = fgets($handle, 4096);
    if (strlen ($buffer) !=0) {
      list($company,$leg_ord,$CONr) =  split(";", $buffer, 4);
      
      $query = "update orders_correspondence SET MapicsCO='$CONr' where company='$company' and ".
               " order_leg_full ='$leg_ord'";
       	
      $sql2 = mysql_query ($query)
	    or die("Invalid query:<br>$i $query<br>" . mysql_error()); 
    }
    $i = $i + 1;
  }
  echo("updated $i lines<br>");
  fclose($handle);
}
else {
  echo ("Error in open file ".$real_dir.$real_name);
};
	
?>