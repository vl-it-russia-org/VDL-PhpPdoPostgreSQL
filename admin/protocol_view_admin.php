<?php
session_start();
isset($_SESSION['login']) or die('You are not login/вы не вошли');
isset($_SESSION['admin_login']) or die('You are not login as Admin/Вы не вошли как Admin');
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<title>Protocol administrator</title></head>
<body>
<?php
include ("../setup/config.php");
include ("set_passw.php");
  
  $date_beg='';
  $date_end='';
  $OpCode='';
  $WHS='';

  if (!empty($_GET['OpCode'])) {
    $OpCode = $_GET['OpCode'];
    $WHS=" and (code='$OpCode') "; 
  }

  if (!empty($_GET['date_beg'])) {
    $date_beg = $_GET['date_beg'];  
  }
  else {
    $date = getdate();
    $before_date= $date[0] - 86400 * 10;
    $date_beg = date ("Y-m-d", $before_date);
  }

  if (!empty($_GET['date_end'])) {
    $date_end = $_GET['date_end'];  
  }
  else {
    $date = getdate();
    $before_date= $date[0] + 86400 ;
    $date_end = date ("Y-m-d", $before_date);
  }

  echo ('
  <form action="" method="get">
  <br>Date filter: <input type="date" value="'.$date_beg.'" name="date_beg"></input>
  <input type="date" value="'.$date_end.'" name="date_end"></input>
  OpCodeFilter <input type="text" value="'.$OpCode.'" name="OpCode"></input>
  <input type="submit" name="submit" value="update"></input><br>
  </form>');

  $sql = mysql_query ("select op_date, code, param1, param2, description, 
                       user_id 
           from admin_protocol where (op_date >='$date_beg') and (op_date <='$date_end') ".$WHS);
    
    echo ("<table border=1>
        <tr>
          <td>Who did</td> 
          <td>OpDate</td>
          <td>OpCode</td>
          <td>Description</td>
          <td>Param1</td>
          <td>Param2</td>
        </tr>");


    while ($dp = mysql_fetch_object($sql)) {
      echo ("  
        <tr>
          <td>$dp->user_id</td>
          <td>$dp->op_date</td>
          <td>$dp->code</td>
          <td>$dp->description</td>
          <td>$dp->param1</td>
          <td>$dp->param2</td>
        </tr>");
    };
    echo ('</table>');
  
  AdminFooter (); 
?>
</body>
</html>
