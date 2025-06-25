<?php
session_start();

include ("../setup/common.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

$FldNames=array('Id','UserName','Region','FirstName'
      ,'LastName','Email','IsActive','UserRoleId','ManagerId'
      ,'LastLoginDate','LegrandRegion__c','EnglishName__c','Firm__c','SalesTeam__c'
      ,'PatronymicName__c','UnId','Title','CompanyName','Department'
      ,'Phone');
$New=addslashes($_REQUEST['New']);
$UnId=addslashes($_REQUEST['UnId']);
if ($UnId==''){ 
    if ($New==1) {  
      //$query = "select MAX(UnId) MX FROM SF_User ".
      //         "WHERE ";
      //$sql2 = $mysqli->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $mysqli->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['UnId']=$MX;
      //$UnId=$MX;
    }
    else { die ("<br> Error:  Empty UnId");}
}


  //---------------------------- Для автонумерации ---------------
  //include ("NumSeq.php");
  //if($_REQUEST['DocNo']=='') {
  //  $D=$_REQUEST['OpDate'];
  //  if ($D=='') {
  //    $_REQUEST['OpDate']=date('Y-m-d');
  //    $D=$_REQUEST['OpDate'];
  //  }
  //  $_REQUEST['DocNo'] = GetNextNo ( $mysqli, 'BankOp', $D);
  //}


  $dp=array();
  $query = "select * FROM SF_User ".
           "WHERE (UnId='$UnId')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
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
    $q='insert into SF_User(';
    $S1='';
    $S2='';
    $Div='';

    foreach ($FldNames as $F) {
      $V=addslashes ($_REQUEST[$F]);
      $S1.=$Div.$F;
      $S2.="$Div'$V'";
      $Div=', ';
    }
    $q.=$S1.') values ('.$S2.')';
    
    $sql2 = $mysqli->query ($q)
                 or die("Invalid query:<br>$q<br>" . $mysqli->error);
}
  else {
    $q='update SF_User set ';
    $S1='';
    $Div='';

    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      $V=addslashes ($_REQUEST[$F]);
      if ( $V1 != $dp[$F]) {
        $S1.=$Div.$F."='$V'";
        $Div=', ';
      }
    }
    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

$V=addslashes ($_REQUEST['OldUnId']);
      $S1.="(UnId='$V')";
  
      $q.= $S1;
      $sql2 = $mysqli->query ($q)
                 or die("Invalid query:<br>$q<br>" . $mysqli->error);
  
    }
  }
$LNK='';

  $V=$_REQUEST['UnId'];
  $LNK.="UnId=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=SF_UserCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>