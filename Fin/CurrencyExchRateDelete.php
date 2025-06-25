<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

 $FldNames=array('CurrencyCode','StartDate','Multy'
      ,'Rate','FullRate');
$New=$_REQUEST['New'];
$PdoArr = array();
$CurrencyCode=$_REQUEST['CurrencyCode'];
if ($CurrencyCode==''){ die ("<br> Error:  Empty CurrencyCode");}
$PdoArr["CurrencyCode"] = $CurrencyCode;
$StartDate=$_REQUEST['StartDate'];
if ($StartDate==''){ die ("<br> Error:  Empty StartDate");}
$PdoArr["StartDate"] = $StartDate;

$dp=array();
  
  $query = "select * FROM \"CurrencyExchRate\" ".
           "WHERE (\"CurrencyCode\"= :CurrencyCode) AND (\"StartDate\"= :StartDate)";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record (\"CurrencyCode\"= :CurrencyCode) AND (\"StartDate\"= :StartDate)"); 
  }
  $Editable=1;
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"CurrencyExchRate\" ".
           "WHERE (\"CurrencyCode\"= :CurrencyCode) AND (\"StartDate\"= :StartDate)";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

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