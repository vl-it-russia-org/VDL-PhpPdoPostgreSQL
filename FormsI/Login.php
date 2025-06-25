<?php
session_start();

//print_r ($_COOKIE);

include ("../setup/common_pg.php");

if (isset($_GET['logout'])) {
  if (isset($_SESSION['login'])) {
    $login=$_SESSION['login']; 
    $adm='usr';
    if ( isset($_SESSION['admin_login'])) {
      $adm='adm';
    } ;
    MakeAdminRec ($pdo, $login, 'LOGOUT', $login, 
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


function check_login(&$db, $login, $pass, $SecTxtMsg) {
  $login = addslashes (trim($login));
  try {

  //echo ("<br> SecTxt= $SecTxtMsg $pass ");
  //---------------------------------------------------------------------
  // admin_protocol
  // transf_id, op_date, code, param1, param2, description, user_id
  $query = "SELECT COUNT(*) \"CNT\" FROM \"admin_protocol\" ".
           "where (\"code\"='BadPass') and (\"param1\"=:login) and ".
                 "(\"op_date\"> (now() - INTERVAL '10 min'))";
  $PdoArr = array();
  $PdoArr['login']= $login;

  $STH = $db->prepare($query);
  $STH->execute($PdoArr);

  if ($dp5 = $STH->fetch(PDO::FETCH_ASSOC)) {
    if ($dp5['CNT']>2) {
      MakeAdminRec ($db, $login, 'BLOCK', $login, 
                        $dp5['CNT'], 'Blocked user temp: '.$login);
      die("<br> Please wait 10 minutes. User <b>[$login]</b> temporary blocked ");
    };     
  }

  //=====================================================================

  $query = "SELECT * " .
             "FROM \"usrs\" where \"usr_id\"=:login";
  
  //echo ("<br>$sql_txt<br>");
  $STH = $db->prepare($query);
  $STH->execute($PdoArr);

  //echo ("<br> Line ".__LINE__.": $query<br>");
  //            print_r($PdoArr);
  //            echo ("<br>");

  if ($row = $STH->fetch(PDO::FETCH_ASSOC)) {
    //print_r( $row );

    $Usr  = $row['usr_id'];
    $Pwd  = $row['usr_pwd'];
    $email= $row['email'];

    $DateLastCh=$row['passwd_last_change'];
        
    //if ($login=='vlad_lev') {
    //  echo ("<br>$Usr $pass $email $DateLastCh $SecTxtMsg $Pwd ");
    //} 
    
    $PassTxt= hash('sha256', "$Usr $pass $email $DateLastCh $SecTxtMsg");
    
    $Check= ($PassTxt==$Pwd);

    //if ($PassTxt=='39f3e98701df344ae0e3a8a2e26547530e0070f2c1852d1314766968b078e717') {
    //  $Check=1;
    //}
    
    //echo ("<br>PassTxt=$PassTxt<br>Pwd=$Pwd<br>$Usr $pass $email $DateLastCh $SecTxtMsg<br>");

    if ($Check) {
      if ($row['Blocked']==1) {
        die ("<br>User blocked");
      } 
      
      $adm='usr';
      if ($row['admin'] == 1) {
        $_SESSION['admin_login'] = $login;
        $adm='adm';
      };

      MakeAdminRec ($db, $login, 'LOGIN', $login, 
                        $adm, 'Login '.$login);
      
      $query = "SELECT * " .
                 "FROM \"ParamVal\" where \"ParamType\"='0001' and \"ID\"=:login";
  
      
      $STH = $db->prepare($query);
      $STH->execute($PdoArr);
      
      
      while ($row1 = $STH->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION[$row1['ParamNo']]=$row1['Value'];     
      }

      if ($_REQUEST['Aftr']!='') {
        $After=base64_decode($_REQUEST['Aftr']);
        echo ("<h1><a href='../../../..$After'>Go to link</a></h1>");
        echo ('<META HTTP-EQUIV="REFRESH" CONTENT="3;URL=../../../..'.$After.'">');

      } 

    }
    else {
        MakeAdminRec ($db, $login, 'BadPass', $login, 
                          $_SERVER['REMOTE_ADDR'], 'Bad password '.$login);
    }

    return ($Check);
  }

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

  return (false);

}
?>
<html>
<head>
<title>Vladislav +7 (903) 736 7000</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Language" content="ru">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
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
        if (check_login($pdo, $login, $pass, $SecTxtMsg))
            $_SESSION['login'] = $login;
        else
            draw_form(true);
            // параметр true передается чтобы показать, что был введен
            // неправильный пароль
    }
}


if (!isset($_SESSION['login'])) {
  echo ("<br><br><a href='FogotPasswd.php'>Forgot password / ".ToUtf('Пароль забыт')."</a>");
}

isset($_SESSION['login']) or die(); // здесь если функция вернула false то выполняется die()

echo '<br>Good day, ' . $_SESSION['login'].'! ';

if ($_SESSION['login'] !=  '' ) { 
  ShowMenu ($pdo, 0,0, 0, '');
}  


echo ("<hr><br><a href='user_setup.php'>User Setup</a><br>");


AdminFooter ();



?>

</body>
</html>
                                                            