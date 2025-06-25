<?php
session_start();
include ("../setup/common.php");
BeginProc();

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>CurrencyExchRate list</title></head>
<body>
<?php
include ("../js_SelAll.js");
$TabName='CurrencyExchRate';
$CurrFile='CurrencyExchRateList.php';
$Frm='CurrencyExchRate';
$Fields=array('CurrencyCode','StartDate','Multy'
      ,'Rate','FullRate');
$enFields= array('CurrencyCode'=>'Currency');
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $BegPos = addslashes ($_REQUEST['BegPos']);
if ($BegPos==''){
$BegPos=0;
}

$ORD = addslashes ($_REQUEST['ORD']);
if ($ORD =='1') {
$ORD = 'CurrencyCode,StartDate desc';
  }
  else {
    $ORD = 'CurrencyCode,StartDate desc';
  }

  $ORDS = ' order by  '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  }
  
  $WHS = '';
  $FullRef='?ORD='.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=addslashes($_REQUEST['Fltr_'.$Fld]);
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      if ($enFields[$Fld]!='') {
        $WHS.='('.$Fld." = '$Fltr')"; 
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr );
      }
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

  $query = "select * ".
         "FROM CurrencyExchRate ".
         " $WHS $ORDS LIMIT $BegPos, $LN";

  $queryCNT = "select COUNT(*) CNT ".
         "FROM CurrencyExchRate ".
         " $WHS ";

  $sql2 = $mysqli->query ($queryCNT)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);

  $CntLines=0;
  if ($dp = $sql2->fetch_assoc()) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($mysqli, 'CurrencyExchRate').' '.
        GetStr($mysqli, 'List').
        '</b> '.$CntLines.' total lines Page <b>'.
        $CurrPage.'</b> from '. $LastPage) ;
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);

 
  echo ('<form method=get action="'.$CurrFile.'"><table><tr>');
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==4){
      echo('</tr><tr>');
      $i=0;
    }     
    $i++;
    echo("<td align=right>".GetStr($mysqli, $Fld).":</td>");

    if ($enFields[$Fld]!=''){
      echo("<td>".EnumSelection($mysqli, $enFields[$Fld],'Fltr_'.$Fld, $_REQUEST['Fltr_'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text length=30 size=20 name='Fltr_$Fld' value='".
        $_REQUEST['Fltr_'.$Fld]."'></td>");
    }
  }
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');
  echo ('<hr><table><tr><td><form method=post action="CurrencyExchRateCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($mysqli, 'New')."'></form></td><td>" );
//--------------------------------------------------------------------------------
echo ('<form method=post action="CurrencyExchRateGroupOp.php">'.
        "<input type=submit  Name=OpType Value='".GetStr($mysqli, 'Delete')."' 
          onclick='return confirm(\"Delete selected?\");'></td></tr></table>" );
echo ('<table><tr class="header">');

echo("<th><input type=checkbox onclick='return SelAll();'></th>");


foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($mysqli, $Fld)."</th>");
}
echo("</tr>");

$n=0;
$Cnt=0;
while ($dp = $sql2->fetch_assoc()) {
  $Cnt++;
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }
  
  echo ("<tr".$classtype.">");

  $PKValArr=array();
    $PKValArr['CurrencyCode']= $dp['CurrencyCode'];
    $PKValArr['StartDate']= $dp['StartDate'];
  $PKRes=base64_encode( json_encode($PKValArr));
  
  echo ("<td><input type=checkbox ID='Chk_$Cnt' Name=Chk[$Cnt] value='$PKRes'></td>");
  

  $Fld='CurrencyCode';
  echo("<td>".GetEnum($mysqli, 'Currency', $dp[$Fld])."</td>");
  

  $Fld='StartDate';
  echo("<td><a href='CurrencyExchRateCard.php?CurrencyCode={$dp['CurrencyCode']}&StartDate={$dp['StartDate']}'>{$dp[$Fld]}</a></td>");
  

  $Fld='Multy';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Rate';
  $OW=number_format($dp[$Fld], 4, ".", "'");
  echo("<td align=right> $OW </td>");
  

  $Fld='FullRate';
  $OW=number_format($dp[$Fld], 6, ".", "'");
  echo("<td align=right> $OW </td>");
  echo("</tr>");
}
echo("</table>".
     "<input type=hidden ID=AllCnt value='$Cnt'>".
     "<input type=submit Name=OpType Value='".GetStr($mysqli, 'Delete')."' 
          onclick='return confirm(\"Delete selected?\");'></form>");

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
      '<td><a href="CurrencyExchRatePrintXLS.php'.$FullRef.'">Print XLS</a></td>'.
      '<td><a href="UploadCurrencyExchRate.php?Days=45">Upload currency</a></td>'.

       '</tr></table>');

?>
</body>
</html>
