<?php
//==========================================================================================
function ScriptSelectionTabs ($TabName, $FrmName, $Caption, $Param2='') {
// Скрипт для выбора организации
echo ("<script>
 function Select{$TabName}Fld( ElId, EldId2, EldId3, EldId4 ) {
   SubVal = document.getElementById(ElId).value;
   S1='&SubStr='+SubVal;
   if (EldId2 !== undefined) {
     SubVal2 = document.getElementById(EldId2).value;
     S1+= '&SelId2='+SubVal2;
   }
   if (EldId3 !== undefined) {
     SubVal3 = document.getElementById(EldId3).value;
     S1+= '&SelId3='+SubVal3;
   }
   if (EldId4 !== undefined) {
     SubVal4 = document.getElementById(EldId4).value;
     S1+= '&SelId4='+SubVal4;
   }


   a=window.open('{$FrmName}?{$Param2}ElId='+ElId+S1, 'Select',
               'width=900,height=520,resizable=yes,scrollbars=yes,status=yes');
   return false;
}
</script>");
};
//==========================================================================================


//==========================================================================================
function GetTableName (&$db, $TabStr, &$BegPos) {
  $Tab2='';
  $i=strpos($TabStr, '[T:', $BegPos);
  if ($i!== false) {
    $end=strpos($TabStr, ']', $i);
    if ($end!==false) {
     $Tab2= substr($TabStr, $i+3, $end-$i-3);
     $BegPos=$end+1;
     //echo ("<br> Tab2: $Tab2 ");
    }
  }
  return $Tab2;
}
//==========================================================================================
function GetFieldName (&$db, $TabStr, &$BegPos) {
  $Tab2='';
  $i=strpos($TabStr, '[F:', $BegPos);
  if ($i!== false) {
    $end=strpos($TabStr, ']', $i+1);
    if ($end!==false) {
      $Tab2= substr($TabStr, $i+3, $end-$i-3);
      $BegPos=$end+1;
      //echo ("<br> Tab2: $TabStr $Tab2 I:$i End:$end");
    }
  }
  return $Tab2;
}
//==========================================================================================
 
?>