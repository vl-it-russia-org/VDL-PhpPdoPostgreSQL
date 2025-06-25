<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $FldNames=array('Id','Year','LastNo');
$New=addslashes($_REQUEST['New']);
$Id=addslashes($_REQUEST['Id']);
if ($Id==''){ die ("<br> Error:  Empty Id");}
$Year=addslashes($_REQUEST['Year']);
if ($Year==''){ die ("<br> Error:  Empty Year");}

$dp=array();
  
  $query = "select * ".
         "FROM AdmNumberSeqYear ".
         " WHERE (Id='$Id') AND (Year='$Year')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  else {
    die ("<br> Error: not found record (Id='$Id') AND (Year='$Year')"); 
  }
  $Editable=1;
  if (!Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete ".
         "FROM AdmNumberSeqYear ".
         " WHERE (Id='$Id') AND (Year='$Year')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);  
$LNK='';

  $V=$_REQUEST['Id'];
  $LNK.="Id=$V";
  
  $V=$_REQUEST['Year'];
  $LNK.="&Year=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=AdmNumberSeqYearList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>