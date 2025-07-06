<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>AdmTabFields list</title></head>
<body>
<?php

CheckLogin1();

CheckRight1 ($pdo, 'Admin');


$TabName='AdmTabFields';
$CurrFile='SelectField.php';
$Frm='AdmTabFields';

$ElId   = $_REQUEST['ElId'];
$SubStr = $_REQUEST['SubStr'];
$Par2   = $_REQUEST['Par2'];

echo ("<script>".
  "function SetSelect( Val ) { 
     OW=window.opener;
     
     var elem1 = OW.document.getElementById('$ElId');
     if (elem1) { 
       elem1.value=elem1.value +'[F:'+Val+']';
     }
     
     window.close();
  }
  </script>");


$Fields=array('ParamName','DocParamType','AddParam','ParamNo');
$enFields= array('DocParamsUOM'=>'DocParamsUOM');

$BegPos = $_REQUEST['BegPos'];
if ($BegPos==''){
  $BegPos=0;
}

$PdoArr = array();
try {

$ORD = '"TypeId","ParamNo"';
  $ORDS = ' order by  '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  }
  else {
  }
  $PdoArr['TypeId']= $Par2;
  $WHS = "(\"TypeId\"=:TypeId)";
  
  $FullRef='?ORD='.$ORD."&ElId=$ElId&SubStr=$SubStr&Par2=$Par2";

  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST['Fltr_'.$Fld];
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      $PdoArr[$Fld]= "%$Fltr%";
      $WHS.='("'.$Fld."\" Like :$Fld)"; 
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

  $query = "select * FROM \"AdmTabFields\" ".
           "$WHS $ORDS ".AddLimitPos($BegPos, $LN);

  //echo ("<br>$query<br>");

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"AdmTabFields\" ".
              "$WHS ";


  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);

  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($pdo, 'AdmTabFields').' '.
        GetStr($pdo, 'List').
        '</b> '.$CntLines.' total lines Page <b>'.
        $CurrPage.'</b> from '. $LastPage) ;

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
     
  echo ('<form method=get action="'.$CurrFile.'"><table><tr>'.
        "<input type=hidden name='ElId' value='$ElId'>".
        "<input type=hidden name='SubStr' value='$SubStr'>".
        "<input type=hidden name='Par2' value='$Par2'>"
  );
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==4){
      echo('</tr><tr>');
      $i=0;
    }     
    $i++;
    echo("<td align=right>".GetStr($pdo, $Fld).":</td>");

    if ($enFields[$Fld]!=''){
      echo("<td>".EnumSelection($pdo, $enFields[$Fld],'Fltr_'.$Fld, $_REQUEST['Fltr_'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text length=30 size=20 name='Fltr_$Fld' value='".
        $_REQUEST['Fltr_'.$Fld]."'></td>");
    }
  }
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');
  echo ('<hr><br><form method=post action="AdmTabFieldsCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'></form>" );
//--------------------------------------------------------------------------------

echo ('<table><tr class="header"><th></th>');

foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
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
  $Res=addslashes($dp['ParamName']);
  echo("<td><input type=button value='".GetStr($pdo, 'Select').
        "' onclick='return SetSelect(\"$Res\");'></td>");

  foreach ( $Fields as $Fld) {
    if ($Fld=='ParamNo') {
      echo("<td align=left><a href='AdmTabFieldsCard.php?TypeId={$dp['TypeId']}&ParamNo={$dp['ParamNo']}'>{$dp[$Fld]}</a></td>");
    }
    else 
    if ($enFields[$Fld]!=''){
      echo("<td>".GetEnum($pdo, $enFields[$Fld], $dp[$Fld])."</td>");
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
