<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>TabFld Save</title></head>
<?php

CheckLogin1();

CheckRight1 ($pdo, 'Admin');

//print_r ($_REQUEST);
//die();

$TabNo= $_REQUEST['TabNo'];
$PdoArr = array();
$PdoArr['TabNo']= $TabNo;
$TabName='';

try {

// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$query = "select * from \"AdmTabNames\" ". 
         "where (\"TabCode\" = :TabNo)  "; 



$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabName=$dp2['TabName'];
}
else {
  die ("<br> Error: Bad Table No= $TabNo ");

}

$PKName=$TabName.'_pkey';
$PdoArr['PKName']= $PKName;
// AdmTabIndx
// TabCode, IndxType, IndxName
$query = "select * from \"AdmTabIndx\" ". 
         "where (\"TabCode\" = :TabNo)and (\"IndxName\"=:PKName) "; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  echo ("<br> PK $PKName already exists ");
}
else {

  // AdmTabIndx
  // TabCode, IndxType, IndxName
  $query = "insert into \"AdmTabIndx\" (\"TabCode\", \"IndxType\", \"IndxName\") ". 
           "values (:TabNo, 10, :PKName) "; 

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);


  // AdmTabNames
  // TabName, TabDescription, TabCode, TabEditable, 
  // AutoCalc, CalcTableName, ChangeDt, Ver
  $PdoArr = array();
  $PdoArr['TabNo']= $TabNo;
  $query = "update \"AdmTabNames\" set \"ChangeDt\"=now() ". 
           "where (\"TabCode\" = :TabNo)"; 

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

}


}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}
echo('
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=TabIndxCard.php?TabCode='.
           $TabNo.'&IndxName='.$PKName.'">');

echo ('<body><br><b>Please setup PK fields</b>') ;
echo ($Proc);

?>
</body>
</html>
				       