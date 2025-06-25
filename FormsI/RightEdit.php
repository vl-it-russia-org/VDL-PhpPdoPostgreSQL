<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Edit Rights</title></head>
<body>
<?php

//print_r ($_REQUEST);
CheckRight1 ($pdo, 'Admin');

$RightType=$_REQUEST['RightType'];
$RightDescription ='';
$HelpLink='';
$NeedLocation=0;
$EnumRight='';

$PdoArr = array();
try {

if (empty( $_REQUEST['AddNew'])) {
  $query = "select * FROM \"Rights\" ".
           "WHERE \"RightType\"=:RightType";
  $PdoArr['RightType']= $RightType;

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $RightDescription = $dp['RightDescription'];
    $HelpLink         = $dp['HelpLink'] ;
    $NeedLocation     = $dp['NeedLocation'];
    $EnumRight        = $dp['EnumRight'];
  };
}

$Chk='';
if ($NeedLocation==1) {
  $Chk='checked';
};

  
echo ('<form method=post action="RightSave.php">');
if ($_REQUEST['AddNew']!='') {
  echo ("<input type='hidden' name='AddNew' value='AddNew'>");
}


echo ('<table><tr>'.
      '<td align=right>'.GetStr($pdo, 'Right').
       '</td><td><input type="text" size=20 length=20 name=RightType value="'.$RightType.
       '"></td></tr><tr>'.
      '<td align=right>'.GetStr($pdo, 'Description').
       '</td><td><input type="text" size=50 length=100 name=RightDescription value="'.$RightDescription.
       '"></td></tr><tr>'.
      '<td align=right>'.GetStr($pdo, 'HelpLink').
       '</td><td><input type="text" size=60 length=200 name=RightDewscription value="'.$HelpLink.
       '"></td></tr><tr>'.
      '<td align=right>'.GetStr($pdo, 'EnumRight').
       '</td><td><input type="text" size=30 length=30 name=EnumRight value="'.$EnumRight.
       '"></td></tr><tr>'.

      '<td align=right>'.GetStr($pdo, 'NeedLocation').
       '</td><td><input type="checkbox" name=NeedLocation value=1 '.
       '></td></tr><tr><td><input type="submit"></form></td>'.
       
//-----------------------------------------------------------------       
       '<td></td><td>');

if ( $RightType != '' ) {
  echo ('<td><form method=post action="RightDel.php">'.
        "<input type=hidden name=RightType value='$RightType'>".
        "<input type=Submit value=Delete></form></td>");
}      
echo('</td></tr></table><br><br>');

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


echo ("<a href='RigtsSetup.php?RightSel=$RightType'>Setup Right</a>");

?>
</body>
</html>
				       