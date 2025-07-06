<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='usrs';
OutHtmlHeader ($TabName." card");


// Checklogin1();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('usr_id','usr_pwd','description','admin'
          ,'email','phone','passwd_duedate','new_passwd'
          ,'passwd_last_change','Blocked','WebCookie','Position'
          ,'Department','Company','FirstName','LastName'
          ,'PatronymicName__c','PwdCoded');
$enFields= array();
$PdoArr = array();
if (empty ($_REQUEST['Usr'])) {
  die ("<br> Error: User is empty");
}

$usr_id=$_REQUEST['Usr'];
$PdoArr["usr_id"]=$usr_id;
echo("<H3>".GetStr($pdo, 'usrs')."</H3>");
  
  $dp=array();
  $FullLink="usr_id=$usr_id";

  $query = "select * FROM \"usrs\" ".
           "WHERE (\"usr_id\"=:usr_id)";
  
  try {
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  
  
$Editable=0;
if (1==1) {
  echo ("<table>");

  $Fld='usr_id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td colspan=3>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='email';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td>");
  
  $Fld='phone';
  $OutVal= $dp[$Fld];
  echo ("<td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='Blocked';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='Position';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td>");
  
  $Fld='Department';
  $OutVal= $dp[$Fld];
  echo ("<td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='Company';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='FirstName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td>");
  
  $Fld='LastName';
  $OutVal= $dp[$Fld];
  echo ("<td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='PatronymicName__c';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  echo ("</table>");
}
echo ("  <hr><br><a href='usrsList.php'>".GetStr($pdo, 'List')."</a>");

  echo("<hr><h4>".GetStr($pdo, 'UsrRights')."</h4>");
  $query = "select * FROM \"UsrRights\" ".
           "where (\"UsrName\"=:usr_id) and \"Val\" order by \"UsrName\",\"RightType\",\"RightSubType\"";
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'RightType').'</th>');
  echo('<th>'.GetStr($pdo, 'RightSubType').'</th>');
  //echo('<th>'.GetStr($pdo, 'Val').'</th>');
  echo('<th></th>');
  $i=0;
  
  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['RightType']}</td>");
    echo ("<td>");

    echo("<a href='UsrRightsCard.php?UsrName={$dpL['UsrName']}&RightType={$dpL['RightType']}&RightSubType={$dpL['RightSubType']}'>");
    echo ("{$dpL['RightSubType']}</a></td>");
    
    //echo ("<td align=center>");
    //$Ch="";
    //  if ($dpL['Val']==1) {
    //    $Ch=" checked ";  
    //  }
    //    echo ("<input type=checkbox Name=Val value=1 $Ch></td>");
      
    
  }
  echo("</tr></table>");
  echo("<a href='UsrRightsCard.php?New=1&UsrName=$UsrName'>".GetStr($pdo, "Add")."</a>");

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
