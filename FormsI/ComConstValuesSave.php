<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('ConstName','OpDate','ValidTill','Value'
      ,'ValueDate','ValueTxt');
$New=$_REQUEST['New'];
$ConstName=$_REQUEST['ConstName'];
if ($ConstName==''){ die ("<br> Error:  Empty ConstName");}

$OpDate=addslashes($_REQUEST['OpDate']);
if ($OpDate==''){
    $OpDate=date('Y-m-d');
}

if (empty($_REQUEST['Value'])) {
  $_REQUEST['Value']='0';
}

if (empty($_REQUEST['ValueDate'])) {
  $_REQUEST['ValueDate']='2000-01-01';
}

if (empty($_REQUEST['ValidTill'])) {
  $_REQUEST['ValidTill']='2100-01-01';
}


$PdoArr = array();
try {
  $PdoArr['ConstName']= $ConstName;
  $PdoArr['OpDate']= $OpDate;

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


  $dp=array();
  
  $query = "select * FROM \"ComConstValues\" ".
           "WHERE (\"ConstName\"=:ConstName) AND (\"OpDate\"=:OpDate)";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    if ($New==1){
      echo ("<br>");
      print_r($dp);
      die ("<br> Error: Already have record ");
    }

    $Editable=1;
    if (!$Editable) {
      die ("<br> Error: Not Editable record ");
    }      
  }
  
  if ($New==1){
    $q='insert into "ComConstValues"(';
    $S1='';
    $S2='';
    $Div='';
    $PdoArr = array();
    foreach ($FldNames as $F) {
      $S1.=$Div.'"'.$F.'"';
      $S2.="$Div:$F";
      $PdoArr[$F]= $_REQUEST[$F];
      $Div=', ';
    }

    $query=$q.$S1.') values ('.$S2.')';
    
    //echo ("<br>$q<br>");
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);


}
  else {
    $q='update "ComConstValues" set ';
    $S1='';
    $Div='';
    $PdoArr = array();
    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      if ( $V1 != $dp[$F]) {
        $S1.=$Div.'"'.$F."\"=:$F";
        $Div=', ';
        $PdoArr[$F]= $_REQUEST[$F];
      }
    }
    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

      $PdoArr['ConstName']=$_REQUEST['OldConstName'];
      $S1.="(\"ConstName\"=:ConstName)";
  
      $PdoArr['OpDate']=$_REQUEST['OldOpDate'];
      $S1.=" and (\"OpDate\"=:OpDate)";

  
      $query=$q.$S1;

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);  
    }
  }
$LNK='';

  $V=$_REQUEST['ConstName'];
  $LNK.="ConstName=$V";
  
  $V=$_REQUEST['OpDate'];
  $LNK.="&OpDate=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=ComConstCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

  echo('<H2>Saved</H2>');
?>
</body>
</html>