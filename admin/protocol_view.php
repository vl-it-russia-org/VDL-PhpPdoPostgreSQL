<?php
session_start();
isset($_SESSION['login']) or die('You are not login/вы не вошли');
isset($_SESSION['admin_login']) or die('You are not login as Admin/Вы не вошли как Admin');
?>
<html>
<head><title>Protocol requests</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
</head>
<body>
<?php

include ("../setup/config.php");
include ("set_passw.php");
  
  $date_beg='';
  $date_end='';
  $Who=addslashes ($_GET['Who']);
  
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
  <br>Who filter: <input type="text" value="'.$Who.'" name="Who"></input>
  <input type="submit" name="submit" value="update"></input><br>
  </form>');

  echo ('<table><tr><td>');
  echo ('
  <form action="PrintProtocol.php" method="post">
  <input type="hidden" value="'.$date_beg.'" name="DateBeg"></input>
  <input type="hidden" value="'.$date_end.'" name="DateEnd"></input>
  <input type="hidden" value="'.$Who.'" name="Who"></input>
  <input type="submit" name="Detailed To XLS" value="Detailed To XLS"></input><br>
  </form></td><td>');

  echo ('
  <form action="PrintProtocol1.php" method="post">
  <input type="hidden" value="'.$date_beg.'" name="DateBeg"></input>
  <input type="hidden" value="'.$date_end.'" name="DateEnd"></input>
  <input type="hidden" value="'.$Who.'" name="Who"></input>
  <input type="submit" name="Groupped To XLS" value="Grouped To XLS"></input><br>
  </form></td></tr></table>');
  
  $Query = "select op_date, ip_addr, reference, qty, answer, 
                       usr 
           from protocol where (op_date >='$date_beg') and (op_date <='$date_end')";

  if ($Who != '') {
    $Query .= " and (usr = '$Who')";
  }

  $sql = mysql_query ($Query);
  
  echo ("<table border=1>
        <tr>
          <td>Who did</td> 
          <td>OpDate</td>
          <td>IP address</td>
          <td>Reference</td>
          <td>Qty</td>
          <td>Answer</td>
        </tr>");


    while ($dp = mysql_fetch_object($sql)) {
      echo ("  
        <tr>
          <td>$dp->usr</td>
          <td>$dp->op_date</td>
          <td>$dp->ip_addr</td>
          <td>$dp->reference</td>
          <td>$dp->qty</td>
          <td>$dp->answer</td>
        </tr>");
    };
    echo ('</table>');
  
  AdminFooter (); 
?>
</body>
</html>
