<?php
session_start();

include ("../setup/common_pg.php");

BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

//print_r($_REQUEST)
$Tab= $_REQUEST['Tab'];

$PdoArr = array();
$PdoArr['Tab']= "%".$Tab."%";

$Res=array();


// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$query = "select \"TabCode\", \"TabName\", \"TabDescription\" from \"AdmTabNames\" ". 
         "where (\"TabName\" like :Tab ) order by \"TabName\" "; 

try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

$j=0;
while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $j++;
  $Res[$j]=$dp2;
}

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

$Res['Count']=$j;

echo ( json_encode($Res));

?>