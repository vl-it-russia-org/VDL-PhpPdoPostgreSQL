<?php
session_start();

include ("../setup/config_pg.php");
include ("set_passw.php");

?>
<html>
<head>
<title>Vladislav +7 (903) 736 7000</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
</head>
<body>
<?php

// >>> точка входа <<<
echo("Vladislav +7 (903) 736 7000<br>");


// на случай если мы уже авторизированы
$input1 = array("21", "34", "45", "67", "11", "14", "25");
$input2 = array("1",  "2",  "3",  "4",  "5",  "6" , "7");

$rand_keys1 = array_rand($input1, 1);
$rand_keys2 = array_rand($input2, 1);

//echo ("<br>");
//print_r($rand_keys1);

//echo ("<br>");
//print_r($rand_keys2);

$Val1= 0+ $input1[$rand_keys1];
$Val2= 0+ $input2[$rand_keys2];

$Res = $Val1 + $Val2; 

if (!isset($_SESSION['login'])) {

  echo ('<form action="ForgotPasswdSave.php" method="post">'.
        '<table>'.
        '<tr><td align=right>Your e-mail / Ваш e-mail:</td><td><input type="text" required size=40 name="mail"></input></td></tr>'.
        '<tr><td>Confirm You are not robot: '.
        "$Val1 + $Val2 =</td><td><input type=text size=5 name=Res required></input>".
        "<input type=hidden name=Key1 value='$rand_keys1'>".
        "<input type=hidden name=Key2 value='$rand_keys2'>".
        "</td></tr>".
        '<tr><td colspan=3 align=right><input type="submit" name="submit" value="Restore Password"></td></tr>'.
        '</table></form>');
}

?>

</body>
</html>
                                                            