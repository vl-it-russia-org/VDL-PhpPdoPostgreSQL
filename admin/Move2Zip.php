<?php
/*
session_start();
isset($_SESSION['login']) or 
  die('You are not login.<br>'.
        iconv('Windows-1251', 'UTF-8', 'Вы не вошли').'<br><br>'.
      '<a href="../admin/index.php">Login page<br>'.
      iconv('Windows-1251', 'UTF-8', 'Страничка для входа').'</a>');
*/
include "../admin/set_passw.php"
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Mnf Label Print</title></head>
<body>
<?php
include ("../setup/config.php");

//print_r ($_REQUEST);

$zip = new ZipArchive();
$AddName=date('Ymd-His');

$MakeCopy=$_REQUEST['MK'];

$filename = "";
$dir='';

if ($MakeCopy=='') { 
  $filename = "../files/auto_REP_$AddName.zip";
  $dir='../files/Reports';
}
else 
if ($MakeCopy=='1') { 
  $filename = "../files/auto_FILES_$AddName.zip";
  $dir='../files';
}
else 
if ($MakeCopy=='3') { 
  $filename = "../files/auto_SENT_$AddName.zip";
  $dir='../../vladlev/msg_ins/ftpfiles/sent';
}
else 
if ($MakeCopy=='5') { 
  $filename = "../files/auto_Attach_$AddName.zip";
  $dir='../../vladlev/msg_ins/ftpfiles/attach';
}
else 
if ($MakeCopy=='7') { 
  $filename = "../files/auto_DbDump_$AddName.zip";
  $dir='../files/dbdump';
}


$files = scandir($dir);

//print_r ($files);

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$j=0;
$AddToZip='';
$DelFiles=array ();
foreach ($files as $File) {
  $AddFile=0;
  
  $i=strpos( $File, '.xlsx');
  if ($i!==false) {
    $AddFile=1;
  }

  if ($AddFile==0) {
    $i=strpos( $File, '.msg');
    if ($i!==false) {
      $AddFile=1;
    }
  }
  
  if ($AddFile==0) {
    $i=strpos( $File, '.csv');
    if ($i!==false) {
      $AddFile=1;
    }
  }
  
  if ($AddFile==0) {
    $i=strpos( $File, '.txt');
    if ($i!==false) {
      $AddFile=1;
    }
  }

  if ($AddFile==0) {
    $i=strpos( $File, '.xls');
    if ($i!==false) {
      $AddFile=1;
    }
  }

  if ($AddFile==0) {
    $i=strpos( $File, '.snd');
    if ($i!==false) {
      $AddFile=1;
    }
  }

  if ($AddFile==0) {
    $i=strpos( $File, '.sql');
    if ($i!==false) {
      $AddFile=1;
    }
  }


  if ($AddFile==1) {
    $zip->addFile($dir . "/$File");
    $j++;
    echo ("<br>$j $File ");
    $DelFiles[]= $dir . "/$File" ;    
  }
}


echo "<br>numfiles: " . $zip->numFiles . "\n";
echo "<br>status:" . $zip->status . "\n";
$zip->close();

echo ('<H3>Delete Files</h3>');
$j=0;
foreach ($DelFiles as $File) {
  $j++;
  echo ("<br>$j $File ");
  unlink ($File) ;    
}
echo ('<br> Done');

?>
</body>
</html>
				       