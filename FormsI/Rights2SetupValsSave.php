<?php
session_start();

include ("../setup/common_pg.php");

CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

//print_r ($_POST);
//echo('<br>');
//die();

$RightSel = $_POST['RightSel'];
$FilterRight = $_POST['FilterRight'];

$LocSel = $_POST['LocSel'];
$WH = $_POST['WH'];
$WHF = $_POST['WHF'];
$BegPos = $_REQUEST['BegPos']+0;

if ( ($RightSel=='') OR ($LocSel=='')) {
  die ('Update error');
};
echo ('<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=Rights2SetupVals.php?RightSel='.$RightSel.
     '&Sub='.$LocSel.'&WH='.$WH.'&WHF='.$WHF.'&BegPos='.$BegPos.
     "&FilterRight=$FilterRight".'">
<title>Advertazing</title></head><body>');

echo '<br>User: ' . $_SESSION['login'];

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

  foreach ( $ArrayChange as $Usr ) { 
    $PdoArr = array();
    $PdoArr['Usr']= $Usr;
    $PdoArr['RightSel']= $RightSel;
    $PdoArr['LocSel']= $LocSel;
    
    $query = "select \"Val\" FROM \"UsrRights\" ".
             "WHERE (\"UsrName\"=:Usr) AND (\"RightType\"=:RightSel) AND ".
             "(\"RightSubType\"=:LocSel)";
    //echo ("<br>$query<br>");

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);


    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
      $Res= $dp['Val']; 
      if ($Res==0) {
        $Res=1;
      }
      else $Res=0;
      
      $PdoArr['Res']= $Res;

      $query = "update \"UsrRights\" set \"Val\"=:Res ".
             "WHERE (\"UsrName\"=:Usr) AND (\"RightType\"=:RightSel) AND ".
             "(\"RightSubType\"=:LocSel)";
         
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
      
      if ($Res==1) {
        MakeAdminRec ($pdo,'', 'RM', $RightSel, $LocSel, 'Right granted to User '.$Usr);
      }
      else {
        MakeAdminRec ($pdo,'', 'RM', $RightSel, $LocSel, 'Right revoked from User '.$Usr);
      }
    }
    else {
      $query = "insert into \"UsrRights\" (\"UsrName\", \"RightType\", \"RightSubType\", \"Val\") ".
               "VALUES (:Usr, :RightSel, :LocSel, :Res)";

      $PdoArr['Res']= 1;
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);

      $R1= $RightSel;
      if ($LocSel!='') {
        $R1.="-$LocSel";
      }
      MakeAdminRec ($pdo,'', 'RM', $RightSel, $LocSel, 'Right granted to User '.$Usr);

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
				       