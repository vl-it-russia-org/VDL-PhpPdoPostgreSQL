<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

 $FldNames=array('Code2','Code3','DigCode'
      ,'CountryName');
$New=$_REQUEST['New'];
$PdoArr = array();
$Code2=$_REQUEST['Code2'];
if ($Code2==''){ die ("<br> Error:  Empty Code2");}
$PdoArr["Code2"] = $Code2;

$dp=array();
  
  $query = "select * FROM \"Countries\" ".
           "WHERE (\"Code2\"= :Code2)";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record (\"Code2\"= :Code2)"); 
  }
  $Editable=1;
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"Countries\" ".
           "WHERE (\"Code2\"= :Code2)";
  
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

  $V=$_REQUEST['Code2'];
  $LNK.="Code2=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=CountriesList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>