<?php
session_start();
include ("../setup/common.php");
BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>RespPersons list</title></head>
<body>
<?php
include ("../../js_SelAll.js");
$TabName='RespPersons';
$CurrFile='RespPersonsList.php';

$Frm='RespPersons';
$Fields=array('ObjId','Param1','enContactType'
      ,'ContactId','Description','Rank');
$enFields= array('enContactType'=>'ContactType', 'Rank'=>'RespRank');

CheckRight1 ($mysqli, 'ExtProj.Admin');

$Mail = addslashes ($_REQUEST['Mail']);
if ($Mail==''){
  die ("<br> Error: Mail is empty");
}


  echo("<hr><h4>".GetStr($mysqli, 'RespPersons')."</h4>");
  
  $query = "select * FROM RespPersons where ContactId='$Mail' ".
           "order by ObjId,Param1,enContactType,ContactId ";
  $sql2 = $mysqli->query ($query)
            or die("Invalid query:<br>$query<br>" . $mysqli->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($mysqli, 'ObjId').'</th>');
  echo('<th>'.GetStr($mysqli, 'Param1').'</th>');
  echo('<th>'.GetStr($mysqli, 'enContactType').'</th>');
  echo('<th>'.GetStr($mysqli, 'ContactId').'</th>');
  echo('<th>'.GetStr($mysqli, 'Description').'</th>');
  echo('<th>'.GetStr($mysqli, 'Rank').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['ObjId']}</td>");


    echo ("<td>");
    echo ("{$dpL['Param1']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($mysqli, "ContactType", $dpL['enContactType'])."</td>");
    echo ("<td>");

    echo("<a href='RespPersonsCard.php?ObjId={$dpL['ObjId']}&Param1={$dpL['Param1']}&enContactType={$dpL['enContactType']}&ContactId={$dpL['ContactId']}'>");
    echo ("{$dpL['ContactId']}</a></td>");
    echo ("<td>");
    echo ("{$dpL['Description']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($mysqli, "RespRank", $dpL['Rank'])."</td>");
    
  }
  echo("</tr></table>");


?>
</body>
</html>
