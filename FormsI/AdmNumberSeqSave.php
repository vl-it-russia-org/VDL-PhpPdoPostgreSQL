<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','IsYearly','Pattern','LastNumber');
$New=$_REQUEST['New'];
$Id=$_REQUEST['Id'];
if ($Id==''){ 
  die ("<br> Error:  Empty Id");
}


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
$PdoArr = array();

if (empty($_REQUEST['LastNumber'])){
  $_REQUEST['LastNumber']=0;
}


try {
  $PdoArr['Id']= $Id;

  $query = "select * FROM \"AdmNumberSeq\" ".
           "WHERE (\"Id\"=:Id)";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    if ($New==1){
      echo ("<br>");
      print_r($dp);
      die ("<br> Error: Already have record ");
    }

    $Editable=1;
    if (!Editable) {
      die ("<br> Error: Not Editable record ");
    }      
  }
  
  if ($New==1){
    $q='insert into "AdmNumberSeq"(';
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
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  }
  else {
    $q='update "AdmNumberSeq" set ';
    $S1='';
    $Div='';

    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      if ( $V1 != $dp[$F]) {
        $PdoArr[$F]= $V1;
        $S1.=$Div.'"'.$F."\"=:$F";
        $Div=', ';
      }
    }
    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

      $PdoArr['Id']=$_REQUEST['OldId'];
      $S1.="(\"Id\"=:Id)";
  
      $query=$q.$S1;

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
    }
  }

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

$LNK='';

  $V=$_REQUEST['Id'];
  $LNK.="Id=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=AdmNumberSeqCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>