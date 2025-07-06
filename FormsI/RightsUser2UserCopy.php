<?php
session_start();
include ("../setup/common_pg.php");

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Rights copy</title></head>
<?php

$UsrFrom=$_REQUEST['FromUser'];
$UsrTo=$_REQUEST['ToUser'];

if ( ($UsrFrom=='') or ($UsrTo=='')) {
  die ('<br>Error: No user');
};

$FldsArr= array ('UsrName', 'RightType', 'RightSubType', 'Val');
$PKArr=array('UsrName', 'RightType', 'RightSubType');


echo ("<br> UsrFrom=$UsrFrom , UsrTo=$UsrTo<br>");

$PdoArr = array();
$PdoArr['UsrFrom']= $UsrFrom;
$PdoArr['UsrTo']= $UsrTo;

try {



//print_r ($_REQUEST);
//die ();

$query='SELECT "RF"."RightType", "RF"."RightSubType", "RF"."Val", ('.
          'SELECT "RT"."Val" FROM "UsrRights" "RT" '.
          "WHERE (\"RT\".\"UsrName\" =  :UsrTo) AND (\"RF\".\"RightType\" = \"RT\".\"RightType\") ". 
                 'AND ("RF"."RightSubType" = "RT"."RightSubType")) "CV" '.
       'FROM "UsrRights" "RF" '.
       "WHERE \"RF\".\"UsrName\"=:UsrFrom";

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


$i=0;
$Arr=array();

while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $i++;
  echo ("<br>$i: {$dp['RightType']}  {$dp['CV']}");

  if ( $dp['CV'] == '') {
    if ($dp['Val']==1) {
      MakeAdminRec ($pdo, $_SESSION['login'], 'RM', $dp['RightType'], $dp['RightSubType'], 
                    'Right granted to User '.$UsrTo);
      
      $Arr['UsrName']=$UsrTo;
      $Arr['RightType']=$dp['RightType'];
      $Arr['RightSubType']=$dp['RightSubType'];
      $Arr['Val']=$dp['Val'];



      UpdateTable ($pdo, 'UsrRights', $FldsArr, $Arr, $PKArr, 1);

      echo (' inserted ');
    }
  }
  else 
  if ( $dp['CV'] != $dp['Val']) {
    if ($dp['Val']==1) {
      MakeAdminRec ($pdo, $_SESSION['login'], 'RM', $dp['RightType'], $dp['RightSubType'], 
                    'Right granted to User '.$UsrTo);
    }
    else {
      MakeAdminRec ($pdo, $_SESSION['login'], 'RM', $dp['RightType'], $dp['RightSubType'], 
                    'Right revoked from User '.$UsrTo);
    }
    
    $Arr['UsrName']=$UsrTo;
    $Arr['RightType']=$dp['RightType'];
    $Arr['RightSubType']=$dp['RightSubType'];
    $Arr['Val']=$dp['Val'];



    UpdateTable ($pdo, 'UsrRights', $FldsArr, $Arr, $PKArr, 1);

    echo (' updated ');
  }
}

//=====================================================================================

echo ("<hr><br><a href='RigtsSetupAcc.php?RightSel=LocView&Usr=$UsrTo'>User rights</a>");
echo (" | <a href='../ExtProj/CopyUserVisibility.php?FromUser=$UsrFrom&ToUser=$UsrTo'>Ext proj visibility copy</a>");
echo (" | <a href='CopyMailRights.php?FromUser=$UsrFrom&ToUser=$UsrTo'>Copy RIGHTS mail</a>");
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