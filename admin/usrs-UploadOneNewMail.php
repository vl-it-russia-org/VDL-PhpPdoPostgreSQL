<?php  
session_start();
include ("../setup/common.php");
include ("../setup/send_mail_vdl.php");

BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Upload xlsx to usrs</title></head>
<body>
<?php
echo '<br>User: ' . $_SESSION['login'].'<br>';

//print_r($_FILES);
//echo ("<br>");

//print_r($_REQUEST);
//echo ("<br>");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include "common_func.php";


CheckRight1 ($mysqli, 'ExtProj.Admin');

$FileName='usrs';


//=============================================================================================

// Запоминаем параметры (в какой колонке хранятся)
echo("<hr><h4>Update ".GetStr($mysqli, 'usrs')." New Mail</h4>");


$UsrId= addslashes($_REQUEST['UsrId']);
if (empty ($UsrId)) {
  die ("<br> Error: UserId is empty");
}

$OldMail= addslashes($_REQUEST['OldMail']);
if (empty ($OldMail)) {
  die ("<br> Error: OldMail is empty");
}


$NewMail= addslashes($_REQUEST['NewMail']);
if (empty ($NewMail)) {
  die ("<br> Error: NewMail is empty");
}



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
  echo ("<br> Have User: {$dp2['usr_id']} {$dp2['description']} ");
  
  if ($dp2['email'] != $OldMail ) {
    if ($dp2['email']== $NewMail) {
          // Already done
      echo (" already have $NewMail ");         
    }
    else {
      echo ("<br> Error: user $UsrId : Old Email is not match Have: {$dp2['email']} in file {$Arr['B']}");  
      $Warr++;
    }
      
  }
  else {

    // usrs
    // usr_id, usr_pwd, description, admin, 
    // email, phone, passwd_duedate, new_passwd, passwd_last_change, 
    // SFUser, Blocked, WebCookie, Position, Department, 
    // Company, FirstName, LastName, PatronymicName__c, PwdCoded
    $query = "select * from usrs ". 
             "where (usr_id != '$UsrId') and (email = '$NewMail')"; 

    $sql22 = $mysqli->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

    
    if ($dp22 = $sql22->fetch_assoc()) {
      $Warr++;

      
      echo (" <br> Warring: user {$dp22['usr_id']} also have email $NewMail as $UsrId");
    }
    
    MakeAdminRec ($mysqli, $_SESSION['login'], 'USR', $UsrId, 
                      'ChMail', 'Mail changed '.$OldMail.'->'.$NewMail);
  
    // usrs
    // usr_id, usr_pwd, description, admin, 
    // email, phone, passwd_duedate, new_passwd, passwd_last_change, 
    // SFUser, Blocked, WebCookie, Position, Department, 
    // Company, FirstName, LastName, PatronymicName__c, PwdCoded
    $query = "update usrs set email='$NewMail' ". 
             "where (usr_id = '$UsrId')"; 

    $sql27 = $mysqli->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  
    echo ("<br> $UsrId: $OldMail -> $NewMail changed ");
    $UpdCnt++;
  }
    
  // Move rights

  // RespPersons
  // ObjId, Param1, enContactType, ContactId, 
  // Description, Rank
  $query = "update RespPersons set ContactId='$NewMail' ". 
           "where (ContactId='$OldMail' ) "; 

  $sql29 = $mysqli->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

  $Cnt= $mysqli->affected_rows;
  echo ("<br> Update rights: $Cnt lines "); 

  // Email recepients
  // email_recepients
  // company, email_addr
  $query = "update email_recepients set email_addr='$NewMail' ". 
           "where (email_addr='$OldMail' ) "; 

  $sql29 = $mysqli->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

  $Cnt= $mysqli->affected_rows;
  echo ("<br> Update Email $OldMail ==> $NewMail recepients : $Cnt lines "); 

}

  $SndFile=array();
  $Sndr= 'vl@legrand-training.com';

  $Txt=To1251('<html>
  
Заменил вам почту в системе PROJECT.
<br>Вам нужно сбросить пароль.
<br>
<br>1) Перейдите по ссылке:  <a href="'.$BaseHost.'adv/">Реклама</a>
<br>2) Слева внизу ссылка: 
<br>* сначала нужно сделать “LogOut”
<br>* затем "Login"
<br>
<br>3) Под полем для ввода пароля, ссылка  "Пароль забыт"
<br>4) Внесите Вашу почту, решаете несложный пример,
<br>
<br>Новый пароль и ИМЯ_ПОЛЬЗОВАТЕЛЯ Вам придет на почту (или упадет в спам)
<br>Если нужно – наберите меня в Толк для бизнеса или на сотовый +7 915 034 7292
<br>
<br>Обратите внимание, что сервер Прожект теперь не открывается с project.legrand.ru,
<br>Открывается только с project.kontaktor.ru
<br> Т.е. в начале ссылки https://project.legrand.ru/
<br> нужно заменить на https://project.daccord.ru/
</html>  ');

  
  $Subj=MimeHeader1251(To1251('Изменение доступа к сайту PROJECT'));

  //$Sndr .= 'Cc: '.$StdMail['EtmTotalMailCC'] . "\r\n";
  //$Sndr .= 'Bcc: vladislav.levitskiy@kontaktor.ru';
  
  if (multi_attach_mail('vladislav.levitskiy@daccord.ru,'.$NewMail, 
                        $SndFile, $Sndr, $Subj, $Txt)>0) {
    echo (' Ok ');
  }
  else {
    echo (' -- error sending e-mail!!! ');
  }



?>
</body></html>