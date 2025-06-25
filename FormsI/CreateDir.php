<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Create dir</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($pdo, 'Admin');

//print_r($_REQUEST);
//die();

$HDir=trim($_REQUEST['HDir']);
$Dir1=trim($_REQUEST['Dir1']);
$Dir2=trim($_REQUEST['Dir2']);


$DirName=trim($_REQUEST['DirName']);

if (!file_exists($HDir)) {
  die ("<br> Error: $DirName is not exists");
}

if (file_exists("$HDir/$DirName")) {
  die ("<br> Error: $HDir/$DirName already exists");
}

if (!is_dir("$HDir")) {
  die ("<br> Error: $HDir is not FOLDER");
}

$NewDir="$HDir/$DirName";


mkdir($NewDir);

if ($Dir1=='') {
  $Dir1=$NewDir;
}

if ($Dir2=='') {
  $Dir2=$NewDir;
}


echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=EdiDispatchedFiles.php?Dir1='.$Dir1.
'&Dir2='.$Dir2.'">'.
'<title>Save</title></head>
<body>');

echo ("<br> Created new dir= $NewDir<br>");

?>
</body>
</html>			       