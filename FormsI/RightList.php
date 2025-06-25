<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Mnf Label Print</title></head>
<body>
<?php

CheckRight1 ($pdo, 'RIGHT_EDIT');
try {
  $PdoArr = array();

  $BegPos = $_REQUEST['BegPos'];
  if ($BegPos==''){
    $BegPos=0;
  }

  $ORD = '';//2017-04-13 $_REQUEST['ORD'];
  $ORDS = ' order by "RightType" '; 
  if ($ORD !='') {
    //$ORDS = ' order by '.$ORD;
  };
  
  
  $WH =$_REQUEST['WH']; //2017-04-13

  $ValF=array ();
  $ValF[ $_REQUEST['WHF'] ]=$WH; 


  $WHS = ''; 
  if ($WH !='') {
    $F=$_REQUEST['WHF'];
    $WHS = " and (\"$F\" LIKE :$F ) ";
    $PdoArr[$F]="%$WH%";
  };


  $LN = $_SESSION['LPP'];
  if ($LN=='') {
    $LN=20;  
  };


  $query = "select * FROM \"Rights\" ".
           "WHERE (1=1) ".
           "$WHS $ORDS LIMIT $LN offset $BegPos";

  //echo ("<br>$query<br>");

  $queryCNT = "select COUNT(*) \"CNT\" ".
         "FROM \"Rights\" ".
         " WHERE (1=1) ".
         " $WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);



  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.$CntLines.'</b> total lines Page <b>'.$CurrPage.'</b> from '. $LastPage) ;
  $sql2 = $pdo->query($query)
                 or die("Invalid query:<br>$query<br>" . $pdo->error);

 
  echo ('<table><tr>
      <form method=post action="RightList.php">
      <td>'.GetStr ($pdo, 'RightType').':</td>
      <td><input type="text" length=10 size=10 name=WH value='.$ValF["RightType"].'></td>
      <td><button name="WHF" value="RightType" type="submit">Filter</button></td>
      </form>
      <form method=post action="RightList.php">
      <td>'.GetStr ($pdo, 'RightDescription').':</td>
      <td><input type="text" length=20 size=20 name=WH value='.$ValF["RightDescription"].'></td>
      <td><button name="WHF" value="RightDescription" type="submit">Filter</button></td>
      </form>
      </tr>
      </table>');

  echo ('<form method=post action="RightEdit.php">
        <button name="AddNew" value="AddNew" type="submit">'.GetStr($pdo, 'New').
        '</button></form>');

$FieldName=array ('RightType', 'RightDescription', 'NeedLocation');

echo ('<table>  
      <tr class="header">');
      
foreach ($FieldName as $Val) {
  echo ("<td><b><a href='RightList.php?ORD=$Val'>".GetStr($pdo, $Val)."</a></b></td>");
};

echo ("</tr>");

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


$n=0;

while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }
  echo ("<tr".$classtype.">");

  $R=$dp['RightType'];
  foreach ($FieldName as $Val) {
    if ($Val=='RightType') {
      echo ("<td><a href='RightEdit.php?RightType=".
            $dp[$Val]."'>".$dp[$Val]."</a></td>");
    }
    else {
      echo ("<td>".$dp[$Val]."</td>");
    };
  }
  //https://msksrv.legrand-training.com/legrand/adv/RigtsSetup.php?RightSel=SF.Reports
  echo ("<td><a href='RigtsSetup.php?RightSel=$R'>Setup</a></td>");
  echo("</tr>");
};

echo ("</table>");
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


$FullRef='?ORD='.$ORD.'&WH='.$WH.'&WHF='.$WHF;

$PredPage= $BegPos-$LN;
if ($PredPage<0)
  $PredPage = 0; 

$LastPage1= floor($CntLines/$LN) * $LN;

echo('<table><tr class="header">');

if ($CurrPage>1) {
  echo('<td><a href="RightList.php'.$FullRef.'&BegPos=0"> << '.GetStr($pdo, 'FirstPage').' </a></td>' .
       '<td><a href="RightList.php'.$FullRef.'&BegPos='.$PredPage.'"> < '.GetStr($pdo, 'PredPage').'</a></td>');

};

echo ('<td><b>'.$CurrPage.'</b></td>');

if ($CurrPage< $LastPage) {
  echo ('<td><a href="RightList.php'.$FullRef.'&BegPos='.($BegPos+$LN).'"> '.
  GetStr($pdo, 'NextPage').' > </a></td>');
};

echo ('<td><a href="RightList.php'.$FullRef.'&BegPos='.$LastPage1.'"> Last Page '.$LastPage.'>> </a></td>'.
       '<td> <a href="RigtsSetup.php"> Setup to user </a></td></tr></table>');
?>
</body>
</html>