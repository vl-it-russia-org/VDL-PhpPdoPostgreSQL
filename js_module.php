<?php
//==========================================================================================

function FldOrgEdit1(&$mysqli, $FldName, $FldVal, $Required='') {
  echo ("<td align=right><b>".GetStr($mysqli, $FldName).":</b></td>".
          "<td><input type=text size=50 id='$FldName' name='$FldName' value='$FldVal' $Required>".
          "<button onclick=\"return SelectFld('$FldName');\">...</button></td>");
};

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
function ScriptSelectionTabs2 (&$mysqli, $TabName, $FrmName, $Caption) {
// Скрипт для выбора организации
echo ("<script>
 function Select{$TabName}Fld2(ElId, Par2 ) {
   El1 = document.getElementById(Par2);
   if (El1==null) {
     alert(Par2+' is not ok');
   }
   Par2Val = document.getElementById(Par2).value;
   SubVal = document.getElementById(ElId).value;
   a=window.open('{$FrmName}?ElId='+ElId+'&SubStr='+SubVal+'&Par2='+Par2Val, '".
     GetStr($mysqli, 'Select').' '.
     GetStr($mysqli, $Caption)."','width=900,height=520,resizable=yes,scrollbars=yes,status=yes');
   return false;
}
</script>");
};



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