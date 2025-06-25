<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('UserId','DirNo','ShortName','Tip','DirName','Ord');

$PKNames=array('UserId','DirNo');

$New=addslashes($_REQUEST['New']);
$UserId=addslashes($_REQUEST['UserId']);

if ($UserId==''){ die ("<br> Error:  Empty UserId");}

$PdoArr = array();
$PdoArr['UserId']= $UserId;

if (empty ($_REQUEST['Ord'])) {
  $_REQUEST['Ord']=0;
}

try {

$DirNo=addslashes($_REQUEST['DirNo']);
if ($DirNo==''){ 
    if ($New==1) {  
      $query = "select MAX(\"DirNo\") \"MX\" FROM \"FavPlaces\" ".
               "WHERE (\"UserId\"=:UserId)";
      
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);

      $MX=0;
      if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
        $MX=$dp['MX'];
      }
      $MX++;
      $_REQUEST['DirNo']=$MX;
      $DirNo=$MX;
    }
    else { die ("<br> Error:  Empty DirNo");}
}
$PdoArr['DirNo']= $DirNo;


  //---------------------------- Для автонумерации ---------------
  //include ("NumSeq.php");
  //if($_REQUEST['DocNo']=='') {
  //  $D=$_REQUEST['OpDate'];
  //  if ($D=='') {
  //    $_REQUEST['OpDate']=date('Y-m-d');
  //    $D=$_REQUEST['OpDate'];
  //  }
  //  $_REQUEST['DocNo'] = GetNextNo ( $pdo, 'BankOp', $D);
  //}
  $Res=UpdateTable ($pdo, "FavPlaces", $FldNames, $_REQUEST, $PKNames, 1);
  echo ("<br>$Res<br>");

$LNK='';

  $V=$_REQUEST['DirName'];
  $LNK.="Dir1=$V";
  
  $V='/var';
  $LNK.="&Dir2=$V";
  

echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=EdiDispatchedFiles.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
 }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>
</body>
</html>