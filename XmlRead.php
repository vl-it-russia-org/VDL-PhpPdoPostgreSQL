<?php
function ReadValsArr(&$Data, &$BegPos, &$Arr) {
  $LastPos = mb_strpos ( $Data, '>', $BegPos);
  if ($LastPos !== false) {
    $Str = mb_substr($Data, $BegPos, $LastPos-$BegPos);
    $SArr = explode(' ', $Str);
    
    $Cnt=count($SArr); 
    foreach ($SArr as $Indx=>$L) {
      //echo ("<hr>");
      //echo ("$Indx: $L");
      $Pos= mb_strpos($L, '=');
      if ($Pos!==false) {
        $VName=mb_substr($L, 0, $Pos);
        $Pos++;
        $Value=mb_substr($L, $Pos);

        if (mb_substr($Value, -1)!='"') {
          $iii = $Indx+1;
          while ($iii<$Cnt) {
            $Value.=' '.$SArr[$iii];
            if (mb_substr($Value, -1)=='"') {
              $iii= $Cnt+1;
            }
            else {
              $iii++;
            }  
          }
        };
        $Arr[$VName]=$Value; 
      }


    }
    $BegPos= $LastPos+1;
  }
}
//===========================================================
function GetXmlLine(&$Data, $ValName, &$LastPos) {
  $Start="<$ValName ";
  $End="/>";

  $Len= mb_strlen($Start);
  $Res='';
  
  $Pos = mb_strpos ($Data, $Start, $LastPos);
  if ($Pos!== false) {
    $Pos2 = mb_strpos ($Data, $End, $Pos+$Len);
    if ($Pos2!== false) {
      
      $Res= mb_substr ($Data, $Pos+$Len, $Pos2-$Pos-$Len );
      //echo ("\r\n<br>|Res: $Res|\r\n<br>");
      $LastPos=$Pos2+2;
    }
  }
  
  return $Res;  
}
//==============================================================
function GetXMLVal ( &$Data, $ValName, &$LastPos) {
  $Start="<$ValName>";
  $End="</$ValName>";

  $Len= mb_strlen($Start);
  $Res='';
  
  $Pos = mb_strpos ($Data, $Start, $LastPos);
  if ($Pos!== false) {
    $Pos2 = mb_strpos ($Data, $End, $Pos+$Len);
    if ($Pos2!== false) {
      
      $Res= mb_substr ($Data, $Pos+$Len, $Pos2-$Pos-$Len );
      //echo ("<br>|$Res|\r\n");
      $LastPos=$Pos2+$Len+1;
    }
  }
  else {
    $Start="<$ValName ";
    $Pos = mb_strpos ($Data, $Start, $LastPos);
    if ($Pos!== false) {
      $Pos2 = mb_strpos ($Data, $End, $Pos+$Len);
      if ($Pos2!== false) {
        
        $Res= mb_substr ($Data, $Pos+$Len, $Pos2-$Pos-$Len );
        //echo ("<br>|$Res|\r\n");
        $LastPos=$Pos2+$Len+1;
      }
    }
  
  
  }
  
  return $Res;  
}
//==============================================================
function XMLRead ( &$Data, &$ArrRet ) {
  $Len = mb_strlen ($Data);
  $StartPos=0;

  while ($StartPos<$Len) {
    $DivWord= 'EnumVal';
    $DivLen=strlen($DivWord)+2;
    $Pos = mb_strpos ($Data, "<$DivWord>", $StartPos);
    if ($Pos!== false) {
      $Pos2 = mb_strpos ($Data, "</$DivWord>", $Pos+$DivLen);
      if ($Pos2!== false) {
        $NewTxt= mb_substr ($Data, $Pos+$DivLen, $Pos2-$Pos-$DivLen );
        //echo ("<br>|$NewTxt|\r\n");
          
        $StartPos=$Pos2+$DivLen;
        $Arr=array();
      
        $Fld='EnumValue';
        $Arr[$Fld]=GetXMLVal ($NewTxt, $Fld);
        $Fld='Lang';
        $Arr[$Fld]=GetXMLVal ($NewTxt, $Fld);
        $Fld='Descr';
        $Arr[$Fld]=GetXMLVal ($NewTxt, $Fld);
        $ArrRet[]=$Arr;
      }
      else {
        die ("<br> Error: $DivWord not closed\r\n<br>$Data");
      }
    }
    else {
      $StartPos=$Len;
    }
  }
}
//--------------------------------------------------------------
?>