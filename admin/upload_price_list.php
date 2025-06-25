<?php
session_start();
isset($_SESSION['login']) or die('вы не вошли');
echo 'Здравствуйте, ' . $_SESSION['login'].'<br>';
include "../admin/set_passw.php"
?>

<html>
<head><title>Upload price list CSV file</title></head>
<body>
<?php
include ("../setup/config.php");

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];
$dir = BASE_DIR;
$real_name = "$dir/admin/tmp/CSV_TEST.csv";

$Firm=$_REQUEST['FIRM'];

$sizeStr='';
if ($size> (1024*1024) ) {
  $sizeStr = int($size/1024/1024).'M';
}else{
  if ($size>1024) {
    $sizeStr = int($size/1024).'K';
  }
  else
    $sizeStr = $size.'b';
};



echo ("$real_name<br>");


if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded $Firm<br>");

   MakeAdminRec ($_SESSION['admin_login'], 'PRICE_LIST_SF', $sizeStr, 
                        $Firm, 'Uploaded pricelist file for Sales Force');

  
  $sql = mysql_query ("UPDATE  read_files SET  last_line_no = 1, added_qty = 0 ".
                      " WHERE  `read_files`.`firm` =  '$Firm'");
};

?>
</body>
</html>
				       