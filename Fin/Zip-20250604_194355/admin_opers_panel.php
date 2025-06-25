<?php
session_start();

include ("../setup/common.php");

//isset($_SESSION['admin_login']) or die('You are not login as Admin/Вы не вошли как Admin');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Language" content="ru">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Admin operations</title></head>
<body>
<?php

echo ('<table><tr valign=top><td>');

echo ('10. '.
        GetStr($mysqli,'Finance').
      '</a><br><small>'.
      'Finance</small><br>'); 

echo ('<a href="CurrencyExchRateList.php"> -- 10.10. '.
        GetStr($mysqli,'CurrencyExchRate').
      '</a><br><small>Currency Exchange Rate</small><br>'); 

echo ('<a href="CB_BanksList.php"> -- 10.20. '.
        GetStr($mysqli,'CB_Banks').
      '</a><br><small>CB Banks list</small><br>'); 
      
echo ('</td><td>');



echo ('</td><td>');


echo ('</td></tr></table>');

?>
</body>
</html>
