<?php
function multi_attach_mail($to, $files, $sendermail, $Subj, $Msg, $CCList='', $BCCList=''){
    // email fields: to, from, subject, and so on

    if ($sendermail == '') {
      $sendermail = 'vl@legrand-training.com';
    }
    $from = $sendermail;
     
    $subject = $Subj; 
    $message = $Msg;
    $headers = "From: $from";
    
    if ($CCList!= '') {
      $headers .= "\nCc: $CCList";
    }

    if ($BCCList!= '') {
      $headers .= "\nBcc: $BCCList";
    }
 
    // boundary 
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
 
    // headers for attachment 
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
 
    // multipart boundary 
    $message = "--{$mime_boundary}\n" . 
               "Content-Type: text/html; charset=\"Windows-1251\"\n" .
    //"Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
    //"Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
 
    // preparing attachments
    for($i=0;$i<count($files);$i++){
        if(is_file($files[$i])){
            $message .= "--{$mime_boundary}\n";
            $fp =    @fopen($files[$i],"rb");
            $data =  @fread($fp,filesize($files[$i]));
                    @fclose($fp);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\"\n" . 
            "Content-Description: ".basename($files[$i])."\n" .
            "Content-Disposition: attachment;\n" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            }
        }
    $message .= "--{$mime_boundary}--";
    
    //if (!empty ($_SESSION['MY_MAIL'])) {
    //  $sendermail=$_SESSION['MY_MAIL'];
    //}
    $returnpath = "-f" . $sendermail;
    
    $ok=0;
    if ( $_SESSION['MAIL_TEST']==1 ) {
      $ok=1;
      echo("<br><hr>Test mail send".
           "<br>To:$to, ".
           "<br>Subj:$subject,".
           "<br>Head:$headers,". 
           "<br>Ret:$returnpath ".
           "<br>Txt:$message<br><hr>"); 
    }
    else {
      $ok = @mail($to, $subject, $message, $headers, $returnpath); 
    }

    //echo ("<br>mail($to, $subject, $message, $headers, $returnpath)".
    //      "<br>to=$to, subject=$subject <br>message=$message <br>headers=$headers".
    //      "<br>returnpath=$returnpath<br>Result:$ok"); 
    
    if($ok){ return 1; } else { return 0; }
}
//==========================================================================================
function MimeHeader1251($str) {
  $send_charset='windows-1251';
  return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
}
//==========================================================================================
function MimeHeaderUtf8($str) {
  $send_charset='utf-8';
  return '=?' . $send_charset . '?Q?'. base64_encode($str) . '?=';
}
//==========================================================================================
function GetSelfMail(&$pdo) {
  // Электронная почты текущего пользователя 
   
  $Res='';
  if (!empty($_SESSION['MY_MAIL'])) {
    $Res= $_SESSION['MY_MAIL'];
  }

  if ($Res == '') { 
    $query = "SELECT \"email\" FROM \"usrs\" WHERE ".
             "(\"usr_id\"=:login)";
               
    $PdoArr = array();
    $PdoArr['login']= $_SESSION['login'];
    
    try {
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
    
    if ($dp1 = $STH->fetch(PDO::FETCH_ASSOC)) {
      $_SESSION['MY_MAIL']=$dp1['email'];
      $Res = $_SESSION['MY_MAIL'];
    };

    if (  $Res== '' ) {

      $query = "SELECT \"UserMail\" \"email\" FROM \"PL_Users\" WHERE ".
               "(\"UserId\"=:login)";
                 
      $PdoArr['login']= $_SESSION['PL-login'];
        
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);


      if ($dp1 = $sql->fetch_assoc()) {
        $_SESSION['MY_MAIL']=$dp1['email'];
        $Res = $_SESSION['MY_MAIL'];
      };
    
    }


    if ($Res=='') {
      die ('<br>Error: Bad e-mail for current user '.__FILE__.' line:'.__LINE__); 
    }
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }
  
  
  
  }  
  return $Res;                         
};


//==========================================================================================
function GetMLR(&$pdo, &$ResArr, $Right, $SubRight='-') {
  // список электронных почт пользователей (у кого есть конкретное право) 
  // UsrRights
  // UsrName, RightType, RightSubType, Val
  
  // usrs
  // usr_id, usr_pwd, description, admin, email, phone, passwd_duedate, 
  // new_passwd, passwd_last_change, SFUser, Blocked, WebCookie, Position, 
  // Department, Company, FirstName, LastName, PatronymicName__c, PwdCoded    
  $query = "select email from UsrRights R, usrs U ". 
           "where (RightType = '$Right')and (RightSubType='$SubRight') and (Val=1)".
           "and (R.UsrName=U.usr_id)and(Blocked=0)"; 

  $sql2 = $pdo->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
  while ($dp2 = $sql2->fetch_assoc()) {
    $ResArr[$dp2['email']]=1;
  }  
};
//==========================================================================================
function IsBadEMail(&$pdo, $MailAddr) {
  $Res=0;
  // EMailSendsRefuse
  // EMail, OpDate, IpAddr, ConfirmCode, 
  // ESId, SendDate, ConfirmDate, RefuseReason
  $query = "select * from EMailSendsRefuse ". 
           "where (EMail = '$MailAddr') "; 

  $sql2 = $pdo->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
  if ($dp2 = $sql2->fetch_assoc()) {
    $Res=1;
  }
  return $Res;
}
//==========================================================================================

function GetUsrMail (&$pdo, $UserName ) {
  $Res='';
  // usrs
  // usr_id, usr_pwd, description, admin, 
  // email, phone, passwd_duedate, new_passwd, passwd_last_change, 
  // SFUser, Blocked, WebCookie, Position, Department, 
  // Company, FirstName, LastName, PatronymicName__c, PwdCoded
  $query = "select * from usrs ". 
           "where (usr_id = '$UserName') and (Blocked=0)"; 

  $sql2 = $pdo->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
  if ($dp2 = $sql2->fetch_assoc()) {
    $Res= $dp2['email'];
  }

  return $Res;
}
//===========================================================================================


?>