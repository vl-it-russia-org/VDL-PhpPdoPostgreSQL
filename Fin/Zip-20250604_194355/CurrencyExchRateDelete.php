<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $FldNames=array('CurrencyCode','StartDate','Multy'
      ,'Rate','FullRate');
$New=addslashes($_REQUEST['New']);
$CurrencyCode=addslashes($_REQUEST['CurrencyCode']);
if ($CurrencyCode==''){ die ("<br> Error:  Empty CurrencyCode");}
$StartDate=addslashes($_REQUEST['StartDate']);
if ($StartDate==''){ die ("<br> Error:  Empty StartDate");}

$dp=array();
  
  $query = "select * ".
         "FROM CurrencyExchRate ".
         " WHERE (CurrencyCode='$CurrencyCode') AND (StartDate='$StartDate')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
  }
  else {
    die ("<br> Error: not found record (CurrencyCode='$CurrencyCode') AND (StartDate='$StartDate')"); 
  }
  $Editable=1;
  if (!Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete ".
         "FROM CurrencyExchRate ".
         " WHERE (CurrencyCode='$CurrencyCode') AND (StartDate='$StartDate')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);  
$LNK='';

  $V=$_REQUEST['CurrencyCode'];
  $LNK.="CurrencyCode=$V";
  
  $V=$_REQUEST['StartDate'];
  $LNK.="&StartDate=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=CurrencyExchRateList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>