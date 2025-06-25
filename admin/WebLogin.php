<?php
session_start();

include ("../setup/config.php");
include ("set_passw.php");

$Usr=addslashes ($_COOKIE['AL']);
if ($Usr=='') {
  die ("<br> Bad Web Login");
}

$Tab='WebAutoLogin';



$query = "select * from $Tab ".
         "where AL='$Usr'";

$sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

$IP=$_SERVER['REMOTE_ADDR'];

if ($dp = mysql_fetch_array($sql2)) {
  $Pass=$_REQUEST['pass'];
  $Date= $dp['UsrSetDate'];
  $User= $dp['UsrName'];
  $chk=md5($User.$Date.'Vlad'.$Pass);
  //echo ("<br>$chk<br>{$dp['CheckPhrase']} "); 
  if ($chk != $dp['CheckPhrase']) {
    $Err = $dp['CheckCnt'];
    $Err++;
    
    if ($Err > 2 ) {
      $query = "delete from $Tab where AL='$Usr'";
      setcookie ("AL", "", time() - 3600);
      MakeAdminRec ($User, 'LoginBad', $IP, 
                        'Web', 'Web Login disabled '.$IP);
    }
    else {
      $query = "update $Tab  set CheckCnt=$Err where AL='$Usr'";
      echo ("<br> Wrong password. <b>$Err</b> try. Max 3 times "); 
    }
    $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());
  }
  else {
    if ($dp['CheckCnt']>0) {
      $query = "update $Tab  set CheckCnt=0 where AL='$Usr'";

      $sql2 = mysql_query ($query)
                  or die("Invalid query:<br>$query<br>" . mysql_error());
    }
    
    $_SESSION['login']=$User; 
    MakeAdminRec ($User, 'Login', $IP, 
                        'Web', 'Login user from '.$IP);
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<META HTTP-EQUIV="REFRESH" CONTENT="0;URL=index.php">
<title>Mnf Label Print</title></head>
<body>');
    echo ("<br> Login Ok.");
  }
}
else {
  echo ("<br> Bad username. Reset Web login");

}

?>