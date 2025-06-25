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
<title>AdmNumberSeqYear list</title></head>
<body>
<?php
include ("../js_SelAll.js");
$TabName='AdmNumberSeqYear';
$CurrFile='AdmNumberSeqYearList.php';
$Frm='AdmNumberSeqYear';
$Fields=array('Id','Year','LastNo');
$enFields= array();
CheckRight1 ($mysqli, 'ExtProj.Admin');

 $BegPos = addslashes ($_REQUEST['BegPos']);
if ($BegPos==''){
$BegPos=0;
}

$ORD = addslashes ($_REQUEST['ORD']);
if ($ORD =='1') {
$ORD = 'Id,Year';
  }
  else {
    $ORD = 'Id,Year';
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
        $WHS.='('.$Fld." Like '%$Fltr%')"; 
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
         "FROM AdmNumberSeqYear ".
         " $WHS $ORDS LIMIT $BegPos, $LN";

  $queryCNT = "select COUNT(*) CNT ".
         "FROM AdmNumberSeqYear ".
         " $WHS ";

  $sql2 = $mysqli->query ($queryCNT)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);

  $CntLines=0;
  if ($dp = $sql2->fetch_assoc()) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($mysqli, 'AdmNumberSeqYear').' '.
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
  echo ('<hr><table><tr><td><form method=post action="AdmNumberSeqYearCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($mysqli, 'New')."'></form></td><td>" );
//--------------------------------------------------------------------------------
echo ('<form method=post action="AdmNumberSeqYearGroupOp.php">'.
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
    $PKValArr['Id']= $dp['Id'];
    $PKValArr['Year']= $dp['Year'];
  $PKRes=base64_encode( json_encode($PKValArr));
  
  echo ("<td><input type=checkbox ID='Chk_$Cnt' Name=Chk[$Cnt] value='$PKRes'></td>");
  

  $Fld='Id';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Year';
  echo("<td><a href='AdmNumberSeqYearCard.php?Id={$dp['Id']}&Year={$dp['Year']}'>{$dp[$Fld]}</a></td>");
  

  $Fld='LastNo';
  echo('<td>'.$dp[$Fld]."</td>");
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
       '</tr></table>');

?>
</body>
</html>
