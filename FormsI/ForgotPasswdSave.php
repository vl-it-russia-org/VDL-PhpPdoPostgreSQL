<?php
session_start();

include ("../setup/common_pg.php");
include "../setup/send_mail_vdl_pg.php";



?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<title>User list</title></head>
<body>
<?php

if (empty($_REQUEST['mail'])) {
  die ("<br> Error: Mail is empty");
}

$input1 = array("21", "34", "45", "67", "11", "14", "25");
$input2 = array("1",  "2",  "3",  "4",  "5",  "6" , "7");

$Val1= 0+ $input1[$_REQUEST ['Key1']];
$Val2= 0+ $input2[$_REQUEST ['Key2']];

$VRes= $_REQUEST ['Res'];
$Res = $Val1 + $Val2; 

if ($Res == $VRes) {
  $Mail= $_REQUEST['mail'];


  $query = "SELECT \"usr_id\", \"description\", \"email\", \"Blocked\" FROM \"usrs\" WHERE \"email\"=:Mail";
  
  //echo ("<br>$Mail<br>$query<br>");
  $PdoArr = array();
  $PdoArr['Mail']= $Mail;
  try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);



  $NewPasswd='';
  $UsrId='';
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $UsrId=$dp['usr_id'];
    $Descr=$dp['description'];
    $email=$dp['email'];

    echo ("<br>User Id:$UsrId:<br>");
    if ( $dp['Blocked'] ) {
      die ("User blocked");
    }

    $NewPasswd =addslashes (GetNewPass());
    $LastCH=date ('Y-m-d H:i:s');
    echo ( " $LastCH "); 

    //--------------------------------------------------------------
    $PassTxt= hash('sha256', "$UsrId $NewPasswd $email $LastCH $SecTxtMsg");
    
    //echo ( "<br> -- NewCodedPass=$PassTxt ".mb_strlen ($PassTxt));
     
    //--------------------------------------------------------------
    
    $query = "update \"usrs\" set \"usr_pwd\"=:PassTxt, \"PwdCoded\"=:PCod, \"passwd_last_change\"=:LastCH ".
             "WHERE usr_id=:UsrId";
  
    $PdoArr = array();
    $PdoArr['PassTxt']= $PassTxt;
    $PdoArr['LastCH']= $LastCH;
    $PdoArr['UsrId']= $UsrId;
    $PdoArr['PCod']= 1;

    //echo ("<br> Line ".__LINE__.": $query<br>");
    //          print_r($PdoArr);
    //          echo ("<br>");


    $STHUpd = $pdo->prepare($query);
    $STHUpd->execute($PdoArr);
    
    $ErrArr = $STHUpd->errorInfo();
    //echo ("<br> Line ".__LINE__.":ErrArr: ");
    //print_r($ErrArr);
    //echo ("<br>");

    MakeAdminRec ($pdo, $_SESSION['login'], 'USR', $UsrId, 
                        'ChPass', 'Password changed '.$UsrId);
    
    $CompanyName='Home'; //GetConst($pdo, 'CompanyName', date('Y-m-d'));

    
    $Subj= "New pass to $CompanyName server";
    
    $Msg1= "<HTML><BODY><br>Good day! ".
                "<br> Your password to ".
                "<a href='{$BaseHost}'>$CompanyName</a> server".
                "<br>Login:[$UsrId]".
               "<br>New password:[$NewPasswd]".
               "<br>Brackets (at the beginning and end of Login and Password) <b>[ NOT ]</b> copy!!!<br>\r\n".  
                "<br><br> Ваш пароль в систему компании ".
                "<a href='{$BaseHost}'>$CompanyName</a>".
                "<br>Login:[$UsrId]".
               "<br>New password:[$NewPasswd]".
               "<br>Квадратные скобки (в начале и конце пароля и имени пользователя)<b>[ не ]</b> копировать!!!<br>\r\n".
               "</BODY><HTML>";  
        
    
    $Arr= array ($email=>1);
    $Div='';
    foreach ($Arr as $M=>$V) {
      $Rcp.="$Div$M";
      $Div=',';
    } 
      
    $Sndr= 'vl@legrand-training.com';
    $SndFile=array ();
    $Copy='';
    if ($_REQUEST ['CopyToVlad']==1) {
      $Copy='vl@it-eu.org' ;
    }    
    if (multi_attach_mail($Rcp, $SndFile, $Sndr, $Subj, $Msg1, $Copy)>0) {
      echo ("<br>Check Your mail: $email");
      //echo ("<br>$Msg1<br>");
    }
    else {
      echo ("<br> Error during send e-mail");
    }
  }
  else {
    die ("<br>No e-mail");
  }

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }



}
else {
  die ('<br>Not ok');
}

?>
</body>
</html>
