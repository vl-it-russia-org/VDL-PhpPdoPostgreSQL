<?php
session_start();
isset($_SESSION['login']) or die('вы не вошли');
isset($_SESSION['admin_login']) or die('вы не вошли');
include ("set_passw.php");
?>

<header><title>Add new user</title></header>
<body>
 <br>Insert new user
 <br>
 <form method='post' action='ins_new_user.php' enctype='multipart/form-data'>
   <table border='0'>
    <tr>
      <td>User login</td> 
      <td><input type="text" name='usrlogin'></td>
    </tr>
    <tr>
      <td>User full name</td> 
      <td><input type="text" name='full_name'></td>
    </tr>
    <tr>
      <td>E-mail</td> 
      <td><input type="text" name='email'></td>
    </tr>
    <tr>
      <td>Is Admin?</td> 
      <td><input type="checkbox" name='admin'></td>
    </tr>
    <tr>
      <td>Phone</td> 
      <td><input type="text" name='phone'></td>
    </tr>
    <tr>
      <td><input type='submit' value='Add new user'></td>
    </tr> 
    </table>
 </form>
<?php
  
  AdminFooter ();
?>
</body>