<?php
session_start();
isset($_SESSION['login']) or die('вы не вошли');
isset($_SESSION['admin_login']) or die('Вы не вошли как Admin');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="ru">
<title>User list</title></head>
<body>
<?php
include ("../setup/config.php");
include ("set_passw.php");
  $usrlogin = $_GET['usrlogin'];
  
  if (!empty($usrlogin)) {
    $sql = mysql_query ("select usr_id, description, admin, email, phone 
           from usrs where usr_id='$usrlogin'");
    
    if ($dp = mysql_fetch_object($sql)) {
      $IsAdm='';
      if ($dp->admin==1) {
        $IsAdm=' CHECKED';  
      };
      echo "<br>
      <form method='post' action='user_update.php' enctype='multipart/form-data'>
      <table border='0'>
        <tr>
          <td>User login</td> 
          <td><input type='text' value='$dp->usr_id' name='usrlogin'></td>
          <td> </td>
          <td>User full name</td> 
          <td><input type='text' value='$dp->description' name='full_name' size=30></td>
        </tr>

        <tr>
          <td>E-mail</td> 
          <td><input type='text' value='$dp->email' name='email' size=45></td>
          <td> </td>
          <td>Phone</td> 
          <td><input type='text' value='$dp->phone' name='phone'></td>
        </tr>
        <tr>
          <td>New password</td>
          <td><input type='radio' name='new_passw' value='by_mail'>by e-mail</input></td>
          <td> </td>
          <td> </td>
          <td> </td>
        </tr>
        <tr>
          <td> </td>
          <td><input type='radio' name='new_passw' value='on_screen'>Show on screen</input></td>
          <td></td> 
          <td></td> 
          <td> </td>
        </tr>
        <tr>
          <td>Admin</td> 
          <td><input type='checkbox' name='admin' $IsAdm></td>
          <td></td> 
          <td><input type='submit' value='Save'></td>
        </tr>
      </table>
      </form><a href='user_setup.php?UserId=".$dp->usr_id."'>Additional setup</a>";
        
    
    }
  
  };

  if (empty($usrlogin)) {
    echo ('<table>
      <form method=get action="users_list.php"><tr>');
      $UsrFltr=addslashes ($_REQUEST['USRFLTR']);
      $CmpType=addslashes ($_REQUEST['CMPTYPE']);
      echo ('<td align=right>'.GetStr('UserId').':</td>'.
        '<td>'.EnumSelection ('enCompareType', 'CMPTYPE',  $CmpType).'</td>  
         <td><input type="text" length=30 name=USRFLTR value="'.$UsrFltr.'"></td>
         <td><input type=submit name=Filter value="Filter"></td>
         </tr></form></table>');

    $WhereAdd='';
    if ($UsrFltr!= '') {
      $WhereAdd=' where (usr_id LIKE \'';
      if ( ($CmpType==20) or ($CmpType==30)) {
        $WhereAdd.='%';  
      }

      $WhereAdd.= $UsrFltr;
      if ( ($CmpType==10) or ($CmpType==20)) {
        $WhereAdd.='%';  
      }
      $WhereAdd.= "')";
    } 
  

    echo ('<br><table border="1">');

          echo ('<tr>');
          echo ("<td><b>User id</b></td>");
          echo ("<td><b>Description</b></td>");
          echo ("<td><b>Admin</b></td>");
          echo ("<td><b>Email</b></td>");
          echo ("<td><b>Phone</b></td>");
          echo ('</tr>');


    $sql = mysql_query ("select usr_id, description, admin, email, phone from usrs $WhereAdd order by usr_id");
        while ($dp = mysql_fetch_object($sql)) {
          echo ('<tr>');
          echo ("<td><a href='users_list.php?usrlogin=".$dp->usr_id.
                "'>".$dp->usr_id."</a></td>");
          echo ("<td>".$dp->description."</td>");
          echo ("<td>".$dp->admin."</td>");
          echo ("<td>".$dp->email."</td>");
          echo ("<td>".$dp->phone."</td>");
          echo ('</tr>');
        }

    echo ('</table>');  
  }
  else {

    $usrlogin = $_GET['usrlogin'];
  }
  AdminFooter (); 
?>
</body>
</html>
