<?php
function AnSum_AnItems(&$mysqli, $AnIdBeg, $AnIdFrom, $Koef, $ViewName){
  $MaxLineNo=0;
  if ($AnIdBeg==-1) {
    $query = "select max(AnId) as MAnId from AnItems ";
    $sql2 = $mysqli->query ($query) 
                     or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    if ($dp2 = $sql2->fetch_assoc()) {
      $AnIdBeg=$dp2['MAnId'];
    }
    else {
      $AnIdBeg=0;
    }
    $AnIdBeg++;
    $query = "insert into AnItems(AnId, LineNo) values ($AnIdBeg, -500) ";
    $sql5 = $mysqli->query ($query) 
                     or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
  }
  else {
    $query = "select max(LineNo) as MLineNo from AnItems".
             "where (AnId='$AnIdBeg') and (LineNo>0)";
    $sql2 = $mysqli->query ($query) 
                     or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    if ($dp2 = $sql2->fetch_assoc()) {
      $MaxLineNo=$dp2['MLineNo'];
    }
  }


  // ------------------------------------- Standard -----------------
  if ($ViewName=='Standard'){
    $query = "select * from AnItems ".
             "where (AnId = '$AnIdFrom') and (LineNo>0) order by AnId, LineNo ";
    $sql2 = $mysqli->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    while ($dp2 = $sql2->fetch_assoc()) {
      $LineNo=0;
      $LocationNo=$dp2['LocationNo'];
      $ItemNo=$dp2['ItemNo'];
      $LotNo=$dp2['LotNo'];
      $query = "select * from AnItems ".
               "where (AnId = '$AnIdBeg') and( LocationNo='$LocationNo')and( ItemNo='$ItemNo')and( LotNo='$LotNo')";
      $sql21 = $mysqli->query ($query)
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
      if ($dp21 = $sql21->fetch_assoc()) {
        // Have already
        $LineNo = $dp21['LineNo'];

        $query = "update AnItems set  Qty=Qty+({$dp2['Qty']}*$Koef),  CostLCY=CostLCY+({$dp2['CostLCY']}*$Koef),  CostOp=CostOp+({$dp2['CostOp']}*$Koef)";
                 "where (AnId = '$AnIdBeg') and (LineNo='$LineNo') ";
        $sql7 = $mysqli->query ($query)
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
      }
      else {
        // Insert new
        $MaxLineNo+=10;
        $LineNo=$MaxLineNo;
        $query = "insert into AnItems (AnId, LineNo, LocationNo, ItemNo, LotNo, Qty, CostLCY, CostOp) ".
                 "values ('$AnIdBeg', '$LineNo', '{$dp2['LocationNo']}', '{$dp2['ItemNo']}', '{$dp2['LotNo']}', {$dp2['Qty']}*$Koef, {$dp2['CostLCY']}*$Koef, {$dp2['CostOp']}*$Koef) ";
        $sql7 = $mysqli->query ($query)
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
      }
    }

    // Cleanup Zero lines
    $query = "delete from AnItems ".
             "where (AnId = '$AnIdBeg') and (LineNo>0)  and(Qty=0) and(CostLCY=0) and(CostOp=0) ";
    $sql25 = $mysqli->query ($query)
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);

    // set total 
    $query = "select  SUM(Qty) MQty,  SUM(CostLCY) MCostLCY,  SUM(CostOp) MCostOp from AnItems ".
             "where (AnId = '$AnIdBeg') and (LineNo>0)";
    $sql2 = $mysqli->query ($query)
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    MQty=0;
    MCostLCY=0;
    MCostOp=0;
    if ($dp2 = $sql2->fetch_assoc()) {
      MQty=$dp2['MQty'];
      MCostLCY=$dp2['MCostLCY'];
      MCostOp=$dp2['MCostOp'];
    }

    $query = "select * from AnItems ".
             "where (AnId = '$AnIdBeg') and (LineNo=-500)";
    $sql2 = $mysqli->query ($query)
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    if ($dp2 = $sql2->fetch_assoc()) {
      $query = "update AnItems set Qty=$MQty, CostLCY=$MCostLCY, CostOp=$MCostOp ".
               "where (AnId = '$AnIdBeg') and (LineNo=-500)";
      $sql5 = $mysqli->query ($query)
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    }
    else {
      $query = "insert into AnItems (AnId,LineNo, Qty, CostLCY, CostOp) ".
               "values ($AnIdBeg, -500, $MQty, $MCostLCY, $MCostOp)";
      $sql5 = $mysqli->query ($query)
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $mysqli->error);
    }
  }

  //-------------------------------------  

  return $AnIdBeg;
}
?>