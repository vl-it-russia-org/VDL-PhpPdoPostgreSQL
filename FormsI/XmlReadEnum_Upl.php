<?php
session_start();

include ("../setup/common_pg.php");

BeginProc();
CheckRight1 ($pdo, 'Admin');
function GetXMLVal ( &$Data, $ValName) {
  $Start="<$ValName>";
  $End="</$ValName>";

  $Len= mb_strlen($Start);
  $Res='';
  
  $Pos = mb_strpos ($Data, $Start);
  if ($Pos!== false) {
    $Pos2 = mb_strpos ($Data, $End, $Pos+$Len);
    if ($Pos2!== false) {
      
      $Res= mb_substr ($Data, $Pos+$Len, $Pos2-$Pos-$Len );
      //echo ("<br>|$Res|\r\n");
    }
  }
  return $Res;  
}
//==============================================================
function XMLRead ( &$Data, &$ArrRet, $EnumName ) {
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
        $Arr['EnumName']=$EnumName;

        $Fld='EnumValue';
        $Arr['EnumVal']=GetXMLVal ($NewTxt, $Fld);
        $Fld='Lang';
        $Arr[$Fld]=GetXMLVal ($NewTxt, $Fld);
        $Fld='Descr';
        $Arr['EnumDescription']=GetXMLVal ($NewTxt, $Fld);
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
//==============================================================


?>
<html>
<head><title>Uploaded Account SF file</title></head>
<body>
<?php

//echo ("<br>");
//print_r( $_FILES);
//echo ("<br><br>");
$dir=$TmpFilesDir;

$size = $_FILES['userfile']['size'];
$name_temp = $_FILES['userfile']['tmp_name'];
$type = $_FILES['userfile']['type'];

$Firm='EnumXML';

$real_name = "$TmpFilesDir/$Firm.xml";

$sizeStr='';
if ($size> (1024*1024) ) {
  $sizeStr = round($size/1024/1024, 1).'M';
}else{
  if ($size>1024) {
    $sizeStr = round ($size/1024, 1).'K';
  }
  else
    $sizeStr = $size.'b';
};

echo ("File: $real_name $sizeStr<br>");


if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded $Firm<br>");

  //MakeAdminRec ($pdo, $_SESSION['login'], $Firm, $sizeStr, 
  //                      $Firm, "$Firm file");
  
  //echo("<br><a href='XmlReadEnum.php'>Start upload</a>");


set_time_limit (30);

$Firm='EnumXML';
$real_name = "$TmpFilesDir/$Firm.xml";

$data = file_get_contents($real_name);
echo ("<br>Data: $data\r\n");

//EnumDescription
//EnumName, EnumDescription, WhereUsed
$EDArr=array('EnumName', 'EnumDescription', 'WhereUsed');
$PK_EDArr=array('EnumName');

//EnumValues
//EnumName, EnumVal, Lang, EnumDescription
$FldsArr=array ('EnumName', 'EnumVal', 'Lang', 'EnumDescription');
$PK_Vals=array('EnumName', 'EnumVal', 'Lang');

$InsCnt=0;
$UpdCnt=0;

$Len = mb_strlen ($data);
$StartPos=0;

while ($StartPos<$Len) {
  $Pos = mb_strpos ($data, '<Enum>', $StartPos);
  if ($Pos!== false) {
    $Pos2 = mb_strpos ($data, '</Enum>', $Pos+6);
    if ($Pos2!== false) {
      $NewTxt= mb_substr ($data, $Pos+6, $Pos2-$Pos-6 );
      echo ("<br>|Pos=$Pos: $NewTxt|\r\n");
          
      $StartPos=$Pos2+6;

      $Arr=array();
      $Fld='EnumName';
      $Arr[$Fld]=GetXMLVal ($NewTxt, $Fld);
      $EnumName=$Arr[$Fld];
      if ($EnumName=='') {
        die ("<br> Error: Bad EnumName=$EnumName. Pos=$Pos\r\n<br>$NewTxt "); 
      }

      $Fld='EnumDescription';
      $Arr[$Fld]=trim(GetXMLVal ($NewTxt, $Fld));
      $Fld='WhereUsed';
      $Arr[$Fld]=trim(GetXMLVal ($NewTxt, $Fld));

      $Vals=GetXMLVal ($NewTxt, 'EnumVals');
      
      $ValsArr=array();
      XMLRead ( $Vals, $ValsArr, $EnumName);
      echo ("\r\n<br> ValsArr: ");
      print_r($ValsArr);

      UpdateTable ($pdo, 'EnumDescription', $EDArr, $Arr, $PK_EDArr);    
      
      foreach ($ValsArr as $Indx=> $V1) {
        UpdateTable ($pdo, 'EnumValues', $FldsArr, $V1, $PK_Vals);    
          //print_r($Arr);
      }
    }
    else {
      die ("<br> Error: not found /Enum ");
    }
  }
  else {
      $StartPos=$Len;
  }     
}

echo ("<br> Items: $InsCnt inserted, $UpdCnt updated<br>");


};

?>
</body>
</html>
				       