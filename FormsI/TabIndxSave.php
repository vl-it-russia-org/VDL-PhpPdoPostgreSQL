<?php
session_start();

include ("../setup/common.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Tab index save</title></head>
<?php

CheckLogin1();

CheckRight1 ($mysqli, 'RIGHT_EDIT');


//print_r ($_REQUEST);
//die();

$TabName='AdmTabIndx';
$Frm='Tab';


$TabNo= addslashes ($_REQUEST['TabCode']);
$IndxType= addslashes ($_REQUEST['IndxType']);
$IndxName= addslashes ($_REQUEST['IndxName']);
$IndxOldName= addslashes ($_REQUEST['IndxOldName']);


if ($_REQUEST['New'] !='1' ) {
  $Proc='Updated';
  $query="update $TabName set IndxType='$IndxType',IndxName='$IndxName' ";
  $query.=" where (TabCode='$TabNo') AND (IndxName='$IndxOldName')";
}
else {
  $Str1='TabCode,IndxType,IndxName';
  $Str2="'$TabNo', '$IndxType', '$IndxName'";
  $query="insert into $TabName ($Str1) values ($Str2)";
}

//echo ($query);
$sql7 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);


// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$query = "update AdmTabNames set  ChangeDt=now() ". 
         "where (TabCode = '$TabNo')"; 

$sql5 = $mysqli->query ($query) 
          or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);



echo('
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$Frm.'Card.php?TabCode='.
     $TabNo.'#Indx_'.$IndxName.'">');

echo ('<body><br><b>'.GetStr($mysqli, 'Edit'). '</b> ') ;
echo ($Proc);

?>
</body>
</html>
				       