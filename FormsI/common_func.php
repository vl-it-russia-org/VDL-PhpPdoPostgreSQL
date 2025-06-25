<?php

function GetNumberSeq ($ParamTxt) {
  
  $i=strpos($ParamTxt, '[SerNo=');
  
  echo ("<td></td><td>$Val</td>");
  $CurrVal='';
  if ($i!==false) {
    $last=strpos ($ParamTxt, ']', $i);  
    if ($last !== false) {
      $CurrVal= trim (substr($ParamTxt, $i+7, $last -$i-7));

    }
  
  }
  return $CurrVal;
}

//=================================================================

function GetEditableFld($ParamTxt) {
  
  $i=strpos($ParamTxt, '[Editable=');
  
  echo ("<td></td><td>$Val</td>");
  $CurrVal='';
  if ($i!==false) {
    $last=strpos ($ParamTxt, ']', $i);  
    if ($last !== false) {
      $CurrVal= trim (substr($ParamTxt, $i+10, $last -$i-10));

    }
  
  }
  return $CurrVal;
}


//=================================================================

function GetMasterTabName($ParamTxt) {
  
  $i=strpos($ParamTxt, '[MasterTab=');
  
  echo ("<td></td><td>$Val</td>");
  $CurrVal='';
  if ($i!==false) {
    $last=strpos ($ParamTxt, ']', $i);  
    if ($last !== false) {
      $CurrVal= trim (substr($ParamTxt, $i+11, $last -$i-11));

    }
  
  }
  return $CurrVal;
}



//=================================================================



/*
function EnumMultiSelect (&$pdo, $EnumName, $SelName, $StrVals) {
  // StrVals -- какие должны быть выделены позиции строка: 0,3,5,6
  // Передает на сервер : 1)  _MaxQty -- сколько всего шт выбора
  //                      2) _Sel_3] => 20  -- какие опции выбрал пользователь
  // Array ( [TestUOM_MaxQty] => 6 [TestUOM_Sel_3] => 20 [TestUOM_Sel_5] => 40 
  $Res='';
  $VArr = array ();
  $i = strpos ($StrVals, ',');
  $Div=',';
  if ($i===false) {
    $Div=';';
  }

  $Arr=explode ($Div, $StrVals);
  foreach($Arr as $Indx=>$V) {
    $Tr=trim($V);
    if ($Tr!='') {
      $VArr[$Tr]=1;
    }
  }

  $Lang=$_SESSION['LANG'];
  if ($Lang=='') {
    $Lang='RU';
  }
  
  //EnumValues
  //EnumName, EnumVal, Lang, EnumDescription
  $query = "select count(*) CNT from EnumValues ".
           " WHERE (EnumName='$EnumName') and (Lang='$Lang')";

  $sql2 = $pdo->query ($query) 
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);

  $Cnt=0;
  if ($dp2 = $sql2->fetch_assoc()) {
    $Cnt=$dp2['CNT'];
  }

  if ($Cnt==0) {
    die ("Error: No values for EnumName='$EnumName' and Lang='$Lang' ");
  }


  //EnumValues
  //EnumName, EnumVal, Lang, EnumDescription
  $query = "select EnumVal, EnumDescription from EnumValues ".
           " WHERE (EnumName='$EnumName') and (Lang='$Lang') order by EnumVal ";

  $sql2 = $pdo->query ($query) 
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);

  echo("<input type=hidden Name={$SelName}_MaxQty ID={$SelName}_MaxQty value=$Cnt>".
        "<table><tr class=header><td><input type=checkbox Name={$SelName}_SelAll ".
         "ID={$SelName}_SelAll onclick='MSChangeVal(\"$SelName\");'></td>".
        "<td align=center><input type=button onclick='return MSClrVal(\"$SelName\");' value='".
           GetStr($pdo, 'Clear')."'></td>");


  $n=0;
  $i=0;
  while ($dp2 = $sql2->fetch_assoc()) {
    $n=NewLine($n);
    $i++;
    $Ch='';
    if ( !empty ( $VArr[$dp2['EnumVal']]) ) {
      $Ch=' checked';
    }
    $Clr=addslashes ($dp2['EnumDescription']);
    echo ("<td><input type=checkbox Name={$SelName}_Sel_$i$Ch Id={$SelName}_SEL_$i value='{$dp2['EnumVal']}'></td>".
          "<td>{$dp2['EnumDescription']}</td>");  
  }
  echo("</tr></table>");

  
}
*/

?>