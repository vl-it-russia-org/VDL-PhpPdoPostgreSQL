<?php
//==========================================================================================
function GetRightSelection (&$db, $Name, $DefaultVal, $NoDef=0 ) {
  
  $Res="<select name=$Name>".
       "<option value=''></option>" ;
  
  $query = "select \"RightType\", \"RightDescription\" from \"Rights\" ".
           "order by \"RightType\"";
  
  
  try {
    $STH = $db->prepare($query);
    $STH->execute();
  
  

  while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
    
    $Code=$dp2['RightType'];
    
    if ($DefaultVal=='') {
      if ($NoDef==0) {
        $DefaultVal=$dp2['RightType'];
      }
    }
    
    $Sel='';
    if ($DefaultVal==$dp2['RightType']) 
      $Sel = ' selected ';
    $Res = $Res. "<option $Sel value='$Code'>$Code: ".$dp2['RightDescription'].'</option>';  
  }
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

  $Res.="</select>"; 
  return $Res;                         
};
//==========================================================================================
?>