<?php
function SetParam (&$mysqli, $ParamType, $ParamNo, $ID, $ParamVal) {
  $CurrVal='';
  //echo ("<br>ParamType:$ParamType, ParamNo: $ParamNo, ID: $ID, ParamVal: $ParamVal");
  
  $query = "select Value from ParamVal where ".
           "ParamType='$ParamType' and ParamNo='".$ParamNo."' and ID='$ID'";
  
  $sql3=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);  
  if ($dp3 = $sql3->fetch_object()) {
    $CurrVal=$dp3->Value;    
  }
  else {
    MakeAdminRec ($mysqli, $_SESSION['login'], 'USR', $UserId, 
            'UPDPARAM', "Insert param $ParamType, $ParamNo to ".
              $ParamVal);
    
    $query = "insert into ParamVal ".
            "( ParamType, ParamNo, ID, Value) values ".
            "('$ParamType','".$ParamNo."','$ID', '$ParamVal')";
  
    
    $sql3=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);    
    echo ("<br>Insert param $ParamNo");

  };
  
  if ( $CurrVal != $ParamVal) {
    MakeAdminRec ($mysqli, $_SESSION['login'], 'USR', $UserId, 
            'UPDPARAM', "Update param $ParamType, $ParamNo to ".
              $ParamVal);
    
    $query = "update ParamVal set Value='$ParamVal' where ".
             "ParamType='$ParamType' and ParamNo='".$ParamNo."' and ID='$ID'";
  
    
    $sql3=$mysqli->query($query)
                  or die ("Invalid query:<br>$query<br>Line:".__LINE__." ".$mysqli->error);    
    echo ("<br>Update param $ParamNo");
  }
}
?>   
