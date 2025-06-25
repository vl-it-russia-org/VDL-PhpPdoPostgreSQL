<?php

function GetCurrencyRate(&$mysqli, $CurrCode, $OpDate='') {
  $Res=1;
  
  if ($OpDate=='') {
    $OpDate= date ('Y-m-d');
  }
  
  // CurrencyExchRate
  // CurrencyCode, StartDate, Multy, Rate, FullRate
  $query = "select * FROM CurrencyExchRate ".
           "WHERE (CurrencyCode='$CurrCode')and(StartDate<='$OpDate') ".
           "order by CurrencyCode, StartDate desc LIMIT 0,1";
  
  //echo ("<br>$query<br>");


  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
  
  if($dp2 = $sql2->fetch_assoc()) {    
    if ($dp2['FullRate']==0) {
      if ($dp2['Multy']==1) {
        $Res= $dp2['Rate'];
      }
    }
    else {
      $Res= $dp2['FullRate'];
    }   
  }
  return $Res;
}
//=======================================================================
?>