<?php
session_start();


include ("../setup/common_pg.php");

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('usr_id','usr_pwd','description','admin'
      ,'email','phone','passwd_duedate','new_passwd'
      ,'Blocked','WebCookie','Position','Department'
      ,'Company','FirstName','LastName','PatronymicName__c');

$BoolArr = array ('WebCookie'=>1, 'admin'=>1, 'Blocked'=>1);

$New=$_REQUEST['New'];
$usr_id=$_REQUEST['usr_id'];

if ($usr_id==''){ 
  die ("<br> Error:  Empty usr_id");
}

try {


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
  $query = "select * FROM \"usrs\" ".
           "WHERE (\"usr_id\"=:usr_id)";
  $PdoArr = array();
  $PdoArr['usr_id']= $usr_id;

  
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
  
  echo ("<br> New = $New <br>");

  if ($New==1){
    
    
    $q='insert into "usrs"(';
    $S1='';
    $S2='';
    $Div='';

    foreach ($FldNames as $F) {
      $S1.=$Div.'"'.$F.'"';
      $S2.="$Div:$F";
      $Div=', ';
      $PdoArr[$F]=$_REQUEST[$F];
      
      if ($BoolArr[$F]==1) {
        if (empty($_REQUEST[$F])) {
          $PdoArr[$F]=0;
        }
      }
    }
    
    $PdoArr['passwd_duedate']="2000-01-01";
    $PdoArr['passwd_last_change']="2000-01-01";

    $PdoArr['usr_pwd']="";
    $PdoArr['new_passwd']="";
    $PdoArr['PwdCoded']=0;
    //$PdoArr['usr_pwd']="";
    $F='PwdCoded';
    $PdoArr['usr_pwd']="";

    $S1.=$Div.'"'.$F.'"';
    $S2.="$Div:$F";
    
    
    
    $F='passwd_last_change';

    $S1.=$Div.'"'.$F.'"';
    $S2.="$Div:$F";




    $q.=$S1.') values ('.$S2.')';

    echo ("<br> $q<br> Pdo: ");
    print_r($PdoArr);
    echo ("<hr>");


    $STHIns = $pdo->prepare($q);
    $STHIns->execute($PdoArr);
    
    $ErrArr = $STHIns->errorInfo();
    echo ("<br> Line ".__LINE__.":ErrArr: ");
    print_r($ErrArr);
    echo ("<br>");
    
  }
  else {
    $FldNames=array('usr_id', 'description','admin'
      ,'email','phone' ,'Blocked','WebCookie','Position','Department'
      ,'Company','FirstName','LastName','PatronymicName__c');


    $q='update "usrs" set ';
    $S1='';
    $Div='';

    foreach ($FldNames as $F) {
      if ( $_REQUEST[$F] != $dp[$F]) {
        $S1.="$Div\"$F\"=:$F";
        $Div=', ';
        $PdoArr[$F]=$_REQUEST[$F];
        if ($BoolArr[$F]==1) {
          if (empty($_REQUEST[$F])) {
            $PdoArr[$F]=0;
          }
        }
      }
    }
    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

      $S1.="(\"usr_id\"=:usr_id)";
      $PdoArr['usr_id']= $_REQUEST['Oldusr_id'];
      $q.= $S1;

      $STH = $pdo->prepare($q);
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

  $V=$_REQUEST['usr_id'];
  $LNK.="usr_id=$V";


  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=usrsCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>