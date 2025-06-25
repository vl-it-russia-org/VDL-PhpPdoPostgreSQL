<?php
session_start();

include ("../setup/common_pg.php");
BeginProc(1);

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('MenuName','MenuType','Description','Link'
      ,'NewWindow','ColumnNo', 'ParentId','Ord', 'MenuCode');
$New=$_REQUEST['New'];
$Id=$_REQUEST['Id'];

$IntArr = array ('NewWindow', 'ParentId');

foreach ( $IntArr as $F ) {
  if ($_REQUEST[$F]=='') {
    $_REQUEST[$F]='0';
  }
}



if ($Id==''){ 
    if ($New==1) {  
      //$query = "select MAX(Id) MX FROM Menu ".
      //         "WHERE ";
      //$sql2 = $pdo->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $pdo->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['Id']=$MX;
      //$Id=$MX;
    }
    else { die ("<br> Error:  Empty Id");}
}

$Editable=1;


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
  try {

  if (!empty($Id)) { 



  $query = "select * FROM \"Menu\" ".
           "WHERE (\"Id\"=:Id)";
  
  $PdoArr = array();
  $PdoArr['Id']= $Id;


  
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
  }
  
  if ($New==1){
    $PdoArr = array();
    
    $q='insert into "Menu"(';
    $S1='';
    $S2='';
    $Div='';

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
    $q='update "Menu" set ';
    $S1='';
    $Div='';
    $PdoArr = array();

    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      if ( $V1 != $dp[$F]) {
        $S1.=$Div.'"'.$F."\"=:$F";
        $PdoArr[$F]= $_REQUEST[$F];
        $Div=', ';
      }
    }
    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

      $S1.="(\"Id\"=:Id)";
      $PdoArr['Id']= $_REQUEST['OldId'];
  
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
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=MenuCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>