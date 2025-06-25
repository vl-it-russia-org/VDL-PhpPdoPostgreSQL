<?php
session_start();
include ("../setup/common.php");


?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Accounting php</title>
<link rel="stylesheet" type="text/css" href="style.css"> 
</head>
<body>
<br>
<img src="../logo2.png" width=80 height=21 alt=logo border>
<br>
<?php
echo ('
<br>
<a class="menu" href="../ExtProj/admin_opers_panel.php" target="mainFrame">'.
    GetStr($mysqli, 'ExtProj').'</a>

<br>
<br>
<a class="menu" href="../Pur/admin_opers_panel.php" target="mainFrame">'.
    GetStr($mysqli, 'Purchase').'</a>

<br>
<br>
<a class="menu" href="Reports.php" target="mainFrame">'.
    GetStr($mysqli, 'Reports').'</a>
<br>
<br>
<a class="menu" href="../ExtProj/admin_opers_panel.php" target="mainFrame">Admin</a>
<br><br><hr><br>
<a class="menu" href="Login.php" target="mainFrame">Login</a>
<br>
<br><a class="menu" href="Login.php?logout" target="mainFrame">LogOut</a>
');
?>
</body>
</html>