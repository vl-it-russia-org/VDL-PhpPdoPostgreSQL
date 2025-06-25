<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" href="../Img/SQL.ico" type="image/x-icon">
<title>SQL Form</title></head>
<body>
<?php
include ('FrmSql.js');

CheckLogin1();
CheckRight1 ($pdo, 'Admin');


//SELECT 'TabName', 'TabDescription', 'TabCode' FROM  WHERE 1

try {

$query = "SELECT current_database() \"db\""; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  print_r($dp2);
  //echo ("<br>".$pdo->host_info);
}

echo (" | <a href='FrmSqlValuesUpload.php' target=SqlUpload>Sql Values Upload</a>");



echo ("<br><table><tr><td><form action='SqlRun.php'  method=post>".
      "SQL:<textarea cols=80 Rows=10 Name=SqlTxt ID=SqlTxt></textarea><br>".
      "<input type=submit value='Run SQL'></form></td>");

//======================================================================
echo ("<td><button  onclick='return PutTabName();'> &lsaquo;&lsaquo; </button> <input type=text Name=TabName size=15 ID=TabName placeholder='Table sub-name'> ".
      "<button onclick='return GetTabName();'>...</button><br>".
      "<select Name=TabList size=10 ID=TabList title='Tables' ondblclick='return TableSelected();' ".
         "onchange='return ShowTabInfo();'>".
         "<option value='' disabled>Select Table</option>".
      "</select><br><br>".
      "<select Name=FldList size=10 ID=FldList  ondblclick='return FldSelected();' ".
          "onchange='return ShowFldInfo();'>".
          "<option value='' disabled>Select Flield</option>".
      "</select></td>".
      "<td><div ID=TabInfo width=230></div><br><br>".
      "<textarea ID=FldInfo cols=30 rows=12></textarea></td>");

echo ("</tr></table>");
//=======================================================================

$Start=0;

$SArr = array();

for($i=1; $i<20; $i++) {
  $SArr[ $i*20 ]=1;
}


if ($_REQUEST['FLine'] !='') {
  $Start=$_REQUEST['FLine'];
}

  echo("<hr><h4>".GetStr($pdo, 'SqlLog')."</h4>");
  $query = "select * FROM \"SqlLog\" where \"TabNo\"='-10' order by \"Id\" desc limit 20 offset $Start ";
  
  $STH = $pdo->prepare($query);
  $STH->execute();


  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'User').'</th>');
  echo('<th>'.GetStr($pdo, 'OpDate').'</th>');
  echo('<th>'.GetStr($pdo, 'Description').'</th>');
  echo('<th>'.GetStr($pdo, 'SqlText').'</th>');
  $i=0;
  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['User']}</td>");
    echo ("<td>");
    echo ("{$dpL['OpDate']}</td>");
    
    echo ("<td>");
    echo ("{$dpL['Description']}</td>");
    echo ("<td>");
    echo ("{$dpL['SqlText']}</td>");
    
  }
  echo("</tr></table>");
  echo("<hr>");
  
  if ($Start>0) {
    echo ("<a href='FrmSql.php?FLine=0'>First page</a> | ");
  }

  if ($Start-20>0) {
    $PPage= $Start-20;  
    echo ("<a href='FrmSql.php?FLine=$PPage'>Pred page</a> | ");
  }

  if (1>0) {
    $PPage= $Start+20;  
    echo ("<a href='FrmSql.php?FLine=$PPage'>Next page</a>");
  }


}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>
</body>
</html>
                       