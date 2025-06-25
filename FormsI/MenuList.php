<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Menu list</title></head>
<body>
<?php
include ("../js_SelAll.js");

$TabName='Menu';
$CurrFile='MenuList.php';
$Frm='Menu';
$Fields=array('Id','MenuType', 'MenuCode', 'MenuName','Description','Link',
              'NewWindow','ColumnNo', 'ParentId','Ord');

$enFields= array('MenuType'=>'MenuType');

CheckRight1 ($pdo, 'Admin');

$BegPos = $_REQUEST['BegPos'];
if ($BegPos==''){
  $BegPos=0;
}

$PdoArr = array();


try {
$ORD = $_REQUEST['ORD'];
if ($ORD =='1') {
$ORD = '"Id"';
  }
  else {
    $ORD = '"Id"';
  }

  $ORDS = ' order by  '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  }
  
  $WHS = '';
  $FullRef='?ORD='.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST['Fltr_'.$Fld];
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      if ($enFields[$Fld]!='') {
        $WHS.='("'.$Fld."\" = :$Fld)"; 
        $PdoArr[$Fld]= $Fltr;
      }
      else {
        $WHS.='("'.$Fld."\" Like :$Fld )"; 
        $PdoArr[$Fld]= "%$Fltr%";
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

  $query = "select * FROM \"Menu\" ".
           "$WHS $ORDS LIMIT $LN offset $BegPos";

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"Menu\" ".
              "$WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);

  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($pdo, 'Menu').' '.
        GetStr($pdo, 'List').
        '</b> '.$CntLines.' total lines Page <b>'.
        $CurrPage.'</b> from '. $LastPage) ;
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

 
  echo ('<form method=get action="'.$CurrFile.'"><table><tr>');
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
      echo("<td><input type=text size=10 name='Fltr_$Fld' value='".
        $_REQUEST['Fltr_'.$Fld]."'></td>");
    }
  }
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');
  echo ('<hr><table><tr><td><form method=post action="MenuCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'></form></td><td>" );
//--------------------------------------------------------------------------------
echo ('<form method=post action="MenuGroupOp.php">'.
        "<input type=submit  Name=OpType Value='".GetStr($pdo, 'Delete')."' 
          onclick='return confirm(\"Delete selected?\");'></td></tr></table>" );
echo ('<table><tr class="header">');

echo("<th><input type=checkbox onclick='return SelAll();'></th>");

foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
}

echo("<th>".GetStr($pdo, 'Rights')."</th>");

echo("</tr>");

$n=0;
$Cnt=0;

while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
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
  $PKRes=base64_encode( json_encode($PKValArr));
  
  echo ("<td><input type=checkbox ID='Chk_$Cnt' Name=Chk[$Cnt] value='$PKRes'></td>");
  


  $Fld='Id';
  echo("<td><a href='MenuCard.php?Id={$dp['Id']}'>{$dp[$Fld]}</a></td>");
  $MenuId = $dp[$Fld]; 

  $Fld='MenuType';
  echo('<td>['.$dp[$Fld]."] ".GetEnum($pdo, $Fld,$dp[$Fld])."</td>");

  $Fld='MenuCode';
  echo("<td>{$dp[$Fld]}</td>");

  $Fld='MenuName';
  echo("<td>{$dp[$Fld]} <a href='TranslateFrm.php?Enum={$dp[$Fld]}' target=Transl>...</a></td>");
  

  $Fld='Description';
  echo("<td>{$dp[$Fld]} <a href='TranslateFrm.php?Enum={$dp[$Fld]}' target=Transl>...</a></td>");
  

  $Fld='Link';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='NewWindow';
  echo('<td>'.$dp[$Fld]."</td>");
  
  

  $Fld='ColumnNo';
  echo('<td>'.$dp[$Fld]."</td>");

  $Fld='ParentId';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Ord';
  echo('<td>'.$dp[$Fld]."</td>");
  //-------------------------------------------
  // Права
  // Rights2SetupVals.php?RightSel=Menu&Sub=83
  echo ("<td><a href='Rights2SetupVals.php?RightSel=Menu&Sub=$MenuId'>...</a> ".
        "</td>");

  echo("</tr>");
}
echo("</table>".
     "<input type=hidden ID=AllCnt value='$Cnt'>".
     "<input type=submit Name=OpType Value='".GetStr($pdo, 'Delete')."' 
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
