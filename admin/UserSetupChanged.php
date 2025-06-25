<?php
session_start();
include ("../setup/common.php");
BeginProc();

//ini_set('display_errors', TRUE);

$UserId = $_POST['UserId'];

if ($UserId == '') {
  die ('Update error');
};

if ($UserId != $_SESSION['login']) {
  CheckRight1 ($mysqli, 'ExtProj.Admin');
};

echo ('<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<META HTTP-EQUIV="REFRESH" CONTENT="2;URL=user_setup.php?UserId='.$UserId.'">
<title>Mnf Label Print</title></head>
<body>');

//print_r($_POST);

echo '<H3>User: ' . $_SESSION['login'].'</h3>';

$query = "select email, phone FROM usrs ".
         "WHERE usr_id='$UserId' ";

$sql2=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);

$UserMail  = ($_POST['UserMail']);
$UserPhone = ($_POST['UserPhone']);
$WC = addslashes ($_POST['WebCookie']);

if ($dp = $sql2->fetch_object()) {
  $Upd="WebCookie='$WC'";
  print_r( $dp);
  echo("<br>$UserPhone =".$dp->phone.']<br>'); 
  echo("<br>$UserMail =".$dp->email.']<br>'); 
  if ($dp->email != $UserMail) {
    $Upd.=", email='".addslashes ($UserMail)."' ";
  };

  if ($dp->phone != $UserPhone) {
    if ($Upd!= '') {
     $Upd=$Upd.', '; 
    };
    $Upd=$Upd." phone='".addslashes ($UserPhone)."' ";
  };    
     
  echo('<br>'. $Upd);

  if ($Upd!='') {
    MakeAdminRec ($mysqli,  $_SESSION['login'], 'USR', $UserId, 
            'UPDUSR', 'Updated user info '.$UserMail.','.
              $UserPhone);

    $query = "Update usrs set ". $Upd. 
               " WHERE usr_id = '$UserId'";

    $sql=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);
    echo ('<br>Changed');    
  }
}
    
?>
</body>
</html>
				       