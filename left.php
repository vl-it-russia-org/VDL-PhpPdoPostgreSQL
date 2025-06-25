<?php
session_start();
include ("../setup/common_pg.php");


?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Accounting php</title>
<link rel="stylesheet" type="text/css" href="style.css"> 
</head>
<body>
<br>
<img src="../logo-Kolya.png" width=80 height=21 alt=logo border>
<br>
<?php
echo ('


<br>
<a class="menu" href="FormsI/TabList.php" target="mainFrame">TabList</a>

<br>
<a class="menu" href="FormsI/admin_opers_panel.php" target="mainFrame">Admin</a>


<br><br><hr><br>

<a class="menu" href="FormsI/Login.php" target="mainFrame">Login</a>
<br>
<br><a class="menu" href="FormsI/Login.php?logout" target="mainFrame">LogOut</a>
');
?>
</body>
</html>