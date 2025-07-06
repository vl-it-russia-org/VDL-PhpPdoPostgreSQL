<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Fin');

 $FldNames=array('ExpId','ExpenseName','HaveRegions'
      ,'FinDiv');
$New=$_REQUEST['New'];
$PdoArr = array();
$ExpId=$_REQUEST['ExpId'];
if ($ExpId==''){ die ("<br> Error:  Empty ExpId");}
$PdoArr["ExpId"] = $ExpId;

$dp=array();
  
  $query = "select * FROM \"Expenses\" ".
           "WHERE (\"ExpId\"= :ExpId)";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record (\"ExpId\"= :ExpId)"); 
  }
  $Editable=1;
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"Expenses\" ".
           "WHERE (\"ExpId\"= :ExpId)";
  
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

  $V=$_REQUEST['ExpId'];
  $LNK.="ExpId=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=ExpensesList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>