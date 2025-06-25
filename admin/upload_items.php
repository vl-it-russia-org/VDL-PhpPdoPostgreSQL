
<html>
<head><title>Upload Items from CSV file</title></head>
<body>
<?php
include ("../setup/config.php");
echo ("1<br>");

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];
$dir = BASE_DIR;
$real_name = "$dir/admin/tmp/file_tmp.csv";

echo ("$real_name<br>");


if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded<br>");
  
  $sql = mysql_query ("select MAX(log_id) as LG from VDL_LOG");
  $log_id = 0;
  if ($dp = mysql_fetch_object($sql)) {
    $log_id = $dp->LG + 10;
    $ins_qry = "insert into VDL_LOG (log_id, log_msg, param_no) values ($log_id, 'Begin', 0)";
    $sql1 = mysql_query ($ins_qry);
    echo ("WWW --- $log_id $ins_qry<br>");
  };	
  
  $handle = @fopen($real_name, "r");
  if ($handle) {
    $i = 1;
    $j = 0;
    while (!feof($handle)) {
      $buffer = fgets($handle, 4096);
      if (strlen ($buffer) !=0) {
	list($art,$descr,$DesCode,$NewDescode,$NetWeight,$Weight,$Silver,$Category,$ProdGroup,
	   $BomNo,$Coat,$LegrandGroup,$Article1C,$SimpleName,$St1)=  split(";", $buffer, 15);
	
	if ($i > 1 ) {    
	$query = "DELETE FROM VDL_ITEMS WHERE ARTICLE= '$art'";
	$sql1 = mysql_query ($query);
	$j = $j + 1;
	IF ($j == 10)  {
	  $ins_qry = "update  VDL_LOG set param_no = $i where log_id = $log_id";
          $sql1 = mysql_query ($ins_qry);
	  $j = 0;
	}
	if ($art != "_PARAM_") {   

	$descr      = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($descr));
	$DesCode    = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($DesCode));
	$NewDescode = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($NewDescode));
	
	$Article1C   = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($Article1C));
	$SimpleName  = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($SimpleName));
	$ProdGroup   = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($ProdGroup));

	$NetWeight = str_replace("'", "", $NetWeight);
	$Weight = str_replace("'", "", $Weight);
	$Silver = str_replace("'", "", $Silver);
	
	$query = "INSERT INTO VDL_ITEMS (ARTICLE,DESCRIPTION,DES_CODE,DES_CODE_NEW,NET_WEIGHT,WEIGHT, 
	      SILVER,CATEGORY,PRODUCTION_GROUP,BOM_NO,COAT,LEGRAND_GROUP,ART_1C,SIMPLE_NAME) VALUES 
	      ('$art','$descr','$DesCode','$NewDescode',$NetWeight,$Weight,$Silver, 
	       '$Category','$ProdGroup','$BomNo',$Coat,'$LegrandGroup','$Article1C','$SimpleName')";
	
	$sql2 = mysql_query ($query)
	  or die("Invalid query:<br>$i $query<br>" . mysql_error()); ;
	}
	else {
    	    //list($art,$descr,$DesCode,$NewDescode,$NetWeight,$Weight,$Silver,$Category,$ProdGroup,
	    //$BomNo,$Coat,$LegrandGroup,$Article1C,$SimpleName,$St1)=  split(";", $buffer, 15);
	      
  	  $query = "DELETE FROM VDL_ITEMS WHERE ARTICLE= '$art'";
	  $sql1 = mysql_query ($query);
	  $art      = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($descr));
	  $param_no  = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($DesCode));
	  $param_val = iconv( "WINDOWS-1251", "UTF-8", mysql_real_escape_string($NewDescode));
  	  
	  $query = "DELETE FROM VDL_PARAM_VALS WHERE item_no = '$art' AND param_no = '$param_no'";
	  $sql1 = mysql_query ($query);
	
	  $query = "INSERT INTO VDL_PARAM_VALS (item_no, param_no, param_val) VALUES
	      ('$art','$param_no','$param_val')";
   	  $sql2 = mysql_query ($query)
	       or die("Invalid query:<br>$i $query<br>" . mysql_error()); ;
	  
	};
	};
	
        $i = $i + 1;
      };
     }
     echo("uploaded $i lines<br>");
     fclose($handle);
  }
};

?>
</body>
</html>
				       