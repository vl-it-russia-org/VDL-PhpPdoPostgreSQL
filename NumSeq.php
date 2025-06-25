<?php

function GetNextNo ( &$mysqli, $SeqName, $OpDate="") {
  $Id=addslashes ($SeqName);
    
  $query = "select * FROM AdmNumberSeq WHERE (Id='$Id')";
  
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);

  $Pattern='No';
  if ($dp = $sql2->fetch_assoc()) {
    $Pattern= $dp['Pattern'];
    $HaveYear=0;
    $Y='';
    if ($OpDate=='') {
      $Y=date('Y');
    }
    else {
      $Y=mb_substr($OpDate, 0, 4);
    }
    $YL=mb_substr($Y, 2,2);
    $LastNo=0;
    
    if ($dp['IsYearly']==1) {
      
      $query = "select * FROM AdmNumberSeqYear where (Id='$Id') and (Year='$Y') ";
      $sql1 = $mysqli->query ($query)
                      or die("Invalid query:<br>$query<br>" . $mysqli->error);
      
      if ($dp1 = $sql1->fetch_assoc()) {
        $LastNo = $dp1['LastNo'];
        $LastNo++;
        $query = "update AdmNumberSeqYear set LastNo='$LastNo' where (Id='$Id') and (Year='$Y')";
        
        $sql3 = $mysqli->query ($query)
                      or die("Invalid query:<br>$query<br>" . $mysqli->error);
      }
      else {
        $LastNo++;
        $query = "insert into AdmNumberSeqYear (Id,Year,LastNo) values ".
                 "('$Id','$Y','$LastNo')";
        $sql3 = $mysqli->query ($query)
                      or die("Invalid query:<br>$query<br>" . $mysqli->error);
      }    
    }
    else {
      $LastNo=$dp['LastNumber'];
      
      $LastNo++;
      
      $query = "update AdmNumberSeq set LastNumber='$LastNo' where (Id='$Id')";
      $sql3 = $mysqli->query ($query)
                      or die("Invalid query:<br>$query<br>" . $mysqli->error);
    }

    $i=mb_strpos($Pattern, '{YY}');
    if ($i!==false) {
      //$i=mb_substr($Pattern, "{YY}");
      $Pattern=str_replace("{YY}", $YL, $Pattern); 
    }

    $NewNum='';
    $i=mb_strpos($Pattern, '{0');
    if ($i!==false) {
      $j=mb_strpos($Pattern, '}', $i);
      $Sub1='{0';
      if ($j!==false) {
        $Sub1= mb_substr($Pattern, $i, $j-$i+1);
        $len = mb_strlen($Sub1)-2;
        $len2= mb_strlen($LastNo);
        $len-=$len2;
        for( $b=0;$b<$len;$b++){
          $NewNum.='0';
        }; 
      }
      $NewNum.=$LastNo;
      $Pattern=str_replace($Sub1, $NewNum, $Pattern); 
    }
  }
  else {
    die ("<br> Bad sequence $SeqName. Line:".__LINE__.' in file:'.__FILE__);
  }
  return $Pattern;
}
//================================================================================
// Сколько процентов НДС
function GetTaxProc(&$mysqli, $TaxRule, $LineType, $No, $OpDate) {
  $Res=0;
  if ($TaxRule!=0) {
    if ($LineType==0) { // Товар
      $Res= GetConst ($mysqli, 'VAT', $OpDate);
    
    }
    else {
      $Res= GetConst ($mysqli, 'VAT', $OpDate);
    }
  }
  return $Res;
}
//================================================================================
/*
function GetConst (&$mysqli, $ConstName, $OpDate) {
  $Res='';

  $query = "select ConstType from ComConst ".
           "where ConstName='$ConstName'";

  $sql2 = $mysqli->query ($query)
            or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if ($dp = $sql2->fetch_assoc()) {
    $F='';
    if ($dp['ConstType']==5) {
      $F='Value';
    }  
    if ($dp['ConstType']==10) {
      $F='ValueDate';
    }  
    if ($dp['ConstType']==15) {
      $F='ValueTxt';
    }  
    
    //ComConstValues
    //ConstName, OpDate, ValidTill, Value, ValueDate, ValueTxt
    $query = "select $F R from ComConstValues ".
             "where (ConstName='$ConstName') and (OpDate<='$OpDate') and ".
             " ( (ValidTill ='0000-00-00') OR (ValidTill>='$OpDate'))";

    //echo ("<br>$query<br>");
    
    $sql3 = $mysqli->query ($query)
                      or die("Invalid query:<br>$query<br>" . $mysqli->error);

    if ($dp1 = $sql3->fetch_assoc()) {
      //print_r ($dp1);
      
      $Res= $dp1['R'];
    }
  }

  return $Res;
}
*/
//-----------------------------------------------------------------------------------

?>