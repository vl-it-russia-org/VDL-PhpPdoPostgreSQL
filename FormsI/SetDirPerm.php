<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>HelpTopicLine Card</title></head>
<body>
<?php

CheckLogin1 ();


CheckRight1 ($pdo, 'Admin');


//if (!file_exists($real_extract.'/done')) {
//  mkdir( $real_extract.'/done');
//}

$Dir1=addslashes($_REQUEST['D']);
if ($Dir1=='') {
  die ("<br>Error: Dir is empty");
}

$NewVal=addslashes($_REQUEST['NewVal']);

if ($NewVal!= '') {
  //echo ("<br> NewVal= $NewVal");
  $Res=chmod($Dir1,$NewVal);
  if ($Res) {
    echo ("<br> Changed Ok <br>");
  }
  else {
    echo ("<br> Changed with error $Dir1, $NewVal <br>");
  }
}




echo ("<h3>$Dir1</h3>");

$perms = fileperms($Dir1);

echo ("<br> Permitions: ".substr(sprintf('%o', $perms), -4)."<br>");

switch ($perms & 0xF000) {
    case 0xC000: // socket
        $info = 's';
        break;
    case 0xA000: // symbolic link
        $info = 'l';
        break;
    case 0x8000: // regular
        $info = 'r';
        break;
    case 0x6000: // block special
        $info = 'b';
        break;
    case 0x4000: // directory
        $info = 'Directory';
        break;
    case 0x2000: // character special
        $info = 'c';
        break;
    case 0x1000: // FIFO pipe
        $info = 'p';
        break;
    default: // unknown
        $info = 'u';
}

// Owner
$info .= '<br>Owner:'.(($perms & 0x0100) ? 'r' : '-');
$info .= (($perms & 0x0080) ? 'w' : '-');
$info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

// Group
$info .= '<br>Group:'.(($perms & 0x0020) ? 'r' : '-');
$info .= (($perms & 0x0010) ? 'w' : '-');
$info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

$NewGroupPerm = $perms | 0x0010 ;

if ( $NewGroupPerm != $perms ) {
  echo ("<br><a href='SetDirPerm.php?D=$Dir1&NewVal=$NewGroupPerm' ".
       "onclick='confirm(\"Group write rights setup for $Dir1 ?\")'>Group W+</a><br>");

  echo ("<br> New Permitions: ".substr(sprintf('%o', $NewGroupPerm), -4)."<br>");
}

// World
$info .= '<br>Other:'.(($perms & 0x0004) ? 'r' : '-');
$info .= (($perms & 0x0002) ? 'w' : '-');
$info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

echo $info;



?>
</body>
</html>