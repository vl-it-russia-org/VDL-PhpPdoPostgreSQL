<?php
session_start();

include ("../setup/common.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Tab Index Edit</title></head>
<body>
<?php

CheckLogin1();

CheckRight1 ($mysqli, 'RIGHT_EDIT');

//print_r ($_REQUEST);

//SELECT  FROM  WHERE 1

$TabName='AdmTabIndx';
$Frm='Tab';

$Fields=array('TabCode', 'IndxType', 'IndxName');


$TabNo= addslashes ($_REQUEST['TabNo']);
$IndxName= addslashes ($_REQUEST['IndxName']);

if ($TabNo == '') {
  $TabNo= addslashes ($_REQUEST['TabCode']);
}


if ($TabNo == '') {
  die ("<br> Error: Empty TabNo ");
}

if ($IndxName == '') {
  die ("<br> Error: Empty IndxName ");
}

  
echo ("<br><a href='TabList.php'>".GetStr($mysqli,'List')."</a>");
echo ("<br><a href='TabCard.php?TabCode=$TabNo'><b>".
      GetStr($mysqli, 'Index'). " $TabNo $IndxName</b></a>");

$query = "delete ".
         "FROM AdmTabIndx where TabCode='$TabNo' and IndxName= '$IndxName'";

//echo ($query);


$sql2 = $mysqli->query ($query)
               or die("Invalid query:<br>$query<br>" . $mysqli->error);

//===============================================================================
$query = "delete ".
         "FROM AdmTabIndxFlds where TabCode='$TabNo' and IndxName='$IndxName'";

$sql2 = $mysqli->query ($query)
           or die("Invalid query:<br>$query<br>" . $mysqli->error);

//=====================================================================================
  
echo ("<br>Index deleted<br>");
?>
</body>
</html>				       