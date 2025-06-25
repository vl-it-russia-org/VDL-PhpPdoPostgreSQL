<?php
session_start();

include ("../setup/common_pg.php");

//print_r ($_POST);

CheckLogin1 ();

//echo('<br>');

$RightSel = $_POST['RightSel'];


if ($RightSel=='Route.Can') {
  CheckRight1 ($pdo, 'Route.Edit');
}
else {
  CheckRight1 ($pdo, 'RIGHT_EDIT');
}
  


$FilterRight = $_POST['FilterRight'];

$LocSel = $_POST['LocSel'];
$WH = $_POST['WH'];
$WHF = $_POST['WHF'];
$BegPos = $_REQUEST['BegPos'];
$EnumVal = $_REQUEST['EnumVal'];

//print_r($_REQUEST);


if ( $EnumVal != '') {
  $LocSel= $EnumVal;
}



if ( ($RightSel=='') OR ($LocSel=='')) {
  die ('Update error');
};

echo ('<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=RigtsSetup.php?RightSel='.$RightSel.
     '&LocSel='.$LocSel."&SubType=$LocSel&WH=".$WH.'&WHF='.$WHF.'&BegPos='.$BegPos.
     "&EnumVal=$EnumVal".
     "&FilterRight=$FilterRight".'">
<title>Advertazing</title></head><body>');

echo '<br>User: ' . $_SESSION['login'];

echo ("<br>EnumVal=$EnumVal<br>");


$ArrayChange = array ();
$i=0;

foreach ( $_POST as $Key => $Val) {
  if ( substr ($Key, 0, 4) == 'CHV_') {
    $i++;
    $ArrayChange[] = $Val; 
  }
}

echo ("<br>$i total changes");

if ($i>0) {
try {
  $query = "select \"Val\" FROM \"UsrRights\" ".
           "WHERE (\"UsrName\"=:Usr) AND (\"RightType\"=:RightSel) AND ".
                 "(\"RightSubType\"=:LocSel)";
  $STH = $pdo->prepare($query);
      
  $query = "update \"UsrRights\" set \"Val\"=:Res ".
           "WHERE (\"UsrName\"=:Usr) AND (\"RightType\"=:RightSel) AND ".
                 "(\"RightSubType\"=:LocSel)";
  
  $STHUpd = $pdo->prepare($query);
  
  $query = "insert into \"UsrRights\" (\"UsrName\", \"RightType\", \"RightSubType\", \"Val\") ".
           "VALUES (:Usr, :RightSel, :LocSel, :Res)";
  
  $STHIns = $pdo->prepare($query);
    

  foreach ( $ArrayChange as $Usr ) { 
    //echo ("<br>$query<br>");
    $PdoArr = array();
    $PdoArr['Usr']= $Usr;
    $PdoArr['RightSel']= $RightSel;
    $PdoArr['LocSel']= $LocSel;

    $STH->execute($PdoArr);

    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
      $Res= $dp['Val']; 
      if ($Res==0) {
        $Res=1;
      }
      else $Res=0;

      $PdoArr['Res']= $Res;

      $STHUpd->execute($PdoArr);
      
      if ($Res==1) {
        MakeAdminRec ($pdo,$_SESSION['login'], 'RM', $RightSel, $LocSel, 'Right granted to User '.$Usr);
      }
      else {
        MakeAdminRec ($pdo,$_SESSION['login'], 'RM', $RightSel, $LocSel, 'Right revoked from User '.$Usr);
      }
    }
    else {
      $query = "insert into UsrRights (UsrName, RightType, RightSubType, Val) ".
               "VALUES (:Usr, :RightSel, :LocSel, :Res)";

      $PdoArr['Res']= 1;

      $STHIns->execute($PdoArr);
      
      $R1= $RightSel;
      if ($LocSel!='') {
        $R1.="-$LocSel";
      }
      MakeAdminRec ($pdo,$_SESSION['login'], 'RM', $RightSel, $LocSel, 'Right granted to User '.$Usr);

    }
  }

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


}
    
echo('<br>'. GetStr($pdo,'Changed'));
?>
</body>
</html>
				       