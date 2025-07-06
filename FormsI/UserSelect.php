<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
include ("common_func.php");
  
?>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>User Select</title></head>
<body>
<?php

$ResId=$_REQUEST['ResId'];

if ($ResId!='') {

echo("
<script>
function SetSelected(Val) {
  OW=window.opener;
  var elem = OW.document.getElementById('$ResId');
  elem.value=Val;
  window.close();
}
</script>");

}
else {
echo("
<script>
function SetSelected(Val, ValDescr) {
  OW=window.opener;
  //alert (OW);
  var elem = OW.document.getElementById('UM_SUB');
  elem.value=Val;

  var elem2 = OW.document.getElementById('DESCR_ID');
  elem2.value=ValDescr;

  window.close();
}
</script>");
}

  $BegPos = $_REQUEST['BegPos'];
  if ($BegPos==''){
    $BegPos=0;
  }


$FormName="UserSelect.php";
$PdoArr = array();

try {

  $BegPos = $_REQUEST['BegPos'];
  if ($BegPos==''){
    $BegPos=0;
  }

  $ORD = '';//2017-04-13 $_REQUEST['ORD'];
  $ORDS = ' order by "usr_id" '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  };
  
  
  $WH = $_REQUEST['WH'];
  $WHF = $_REQUEST['WHF'];
  
  $SubStr =  '%'.$_REQUEST['SubStr'].'%';
  $SubStr1 =  $_REQUEST['SubStr'];

  $ValF=array ();
  $ValF[ $_REQUEST['WHF'] ]=$WH; 


  $WHS = ''; 
  if ($WH !='') {
    $WHS = " and ".$_REQUEST['WHF']." LIKE '%".$WH."%'";
  };

  $LN = $_SESSION['LPP'];
  if ($LN=='') {
    $LN=20;  
  };

$WW='';
$Div='';
$AddSel1="&ResId=$ResId";

foreach ($_REQUEST as $Key => $Val) {
  $V = substr ( $Key , 0, 4 );
 
  if ($V=='Sts_') {
    $X= addslashes (substr ( $Key , 4 ));
    echo ("<br>$Key $X");
    $StsArr[$X]=1;
    $WW .= $Div.$X;
    $Div =',';
    $AddSel1.="&$Key=$X";
  } 
};

//echo("<br>");
//print_r ($_REQUEST);

//echo("<br>");
//print_r ($StsArr);


  $WHS.= " and ((\"email\" LIKE '$SubStr') OR (\"description\" like '$SubStr')) ";   
  $query = "select \"usr_id\", \"description\", \"email\" ".
         "FROM \"usrs\" ".
         " WHERE (1=1) ".
         " $WHS $ORDS ".AddLimitPos($BegPos, $LN);

  
  $queryCNT = "select COUNT(*) \"CNT\" ".
         "FROM  \"usrs\" ".
         " WHERE (1=1) ".
         " $WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);
  
  $CntLines=0;
  //echo ("<br>$query");
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.$CntLines.'</b> total lines Page <b>'.$CurrPage.'</b> from '. $LastPage) ;
  
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  echo ('<table><tr>
      <form method=post action="'.$FormName.'">
      <td>UserId:</td>
      <td><input type="text" length=10 name=WH value='.$ValF["usr_id"].'></td>
      <td><button name="WHF" value="usr_id" type="submit">Filter</button></td>
      </form>
      <form method=post action="'.$FormName.'">
      <td>User name:</td>
      <td><input type="text" length=10 name=WH value='.$ValF["description"].'></td>
      <td><button name="WHF" value="description" type="submit">Filter</button></td>
      </tr></form></table>');
  
//----------------------------------------------------------------------------------
echo ('<table>  
      <tr class="header">'.
      "<td></td><td><b>".GetStr($pdo, 'UserId')."</b></td>".
      "<td width='35%'><b>User Name</b></td><td><b>E-mail</b></td></tr>");

$n=0;
//echo ("$query");
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }
  echo ("<tr".$classtype.">");
  if ($ResId!='') {
    echo("<td> <input type=button value='".GetStr($pdo, 'Choose').
        "' OnClick='SetSelected(\"{$dp['usr_id']}\")'> </td>");
  }
  else {
    echo("<td> <input type=button value='".GetStr($pdo, 'Choose').
        "' OnClick='SetSelected(\"{$dp['email']}\",\"{$dp['description']}\" )'> </td>");
  }
  echo(
        '<td>'.$dp['usr_id']."</a></td>".
        "<td>".$dp['description']."</td>".
        "<td>".$dp['email']."</td>".
        "</tr>");
};

echo ("</table>");

$FullRef='?ORD='.$ORD.'&WH='.$WH.'&WHF='.$WHF.$AddSel1.'&SubStr='.$SubStr1;

$PredPage= $BegPos-$LN;
if ($PredPage<0)
  $PredPage = 0; 

$LastPage1= floor($CntLines/$LN) * $LN;

echo('<table><tr class="header">');

if ($CurrPage>1) {
  echo('<td><a href="'.$FormName.$FullRef.'&BegPos=0"> << First page </a></td>' .
       '<td><a href="'.$FormName.$FullRef.'&BegPos='.$PredPage.'"> < Pred Page </a></td>');
};

echo ('<td>Page '.$CurrPage.'</td>');

if ($CurrPage< $LastPage) {
  echo ('<td><a href="'.$FormName.$FullRef.'&BegPos='.($BegPos+$LN).'"> Next Page > > </a></td>');
};

echo ('<td><a href="'.$FormName.$FullRef.'&BegPos='.$LastPage1.'"> Last Page '.$LastPage.'>> </a></td>'.
       '</tr></table>');
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