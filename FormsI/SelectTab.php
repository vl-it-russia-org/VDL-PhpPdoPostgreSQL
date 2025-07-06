<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Table list</title></head>
<body>
<?php

CheckLogin1();

CheckRight1 ($pdo, 'Admin');


//SELECT 'TabName', 'TabDescription', 'TabCode' FROM  WHERE 1

$TabName='AdmTabNames';
$CurrFile='SelectTab.php';
$Frm='Tab';

$ElId   = $_REQUEST['ElId'];
$SubStr = $_REQUEST['SubStr'];
$Par2   = $_REQUEST['Par2'];

echo ("<script>".
  "function SetSelect( Val ) { 
     OW=window.opener;
     
     var elem1 = OW.document.getElementById('$ElId');
     if (elem1) { 
       elem1.value=elem1.value + '[T:'+Val+']';
     }
     
     window.close();
  }
  </script>");

$Fields=array(  'TabCode', 'TabName', 'TabDescription');
 

  $BegPos =$_REQUEST['BegPos']+0;
  if ($BegPos==''){
    $BegPos=0;
  }

  $ORD = $_REQUEST['ORD'];
  if ($ORD =='1') {
    $ORD = '"TabName"';
  }
  else {
    $ORD = '"TabName"';
  }
  $ORDS = ' order by  '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  }
  else {
  }

$PdoArr = array();
try {
  $WHS = '';
  $FullRef='?ORD='.$ORD."&ElId=$ElId&SubStr=$SubStr&Par2=$Par2";
  


  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST['Fltr_'.$Fld];
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      
      $WHS.='("'.$Fld."\" Like :$Fld)"; 
      $PdoArr[$Fld]= "%$Fltr%";
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
    }
  }
  
  $LN = $_SESSION['LPP'];
  if ($LN=='') {
    $LN=20;  
  };

  if ($WHS != '') {
    $WHS = ' where '.$WHS;
  };   

  $query = "select * FROM \"$TabName\" ".
           "$WHS $ORDS LIMIT $LN offset $BegPos";

  //echo ($query);

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"$TabName\" ".
              "$WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($pdo, 'SeriesList'). '</b> '.$CntLines.
       ' total lines Page <b>'.$CurrPage.'</b> from '. $LastPage) ;
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

 
  echo ('<form method=get action="'.$CurrFile.'"><table><tr>'.
        "<input type=hidden name='ElId' value='$ElId'>".
        "<input type=hidden name='SubStr' value='$SubStr'>".
        "<input type=hidden name='Par2' value='$Par2'>"
  );
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==3){
      echo('</tr><tr>');
      $i=0;
    }     
    $i++;
    echo("<td align=right>$Fld:</td>".
      "<td><input type=text length=30 size=20 name='Fltr_$Fld' value='".
        $_REQUEST['Fltr_'.$Fld]."'></td>");
  }
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');

//--------------------------------------------------------------------------------

echo ('<table><tr class="header"><td></td>');

foreach ( $Fields as $Fld) {
  echo("<td><b>$Fld</b></td>");
}
echo("</tr>");

$n=0;
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }
  
  echo ("<tr".$classtype.">");
  $Res=addslashes($dp['TabName']);
  echo("<td><input type=button value='".GetStr($pdo, 'Select').
        "' onclick='return SetSelect(\"$Res\");'></td>");


  foreach ( $Fields as $Fld) {
    if ($Fld=='TabCode') {
      echo('<td align=left><a href="'.$Frm.'Card.php?'.$Fld.'='.$dp[$Fld].'">'.
           $dp[$Fld]."</a></td>");
    }
    else {
      echo('<td>'.$dp[$Fld]."</td>");
    
    }
  }
  echo("</tr>");
};

echo ("</table>");

$PredPage= $BegPos-$LN;
if ($PredPage<0)
  $PredPage = 0; 

$LastPage1= floor($CntLines/$LN) * $LN;

echo('<table><tr class="header">');

if ($CurrPage>1) {
  echo('<td><a href="'.$CurrFile.$FullRef.'&BegPos=0"> << First page </a></td>' .
       '<td><a href="'.$CurrFile.$FullRef.'&BegPos='.$PredPage.'"> < Pred Page </a></td>');
};

echo ('<td>Page '.$CurrPage.'</td>');

if ($CurrPage< $LastPage) {
  echo ('<td><a href="'.$CurrFile.$FullRef.'&BegPos='.($BegPos+$LN).'"> Next Page > > </a></td>');
};

echo ('<td><a href="'.$CurrFile.$FullRef.'&BegPos='.$LastPage1.'"> Last Page '.$LastPage.'>> </a></td>'.
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