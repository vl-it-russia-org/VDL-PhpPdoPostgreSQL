<?php
session_start();

include ("../setup/common_pg.php");
include ("../AnSum/AnSum.php");

BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

// 1030.05.30
// Учет на месяц открывается выполнением функции 
// OpenNewMonth.php?Year=2017&Month=11, При этом копируются остатки из 
// предыдущего месяца, и создаются нулевые обороты.

$Year=$_REQUEST['Year']+0;

if ($Year==''){ die ("<br> Error:  Empty Year");}
if ($Year<'2020'){ die ("<br> Error:  Year 2020 and more");}
if ($Year>'2030'){ die ("<br> Error:  Year 2030 and less");}

$Mon=$_REQUEST['Month'];
if ($Mon==''){ die ("<br> Error:  Empty Month");}

$BegDate = "$Year-$Mon-01";
$PredBegDate = '0000-00-00';


// Находим нужный период.
// Periods
// Id, PeriodType, BegDate, EndDate, 
// Description
$query = "select * from Periods ". 
        "where (PeriodType = 15)and(BegDate='$BegDate')"; 

$sql2 = $pdo->query ($query) 
          or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
if ($dp2 = $sql2->fetch_assoc()) {
  $PeriodId= $dp2['Id'];
  
  $BegDate = $dp2['BegDate'];
  $EndDate = $dp2['EndDate'];


  $PredBegDate = '0000-00-00';


  $PredPeriod=0;

  $query = "select * from Periods ". 
           "where (PeriodType = 15)and(BegDate<'$BegDate') order by BegDate desc limit 0, 1"; 

  $sql21 = $pdo->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);

  if ($dp21 = $sql21->fetch_assoc()) {
    $PredPeriod=$dp21['Id'];
    echo ("<br> Pred period: $PredPeriod: {$dp21['BegDate']} -- {$dp21['EndDate']} "); 
    $PredBegDate = $dp21['BegDate'];
  }
  else {
    // New Accountig started!
    echo ("<br> Not found previous month, start new accounting");
  }

  echo ("<br> Accounts: ");  
  // AccountChart
  // PlanNo, IsBalance, AccountNo, AccId, 
  // AccName, HeadAcc, HaveChild
  
  $query = "select * from AccountChart ". 
           "where (HaveChild = 0) order by PlanNo, IsBalance, AccountNo"; 

  $sql21 = $pdo->query ($query) 
            or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
  while ($dp21 = $sql21->fetch_assoc()) {
    $PlanNo= $dp21['PlanNo'];
    $AccNo = $dp21['AccountNo']; 
    $AccId = $dp21['AccId']; 
    $IsBalance = $dp21['IsBalance']; 

    //==============================================================================
    // Добавляем остатки
    //==============================================================================
    
    $DtSum=0;
    $CtSum=0;
    
    if ($PredPeriod!=0) {

      // FinAccRems
      // OpDate, PlanNo, AccountNo, IsBalance, 
      // AmountDt, AmountCt
      $query = "select * from FinAccRems ". 
               "where (OpDate = '$PredBegDate') and ".
               "(PlanNo='$PlanNo')and(AccountNo='$AccNo')"; 

      $sql25 = $pdo->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
      if ($dp25 = $sql25->fetch_assoc()) {
        $DtSum=$dp25['AmountDt'];
        $CtSum=$dp25['AmountCt'];
      }
    }

    // FinAccRems
    // OpDate, PlanNo, AccountNo, IsBalance, 
    // AmountDt, AmountCt
    
    $query = "select * from FinAccRems ". 
             "where (OpDate='$BegDate') and (PlanNo='$PlanNo') and (AccountNo = '$AccNo') "; 

    $sql23 = $pdo->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
    if ($dp23 = $sql23->fetch_assoc()) {
      echo ( "<br> -- Error: $AccNo Rems $BegDate already exists"); 
    }
    else {
      $query = "insert into FinAccRems(OpDate,PlanNo,AccountNo,IsBalance,AmountDt,AmountCt)". 
               "values('$BegDate','$PlanNo','$AccNo','$IsBalance', '$DtSum', '$CtSum')"; 

      $sql27 = $pdo->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
    }
    
    // Добавить копирование АНАЛИТИЧЕСКИХ Остатков!
    // AccChartAnalitic
    // AccId, AnaliticName, AnaliticBalField, Description, 
    // Ord1
    $query = "select * from AccChartAnalitic ". 
             "where (AccId = $AccId) order by Ord1"; 

    $sql23 = $pdo->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
    while ($dp23 = $sql23->fetch_assoc()) {
      $AnaliticName= $dp23['AnaliticName'];
      $PredSum=0;
      // Предыдущее значение аналитического остатка находим
      // FinAccRemsAnalitic
      // OpDate, AccId, AnaliticName,AnId 
      $query = "select AnId from FinAccRemsAnalitic ". 
               "where (OpDate='$PredBegDate') and (AccId=$AccId) and ".
               "(AnaliticName='$AnaliticName')"; 

      $sql26 = $pdo->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
      if ($dp26 = $sql26->fetch_assoc()) {
        $PredSum=$dp26['AnId'];
      }

      $query = "select AnId from FinAccRemsAnalitic ". 
               "where (OpDate='$BegDate') and (AccId=$AccId) and ".
               "(AnaliticName='$AnaliticName')"; 

      $sql26 = $pdo->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);


      if ($dp26 = $sql26->fetch_assoc()) {
        echo ("<br> Error: --- $AccNo analitic $AnaliticName Rems $BegDate already exists<br>");
      }
      else {

        $AnSum = AnaliticSum($pdo, $AnaliticName, -1, $PredSum, 1);
        $query = "insert into FinAccRemsAnalitic (OpDate, AccId, AnaliticName,AnId) ". 
                 "values ('$BegDate', $AccId, '$AnaliticName', $AnSum)"; 

        $sql29 = $pdo->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
      }
    }



    // Создаем обороты. Обороты Нулевые должны быть
    
    // FinAccTurns
    // PeriodId, AccNo, PlanNo, AmountDt, 
    // AmountCt
    $query = "select * from FinAccTurns ". 
             "where (PeriodId='$PeriodId') and ".
             "(AccNo='$AccNo') and (PlanNo ='$PlanNo')"; 

    $sql23 = $pdo->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
    if ($dp23 = $sql23->fetch_assoc()) {
      echo ("<br> -- Error: for period $PeriodId turn aleady exists for $AccNo ");
    }
    else {
      // FinAccTurns
      // PeriodId, AccNo, PlanNo, AmountDt, 
      // AmountCt
      $query = "insert into FinAccTurns (PeriodId,AccNo,PlanNo,AmountDt,AmountCt) ". 
               "values ('$PeriodId','$AccNo','$PlanNo', 0, 0)"; 


      $sql27 = $pdo->query ($query) 
                 or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
    }

    // Добавить аналитические обороты!
  }


}
else {
  echo ("<br> Error: Period $BegDate is not found, please start new Year if need it ");   
}






$AccId=addslashes($_REQUEST['AccId']);
if ($AccId==''){ die ("<br> Error:  Empty AccId");}
$AnaliticId=addslashes($_REQUEST['AnaliticId']);
if ($AnaliticId==''){ 
    if ($New==1) {  
      $query = "select MAX(AnaliticId) MX FROM AccChartAnalitic ".
               "WHERE (AccId='$AccId')";
      $sql2 = $pdo->query ($query)
                     or die("Invalid query:<br>$query<br>" . $pdo->error);
      $MX=0;
      if ($dp = $sql2->fetch_assoc()) {
        $MX=$dp['MX'];
      }
      $MX++;
      $_REQUEST['AnaliticId']=$MX;
      $AnaliticId=$MX;
    }
    else { die ("<br> Error:  Empty AnaliticId");}
}


  //---------------------------- Для автонумерации ---------------
  //include ("NumSeq.php");
  //if($_REQUEST['DocNo']=='') {
  //  $D=$_REQUEST['OpDate'];
  //  if ($D=='') {
  //    $_REQUEST['OpDate']=date('Y-m-d');
  //    $D=$_REQUEST['OpDate'];
  //  }
  //  $_REQUEST['DocNo'] = GetNextNo ( $pdo, 'BankOp', $D);
  //}


  $dp=array();
  $query = "select * FROM AccChartAnalitic ".
           "WHERE (AccId='$AccId') AND (AnaliticId='$AnaliticId')";
  $sql2 = $pdo->query ($query)
                 or die("Invalid query:<br>$query<br>" . $pdo->error);
  
  if ($dp = $sql2->fetch_assoc()) {
    if ($New==1){
      echo ("<br>");
      print_r($dp);
      die ("<br> Error: Already have record ");
    }

    $Editable=1;
    if (!$Editable) {
      die ("<br> Error: Not Editable record ");
    }      
  }
  
  if ($New==1){
    $q='insert into AccChartAnalitic(';
    $S1='';
    $S2='';
    $Div='';

    foreach ($FldNames as $F) {
      $V=addslashes ($_REQUEST[$F]);
      $S1.=$Div.$F;
      $S2.="$Div'$V'";
      $Div=', ';
    }
    $q.=$S1.') values ('.$S2.')';
    
    $sql2 = $pdo->query ($q)
                 or die("Invalid query:<br>$q<br>" . $pdo->error);
}
  else {
    $q='update AccChartAnalitic set ';
    $S1='';
    $Div='';

    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      $V=addslashes ($_REQUEST[$F]);
      if ( $V1 != $dp[$F]) {
        $S1.=$Div.$F."='$V'";
        $Div=', ';
      }
    }
    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

$V=addslashes ($_REQUEST['OldAccId']);
      $S1.="(AccId='$V')";
  
$V=addslashes ($_REQUEST['OldAnaliticId']);
      $S1.=" and (AnaliticId='$V')";
  
      $q.= $S1;
      $sql2 = $pdo->query ($q)
                 or die("Invalid query:<br>$q<br>" . $pdo->error);
  
    }
  }
$LNK='';

  $V=$_REQUEST['AccId'];
  $LNK.="AccId=$V";
  
  $V=$_REQUEST['AnaliticId'];
  $LNK.="&AnaliticId=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=AccChartAnaliticCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>