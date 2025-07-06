<?php
session_start();

include ("../../setup/common.php");
include ("../../commoni.php");


//print_r ($_COOKIE);

if (isset($_GET['logout'])) {
  if (isset($_SESSION['login'])) {
    $login=$_SESSION['login']; 
    $adm='usr';
    if ( isset($_SESSION['admin_login'])) {
      $adm='adm';
    } ;
    MakeAdminRec ($mysqli, $login, 'LOGOUT', $login, 
                        $adm, 'Logout '.$login);
  };     
    session_unset();
    session_destroy();
echo("
<html><head><title>Vladislav +7 (903) 736 7000</title></head><body>
<br>Logout ok.
<br>For info contact Vladislav +7 (903) 736 7000
</body>
</html>");

    exit(); // после передачи редиректа всегда нужен exit или die
    // иначе выполнение скрипта продолжится.
};

//==========================================================================================


function ShowMenu (&$mysqli, $Level, $StartPoint, $ColumnNo) {
  if ($Level>15) {
    die("<br> Error: Menu level > 15. StartPoint= $StartPoint");
  }
  
  if ($Level==0) {
    echo ("<table width=95% valign=top><tr>");

    for ($i=0; $i<3;$i++) {
      echo ("<td>");
      ShowMenu ($mysqli, $Level, $StartPoint, $i);
      echo ("</td>");
    }
    echo ("</tr></table>");
  
  }
  else {
  
      // SELECT `Id`, `MenuName`, `Description`, `Link`, `NewWindow`, 
      // `ParentId`, `Ord` FROM `Menu` WHERE 
      $query = "SELECT * " .
               "FROM Menu where ParentId='$StartPoint' and ColumnNo=$ColumnNo  order by Ord";
      
      //echo ("<br>$sql_txt<br>");
      
      $sql3 = $mysqli->query ($query)
                     or die("Invalid query:<br>$query<br>" . $mysqli->error);
      echo ("<ul>");
      while ($dp = $sql3->fetch_assoc()) {
        if ( HaveRight1 ($mysqli, "Menu", $dp['Id'])) {
          echo ("<li><a href='{$dp['Link']}'>".GetStr($mysqli, $dp['MenuName']).
                              '</a><p><small>'.GetStr($mysqli, $dp['Description'])).'</small>';
          ShowMenu ($mysqli, $Level+1, $dp['Id'], $ColumnNo);
          echo ("</li>");  
        } 
      }
      echo ("</ul>");
  }
}

function draw_form($bad_login = false) {
  echo ('<form action="" method="post">'.
        '<table>'.
        '<tr><td>Login:</td><td><input type="text" name="login"></input></td></tr>'.
        '<tr><td>Password:</td><td><input type="password" name="pass"></input></td></tr>'.
        '</table>'.
        '<input type="submit" name="submit" value="Log In"></input><br>'.
        '</form>');
  if ($bad_login)
    echo 'Bad Login or password';
}


function check_login(&$mysqli, $login, $pass, $SecTxtMsg) {
  $login = addslashSs ($login);

  $Check=false;

  //---------------------------------------------------------------------
  // admin_protocol
  // transf_id, op_date, code, param1, param2, description, user_id
  $query = "SELECT COUNT(*) CNT FROM admin_protocol ".
             "where (code='BadPass') and (Param1='$login') and ".
                 "(op_date>DATE_SUB(NOW(), INTERVAL 10 MINUTE))";

  $sql5 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  if ($dp5 = $sql5->fetch_assoc()) {
    if ($dp5['CNT']>2) {
      
      if ($login != 'Goncharuk K.') { 
        echo ("<br> Please wait 10 minutes. User <b>[$login]</b> temporary blocked ");
        MakeAdminRec ($mysqli, $login, 'BLOCK', $login, 
                          $dp5['CNT'], 'Blocked user temp: '.$login);
        die();
      }
    };     
  }
  //=====================================================================



  $query = "SELECT usr_id, usr_pwd, email, PwdCoded, passwd_last_change, description, admin, Blocked, WebCookie " .
             "FROM usrs where usr_id='$login'";
  
  //if ($login == 'Goncharuk K.') { 
  //  echo ("<br>$sql_txt<br>");
  //}

  $sql3 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($row = $sql3->fetch_object()) {
      if ($row->Blocked==1) {
        die ("<br>User blocked");
      } 
      
      $Check=0;
      
      //if ($login == 'Goncharuk K.') { 
      //  echo ("<br>Coded: {$row->PwdCoded} ");
      //}

      if ($row->PwdCoded) {
        $Fld='usr_id';
        
        $Usr  = $row->usr_id;
        $Pwd  = $row->usr_pwd;
        $email= $row->email;

        $DateLastCh=$row->passwd_last_change;
        
        //if ($login=='vlad_lev') {
        //if ($login == 'Goncharuk K.') { 
        //  echo ("<br>$Usr $pass $email $DateLastCh $SecTxtMsg $Pwd ");
        //} 
        
        $PassTxt= hash('sha256', "$Usr $pass $email $DateLastCh $SecTxtMsg");
        
        //if ($login == 'Goncharuk K.') { 
        //  echo (" $PassTxt ");
        //}

        $Check= ($PassTxt==$Pwd);
      }
      else {
        die ("<br> Please change password ");
        $Check= ($pass==($row->usr_pwd));
      }
      
      if ($Check) {
        $adm='usr';
        if ($row->admin == 1) {
          $_SESSION['admin_login'] = $login;
          $adm='adm';
        };

        //----------------------------------------------------------------------
        $query = "SELECT * " .
                         "FROM ParamVal where ParamType='0001' and ID='$login'";
          
        $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
        
        if($row1 = $sql2->fetch_object()) {
                $_SESSION[$row1->ParamNo]=$row1->Value;     
        }
        //-----------------------------------------------------------------------
        MakeAdminRec ($mysqli, $login, 'LOGIN', $login, $adm, 'Login '.$login);

      }
      else {
        MakeAdminRec ($mysqli, $login, 'BadPass', $login, 
                          $_SERVER['REMOTE_ADDR'], 'Bad password '.$login);
      }
    return $Check;
  }
  return (false);
}
?>
<html>
<head>
<title>Vladislav +7 (903) 736 7000</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../../style.css">
<meta http-equiv="Content-Language" content="ru">
<link rel="icon" href="../../favicon.ico" type="image/x-icon">
</head>
<body>
<?php

// >>> точка входа <<<
echo("<br>Vladislav +7 (903) 736 7000<br>");

if ($_COOKIE['AL'] != '') {
  
  $i=$_SESSION['login'];

  if (!isset($_SESSION['login'])) {
    echo ('<h3>Phone Login</h3><form action="WebLogin.php" method="post">'.
        '<table>'.
        '<tr><td>Password:</td><td><input type="password" name="pass"></input></td></tr>'.
        '</table>'.
        '<input type="submit" name="submit" value="Log In"></input><br>'.
        '</form><br><hr>');
    
  }  
}

// на случай если мы уже авторизированы
if (!isset($_SESSION['login'])) {

    $login = $_POST['login'];
    $pass  = $_POST['pass'];
    //echo ('<br>VDL post:');
    //print_r($_POST);
    //echo ('<br>');

    if (count($_POST) <= 0)
        draw_form();
    else {
        if (check_login($mysqli, $login, $pass, $SecTxtMsg))
            $_SESSION['login'] = $login;
        else
            draw_form(true);
            // параметр true передается чтобы показать, что был введен
            // неправильный пароль
    }
}


if (!isset($_SESSION['login'])) {
  echo ("<br><br><a href='FogotPasswd.php'>Forgot password / ". ToUtf('Пароль забыт')."</a>");
}

isset($_SESSION['login']) or die(); // здесь если функция вернула false то выполняется die()

echo '<br>Good day, ' . $_SESSION['login'].'! ';

if ($_SESSION['login'] !=  '' ) { 
  ShowMenu ($mysqli, 0,0);
}  


echo ("<hr><br><a href='../user_setup.php'>User Setup</a><br>");
$_SESSION['LPP']=GetUserParam ($mysqli, 'LPP');



if ($_REQUEST['Aftr']!='') {
  $After=base64_decode($_REQUEST['Aftr']);
  echo ("<h1><a href='../../../..$After'>Go to link</a></h1>");
  echo ('<META HTTP-EQUIV="REFRESH" CONTENT="3;URL=../../../..'.$After.'">');

} 


AdminFooter ();

?>

</body>
</html>
															