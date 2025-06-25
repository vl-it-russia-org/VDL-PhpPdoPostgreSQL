<?php
function FilterStr ( $Fld, $Txt ) {
  $Res='';
  $DD=$Txt;       
  $IY=mb_strpos($DD, '..');
  //echo ("<br>IY:$IY ");
  $BegVal='';
  $EndVal='';
      
  $WHADD='';
  if ($IY!== false) {
    if ($IY>0) {
      $BegVal=trim(substr($DD, 0, $IY));
      if ($BegVal != '') {
        $BegVal=addslashes($BegVal);
        $WHADD="$Fld>='$BegVal'";  
      }
    }
    
    $EndVal=trim(substr($DD, $IY+2));
    if ($EndVal!= '') {
      $EndVal=addslashes ($EndVal);
      if (WHADD!='') {
        $WHADD = " ( $WHADD ) and ( $Fld<='$EndVal') ";
      }
      else {
        $WHADD.= "$Fld<='$EndVal'";
      }
    }
  }
  else {
    $WHADD= " $Fld like '%".addslashes($DD)."%'";   
  }
    
    
  if ($WHADD!= '') {    
    $Res=" and ($WHADD)";
  }
  
  return $Res;
}
?>