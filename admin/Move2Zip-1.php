<?php
/*
session_start();
isset($_SESSION['login']) or 
  die('You are not login.<br>'.
        iconv('Windows-1251', 'UTF-8', 'Вы не вошли').'<br><br>'.
      '<a href="../admin/index.php">Login page<br>'.
      iconv('Windows-1251', 'UTF-8', 'Страничка для входа').'</a>');
*/
include "../adv/admin/set_passw.php"
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Mnf Label Print</title></head>
<body>
<?php
include ("../adv/setup/config.php");

//print_r ($_REQUEST);


$MakeCopy=$_REQUEST['MK'];

$filename = "";
$dir='';

  $filename = "../f#les/auto_DbDump_$AddName.zip";
  $dir='../f#les/dbdump';


$files = scandir($dir);

//print_r ($files);


$j=0;
$AddToZip='';
$DelFiles=array ();
foreach ($files as $File) {
  $AddFile=0;
  

  if ($AddFile==0) {
    $i=strpos( $File, '.sql');
    if ($i!==false) {
      $AddFile=1;
    }
  }


  if (($AddFile==1) and ($j<10)) {
    $j++;
    $zip = new ZipArchive();
    $AddName=date('Ymd-His')."-$j";

    $filename = "../f#les/dbdump/auto_DbDump_$AddName.zip";

    if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
      exit("cannot open <$filename>\n");
    }

    $zip->addFile($dir . "/$File");
    echo ("<br>$j $File ");
    $DelFiles[]= $dir . "/$File" ;
    $zip->close();
    unlink ($dir . "/$File") ;    
  
  }
}

echo ('<br> Done');

?>
</body>
</html>
				       