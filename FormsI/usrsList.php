<?php
session_start();
include ("../setup/common_pg.php");
include ("../js_SelAll.js");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>usrs list</title></head>
<body>
<?php

$TabName='usrs';
$CurrFile='usrsList.php';

$Frm='usrs';

$Fields=array('usr_id','description','Company','admin','email','phone',
              'Blocked');

$enFields= array();
CheckRight1 ($pdo, 'Admin');

$BegPos = 0;
if (!empty ($_REQUEST['BegPos'])) {
  $BegPos = $_REQUEST['BegPos'] + 0;

};
if ($BegPos==''){
  $BegPos=0;
}

$ORD = $_REQUEST['ORD'];

$MultiTxt="<input type=hidden Name=BegPos values='$BegPos'>";

$PdoArr = array();


if ($ORD =='1') {
$ORD = '"usr_id"';
  }
  else {
    $ORD = '"usr_id"';
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
        $PdoArr[$Fld]= $Fltr;
        
        $WHS.='("'.$Fld."\" = :$Fltr)"; 
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $PdoArr );
      }
      
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
      $MultiTxt.="<input type=hidden Name=Fltr_$Fld value='$Fltr'>";
    }
  }

$LN = $_SESSION['LPP'];
  if ($LN=='') {
    $LN=20;  
  };

  if ($WHS != '') {
    $WHS = ' where '.$WHS;
  };   


try {

  $query = "select * FROM \"usrs\" ".
           "$WHS $ORDS LIMIT $LN OFFSET $BegPos";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  //echo ("<br> Line ".__LINE__.": $query<br>");
  //            print_r($PdoArr);
  //            echo ("<br>");
  
  
  $queryCNT = "select COUNT(*) \"CNT\" FROM \"usrs\" ".$WHS;

  $STH2 = $pdo->prepare($queryCNT);
  $STH2->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STH2->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.GetStr($pdo, 'usrs').' '.
        GetStr($pdo, 'List').
        '</b> '.$CntLines.' total lines Page <b>'.
        $CurrPage.'</b> from '. $LastPage) ;
  
  
  
 
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
      echo("<td><input type=text length=30 size=20 name='Fltr_$Fld' value='".
        $_REQUEST['Fltr_'.$Fld]."'></td>");
    }
  }
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');
  echo ('<hr><br><table><tr><td><form method=post action="usrsCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'></form></td>".
        "<td><form method=post action=usrsMultiBlock.php><input type=submit value=Block></td>".
        "</tr></table>$MultiTxt" );
//--------------------------------------------------------------------------------

echo ('<table><tr class="header">');

echo("<th><input type=checkbox onclick='return SelAll();'></th>");

foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
}
echo("<th></th></tr>");

$n=0;
$Cnt=0;
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }
  
  $Cnt++;
  echo ("<tr".$classtype.">");



  $PKValArr=array();
  $PKValArr['usr_id']= $dp['usr_id'];
  $UsrId= $dp['usr_id'];
  $PKRes=base64_encode( json_encode($PKValArr));
  
  echo ("<td><input type=checkbox ID='Chk_$Cnt' Name=Chk[$Cnt] value='$PKRes'></td>");

  $Fld='usr_id';
  echo("<td><a href='usrsCard.php?usr_id={$dp['usr_id']}'>{$dp[$Fld]}</a></td>");
  $UsrId = $dp[$Fld];

  $Fld='description';
  echo('<td>'.$dp[$Fld]."</td>");
  
  $Fld='Company';
  echo('<td>'.$dp[$Fld]."</td>");

  $Fld='admin';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='email';
  echo('<td>'.$dp[$Fld].
       "<a href='Frm-usrs-OneMailChange.php?UsrId=$UsrId&NewDomain=kontaktor.ru' ".
       "title='Change mail to Kontaktor' target='Ch_$UsrId'>...</a>".
       "</td>");
  

  $Fld='phone';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Blocked';
  echo('<td>'.$dp[$Fld]."</td>");
  
  echo("<td><a href='user_setup.php?UserId=$UsrId'>Setup</a></td>");
  
  echo("</tr>");
}

echo("<tr><td colspan=7 align=right><input type=submit value=Block></td></tr></table>".
     "<input type=hidden Name=AllCnt ID=AllCnt value='$Cnt'></form>"
     );

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
      '<td><a href="usrsPrintXLS.php'.$FullRef.'">To XLS</a></td>'.
      '<td><a href="../SFMini/Frm_CopyUserFromSF.php">Copy from SF</a></td>'.
      '<td><a href="Frm-usrs-XlsUploadNewMail.php">Xls change mail</a></td>'.
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
