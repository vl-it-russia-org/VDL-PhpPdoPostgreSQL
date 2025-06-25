<?php
session_start();


include ("../setup/common.php");

//error_reporting(E_ERROR  | E_PARSE);
//ini_set('display_errors', 1);

CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

//print_r($_REQUEST);


$FldNames=array('usr_id','usr_pwd','description','admin'
      ,'email','phone','passwd_duedate','new_passwd'
      ,'SFUser','Blocked','WebCookie','Position','Department'
      ,'Company','FirstName','LastName','PatronymicName__c');


if(empty($_REQUEST['Chk'])) {
  die ("<br> Error: Chk is empty");
}

if (!is_array($_REQUEST['Chk']) ) {
  die ("<br> Error: Chk should be array");
}


$BlockCnt=0;

foreach ( $_REQUEST['Chk'] as $Indx=> $VC) {
  $UserArr = json_decode( base64_decode($VC), 1);
  echo ("<br> $Indx: $VC: ");
  print_r($UserArr);

  $UsrId = addslashes($UserArr['usr_id']);

  // usrs
  // usr_id, usr_pwd, description, admin, 
  // email, phone, passwd_duedate, new_passwd, passwd_last_change, 
  // SFUser, Blocked, WebCookie, Position, Department, 
  // Company, FirstName, LastName, PatronymicName__c, PwdCoded
  $query = "select * from usrs ". 
           "where (usr_id = '$UsrId')"; 

  $sql2 = $mysqli->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  if ($dp2 = $sql2->fetch_assoc()) {
    if ($dp2['Blocked']==1 ) {
      echo ("<br> User '$UsrId' already blocked ");
    }
    else {
      $BlockCnt++;
      echo ("<br> $UsrId block ");
      $query = "update usrs set Blocked=1 ". 
               "where (usr_id = '$UsrId')"; 

      $sql5 = $mysqli->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    
      
      MakeAdminRec ($mysqli, $_SESSION['login'], 'USR', $UsrId, 
                        'Blocked', 'User blocked '.$UsrId);
    
    
    }
  }
  else {
    die ("<br> Error: User '$UsrId' is not found"); 
  }
}


$Lnk='';
$Div='';
foreach($_REQUEST as $Name => $VC) {
  $BegL=substr($Name, 0, 5);
  if ( ($Name=='BegPos') OR ($BegL=='Fltr_')) {
    $V= addslashes($VC);  
    $Lnk.="$Div$Name=$V";
    $Div='&';
  }
}



  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="3;URL=usrsList.php?'.$Lnk.'">'.
'<title>Save</title></head>
<body>');
  
  echo("<H2>$BlockCnt BLOCKED</H2>");
?>
</body>
</html>