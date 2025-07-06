<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='Location';
OutHtmlHeader ($TabName." Select");

$CurrFile='SelectLocation.php';
$Frm='Location';
$Fields=array('LocCode','Description');
$enFields= array();
CheckRight1 ($pdo, 'Admin');

 $BegPos = $_REQUEST['BegPos']+0;
if ($BegPos==''){
$BegPos=0;
}

$ORD = $_REQUEST['ORD'];
if ($ORD =='1') {
  $ORD = 'LocCode';
}
else {
  $ORD = 'LocCode';
}
$ORDS = ' order by  '; 
if ($ORD !='') {
  $ORDS = ' order by '.$ORD;
}
else {
  $ORDS = ' order by '.$ORD;
}

$PdoArr = array();
  
  $WHS = '';
  $FullRef='?ORD='.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST['Fltr_'.$Fld];
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      
      if ($enFields[$Fld]!='') {
        $PdoArr[$Fld]= $Fltr;
        $WHS.='("'.$Fld."\" = :$Fld)";
      }
      else {
        $WHS.= SetFilter2Fld ($Fld, $Fltr, $PdoArr );
      }
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
    }
  }

$ElId   = $_REQUEST['ElId'];
$SubStr = $_REQUEST['SubStr'];
$SelId = $_REQUEST['SelId'];
$SelId2 = $_REQUEST['SelId2'];
$SelId3 = $_REQUEST['SelId3'];
$SelId4 = $_REQUEST['SelId4'];
$Par2   = $_REQUEST['Par2'];
if ($SelId== '21') { 

  echo ("<script>".
    "function SetSelect( Val ) { 
       OW=window.opener;
     
       var elem1 = OW.document.getElementById('$ElId');
       if (elem1) { 
         elem1.value=Val;
       }

       window.close();
    }
    </script>");

}
  $LN = $_SESSION['LPP'];
  if ($LN=='') {
    $LN=20;  
  };

  if ($WHS != '') {
    $WHS = ' where '.$WHS;
  };   

  $query = "select * FROM \"Location\" ".
           "$WHS $ORDS ".AddLimitPos($BegPos, $LN);

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"Location\" ".
              "$WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($pdo, 'Location').' '.
        GetStr($pdo, 'List').
        '</b> '.$CntLines.' total lines Page <b>'.
        $CurrPage.'</b> from '. $LastPage) ;
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
   
  echo ('<form method=post action="'.$CurrFile.'"><table><tr>'.
        "<input type=hidden name='ElId' value='$ElId'>".
        "<input type=hidden name='SubStr' value='$SubStr'>".
        "<input type=hidden name='SelId' value='$SelId'>".
        "<input type=hidden name='SelId2' value='$SelId2'>".
        "<input type=hidden name='SelId3' value='$SelId3'>".
        "<input type=hidden name='SelId4' value='$SelId4'>".
        "<input type=hidden name='Par2' value='$Par2'>");
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==3){
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
  echo ("<td colspan=2><input type=text length=10 name=SubStr value='$SubStr'></td>");
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');
  //echo ('<hr><br><form method=post action="LocationCard.php">'.
    //    '<input type=hidden Name=New VALUE=1>'.
    //    "<input type=submit Value='".GetStr($pdo, 'New')."'></form>" );
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

  if ($SelId=='21'){
    $Res="\"".addslashes($dp['LocCode'])."\"";
}

  echo ("<tr$classtype><td><input type=button value='".GetStr($pdo, 'Select').
       "' onclick='return SetSelect($Res);'></td>");

  foreach ( $Fields as $Fld) {
    if ($Fld=='LocCode') {
      echo("<td align=left><a href='LocationCard.php?LocCode={$dp['LocCode']}'>{$dp[$Fld]}</a></td>");
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
$FullRef.="&SelId=$SelId&ElId=$ElId&SubStr=$SubStr&Par2=$Par2&SelId2=$SelId2&SelId3=$SelId3&SelId4=$SelId4";
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
