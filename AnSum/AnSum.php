<?php

include "AnSum_AnItem.php";

function AnaliticSum(&$mysqli, $AnaliticName, $SumRes, $AddSum, $Koef){
  if ($AnaliticName=='AnItem') {
    return AnSum_AnItem($mysqli, $SumRes, $AddSum, $Koef); 
  }
  else {
    die ("<br> Analitic sum $AnaliticName is not found"); 
  }



}

?>
