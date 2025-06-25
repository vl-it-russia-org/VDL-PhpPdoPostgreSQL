<?php
session_start();
isset($_SESSION['login']) or die('вы не вошли');
isset($_SESSION['admin_login']) or die('вы не вошли');
?>

<html>
<head><title>Insert new user</title></head>
<body>
<?php
include ("../setup/config.php");
include ("set_passw.php");
    
    $usrlogin = $_POST['usrlogin'];
    $full_name= $_POST['full_name'];
    $email    = $_POST['email'];
    $admin    = $_POST['admin'];
    $phone    = $_POST['phone'];

    if (empty ($usrlogin) or empty ($full_name) or empty ($email) or
       empty ($phone)) {
      echo ('<br>All fields should be fullfiled please repeat.');

    }
    else {
      $sql = mysql_query ("select phone from usrs where usr_id='".$usrlogin."'");
      if ($dp = mysql_fetch_object($sql)) {
        echo ('We already have user '.$usrlogin.' in database');
      }
      else {
        //$sql = mysql_query ("select usr_id from usrs where email='".$email."'");
        //if ($dp = mysql_fetch_object($sql)) {
        //  echo ('We already have user '.$dp->usr_id.' with e-mail '.$email.' in database');
        //}
        //else {
        {
          $new_pass=GetNewPass();
          $date = getdate();
          
          $adm_int = 0;
          if ($admin=='on') {
            $adm_int = 1;
          }

          $pass_duedate= $date[0] + 86400 * 90;
          $dt_lch = date ("Y-m-d", $date[0]);  
          $pass_duedate_txt = date ("Y-m-d", $pass_duedate);
          //echo ($pass_duedate_txt);  

          $query = "INSERT INTO usrs (usr_id, usr_pwd, description, admin, 
                    email, phone, passwd_duedate,passwd_last_change) 
                    VALUES 
	                ('$usrlogin','$new_pass', '$full_name', $adm_int, 
                    '$email', '$phone', '$pass_duedate_txt', '$dt_lch')";
         
          //echo ("<br>".$query);

          $sql2 = mysql_query ($query)
	               or die("Invalid query:<br>$i $query<br>" . mysql_error()); 

          SendPasswToUser ( $usrlogin );

          MakeAdminRec ($_SESSION['admin_login'], 'NEW_USR', $usrlogin, 
                        '', 'Add new user '.$usrlogin);
          echo ("<html><header>");
          echo ('<META HTTP-EQUIV="REFRESH" CONTENT="2;URL=users_list.php?usrlogin='.$usrlogin.'">
          </header><body>');
          echo("<br>New user $usrlogin added, password has been sent by e-mail to new user"); 

        }
      }
    }

  AdminFooter ();
?>
</body>
</html>