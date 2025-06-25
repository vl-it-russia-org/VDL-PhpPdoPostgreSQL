<?php
session_start();
isset($_SESSION['login']) or die('вы не вошли');
isset($_SESSION['admin_login']) or die('Вы не вошли как Admin');
?>
    
<html>
<head><title>User update</title></head>
<body>
<?php
include ("../setup/config.php");
include ("set_passw.php");
  
  $usrlogin = $_POST['usrlogin'];


  $NewPasswd='';
  if (!empty($_POST['new_passw'])){
    $NewPasswd=GetNewPass ();
  };

  $NewAdmin=0;
  if (!empty($_POST['admin'])){
    $NewAdmin=1;
  };
  //echo ("<br>".$NewAdmin."<br>".$_POST['admin']."<br>");

  if (!empty($usrlogin)) {
    $sql = mysql_query ("select usr_id, description, admin, email, phone 
           from usrs where usr_id='$usrlogin'");
    if ($dp = mysql_fetch_object($sql)) {
      $upd_sql='';
      if ($dp->description != $_POST['full_name']) {
        if (!empty ($upd_sql))
          $upd_sql=$upd_sql.',';  
        $upd_sql=$upd_sql."description='".$_POST['full_name']."'";
      };

      if ($dp->email != $_POST['email']) {
        if (!empty ($upd_sql))
          $upd_sql=$upd_sql.',';  
        $upd_sql=$upd_sql." email='".$_POST['email']."'";
      };  

      if ($dp->phone != $_POST['phone']) {
        if (!empty ($upd_sql))
          $upd_sql=$upd_sql.',';  
        $upd_sql=$upd_sql." phone='".$_POST['phone']."'";
      };  
      
      if ($dp->admin != $NewAdmin) {
        if (!empty ($upd_sql))
          $upd_sql=$upd_sql.',';  
        $upd_sql=$upd_sql." admin=".$NewAdmin;
      };
       
      if (!empty ($NewPasswd)) {
        if (!empty ($upd_sql))
          $upd_sql=$upd_sql.',';  
        $upd_sql=$upd_sql." usr_pwd='".$NewPasswd."'";
      };  
      


      if (!empty ($upd_sql)) {
        $query = "update usrs set ".$upd_sql." where usr_id='".$usrlogin."'";
        
        //echo ("<br>".$query."<br>");

        $sql2 = mysql_query ($query)
	               or die("Invalid query:<br>$i $query<br>" . mysql_error()); 

        echo ('User '.$usrlogin.' has been updated.');
        
        
        if ($dp->admin != $NewAdmin) {
          MakeAdminRec ($_SESSION['admin_login'], 'UPD_ADMIN', $usrlogin, 
                        $NewAdmin, 
                        'Change admin privelege for '.$usrlogin.' email '.$_POST['email']);

        }

        if (!empty ($NewPasswd)) {
          
          MakeAdminRec ($_SESSION['admin_login'], 'UPD_PASSWD', $usrlogin, 
                        $_POST['new_passw'], 
                        'Change passwd to user '.$usrlogin.' email '.$_POST['email']);


          if ($_POST['new_passw'] == 'by_mail') {
            SendPasswToUser ( $usrlogin );
            echo ('<br>New password has been sent to user by e-mail');
          }
          else {
            echo('<br>New password is:'.$NewPasswd);
          };
        };
      }
    }
  }
  AdminFooter ();

?>
</body>
</html>
  
  
 


  
