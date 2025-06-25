<?php
session_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<title>User list</title></head>
<body>
<?php
include ("../setup/config.php");
include ("set_passw.php");

$input1 = array("21", "34", "45", "67", "11", "14", "25");
$input2 = array("1",  "2",  "3",  "4",  "5",  "6" , "7");

$Val1= 0+ $input1[$_REQUEST ['Key1']];
$Val2= 0+ $input2[$_REQUEST ['Key2']];

$VRes= $_REQUEST ['Res'];
$Res = $Val1 + $Val2; 

if ($Res == $VRes) {
  $Mail= addslashes ( $_REQUEST ['mail'] );

  $query = "SELECT usr_id, description, email, Blocked FROM vladlev_legrand.usrs WHERE email='$Mail'";
  
  //echo ("<br>$Mail<br>$query<br>");

  $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());

  $NewPasswd='';
  $UsrId='';
  if ($dp = mysql_fetch_array($sql2)) {
    $UsrId=$dp['usr_id'];
    $Descr=$dp['description'];
    $email=$dp['email'];

    echo ("<br>User Id:$UsrId:<br>");
    if ( $dp['Blocked'] ) {
      die ("User blocked");
    }

    $NewPasswd =addslashes (GetNewPass());
    $query = "update vladlev_legrand.usrs set usr_pwd='$NewPasswd' WHERE usr_id='$UsrId'";
  
    $sql2 = mysql_query ($query)
                 or die("Invalid query:<br>$query<br>" . mysql_error());
    
    include "../adv/send_mail_vdl.php";
    
    $Subj= "New pass to Legrand Labels server";
    
    $Msg1= "<br>Good day! ".
                "<br> Your password to ".
                "<a href='https://project.kontaktor.ru/legrand/Labels/'>Legrand Russia</a> server".
                "<br>Login:[$UsrId]".
               "<br>New password:[$NewPasswd]".
               "<br>Brackets (at the beginning and end of Login and Password) <b>[ NOT ]</b> copy!!!<br>\r\n".  
                "<br><br> ��� ������ � ������� �������� ".
                "<a href='https://project.kontaktor.ru/legrand/Labels/'>Legrand Russia</a>".
                "<br>Login:[$UsrId]".
               "<br>New password:[$NewPasswd]".
               "<br>���������� ������ (� ������ � ����� ������ � ����� ������������)<b>[ �� ]</b> ����������!!!<br>\r\n";  
        
    
    $Arr= array ($email=>1);
    $Div='';
    foreach ($Arr as $M=>$V) {
      $Rcp.="$Div$M";
      $Div=',';
    } 
      
    $Sndr= 'vl@legrand-training.com';
    $SndFile=array ();
        
    if (multi_attach_mail($Rcp, $SndFile, $Sndr, $Subj, $Msg1)>0) {
      echo ("<br>Check Your mail: $email");;
    }
    else {
      echo ("<br> Error during send e-mail");
    }
  }
  else {
    die ("<br>No e-mail");
  }


}
else {
  die ('<br>Not ok');
}

?>
</body>
</html>
