<?php
session_start();

include ("../setup/common_pg.php");
include ("common_func.php");

BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>AdmTabsAddFunc Card</title></head>
<body>
<?php
include ("FrmSql.js");
// Checklogin1();


CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','TabName','AddFunc','Param'
          );

$enFields= array('AddFunc'=>'TabAddFunction', 'AddFunc');

$Id=$_REQUEST['Id'];

echo("<H3>".GetStr($pdo, 'AdmTabsAddFunc')."</H3>");

$TabId='';
$TabName='';
  
$dp=array();
$FullLink="Id=$Id";

$New=$_REQUEST['New'];

$PdoArr = array();
try {


if ($New==1) {
  if (empty($_REQUEST['TabName'])) {
    die ("<br> Error: TabName is empty");
  }
  $TabName=$_REQUEST['TabName'];  
}
else {
  $PdoArr['Id']= $Id;
  $query = "select * FROM \"AdmTabsAddFunc\" ".
           "WHERE (\"Id\"=:Id)";
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $TabName=$dp['TabName'];  
  }
}

if ($TabName != '') {
  // AdmTabNames
  // TabName, TabDescription, TabCode, TabEditable, 
  // AutoCalc, CalcTableName, ChangeDt, Ver, Param
  $query = "select * from \"AdmTabNames\" ". 
           "where (\"TabName\" = :TabName)"; 

  $PdoArr = array();
  $PdoArr['TabName']= $TabName;
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  if ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
    $TabId=$dp22['TabCode'];
    echo ("Table: <a href='TabCard.php?TabCode=$TabId'><b>$TabId</b></a> $TabName<br><i>".
                                                             DivTxt ($dp22['TabDescription'])."</i><hr>");  
  }
  else {
    die ("<br> Table $TabName is not found "); 
  }
}



$Editable=1;
if ($Editable) {
  if ($New==1) {
    $dp["TabName"]= addslashes($_REQUEST["TabName"]);
  }
  echo ('<form method=post action="AdmTabsAddFuncSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $LN=0;
  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldId' value='$OutVal'>");
  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Id}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='TabName';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='AddFunc';
  $OutVal= $dp[$Fld];  echo ("<td align=right>".GetStr($pdo, $Fld).": </td><td>");
  echo ( EnumSelection($pdo, "TabAddFunction", "AddFunc ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $AddFunc= $OutVal;
  if ($AddFunc==10){
    // Master table;

    echo ("<td align=right>".GetStr($pdo, 'SelectTable').": </td>");
    echo ("<td><input type=text Name=MasterTabName size=15 ID=MasterTabName placeholder='Table sub-name'> ".
      "<button onclick='return GetTabName(\"MasterTabName\");'>...</button><br>".
      "<select Name=TabList size=10 ID=TabList title='Tables' ".
      "onchange='return CopyTxtValue(\"TabList\", \"MasterTabName\");'> ".
         "<option value='' disabled>Select Table</option>".
      "</select><br></td>");


  }
  else
  if ($AddFunc==20){
    // Master table fields correspondence
    $MasterTabName = GetMasterTabName($pdo, $TabName); 
    echo ("<br>MasterTabName: <b>$MasterTabName</b><br>");

    if ( $MasterTabName!= '') {
      echo ("</tr><tr>");
      echo ("<td align=right>".GetStr($pdo, 'SetFiledsCorr').": </td><td>");

      echo ("<div id=TabCorresp class=Flex>".
            "<div id=CurrTab  style='overflow-y: scroll; height:230px;'><h3>$TabName</h3>");

      $CorrFields = GetMasterCorrFields ($pdo, $dp['Param']); 
      //print_r ($CorrFields);
      $MaxV=0;
      foreach ($CorrFields['FldT']  as $FN=> $FV) {
        if ($FV> $MaxV) {
          $MaxV=$FV;
        }
      }

      echo ("<input type=hidden Name=CurrIndx ID=CurrIndx value=$MaxV>".
            "<input type=hidden Name=IndxT    ID=IndxT value=0>".
            "<input type=hidden Name=IndxM    ID=IndxM value=0>");

      // AdmTabNames
      // TabName, TabDescription, TabCode, TabEditable, 
      // AutoCalc, CalcTableName, ChangeDt, Ver, Param
      $query = "select \"TabCode\",\"TabName\" from \"AdmTabNames\" ". 
               "where (\"TabName\" in ('$MasterTabName', '$TabName'))"; 

      $STH = $pdo->prepare($query);
      $STH->execute();
      
      $TabCode='';
      $MTabCode='';
      while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
        if ($dp2['TabName']==$TabName) {
          $TabCode = $dp2['TabCode'];
        };

        if ($dp2['TabName']==$MasterTabName) {
          $MTabCode = $dp2['TabCode'];
        };
      }

      $CorrFields = GetMasterCorrFields ($pdo, $dp['Param']); 
      //print_r ($CorrFields);
      
      // AdmTabFields
      // TypeId, ParamNo, ParamName, NeedSeria, 
      // DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
      // CalcFormula, AutoInc, Description, BinCollation, ShortInfo, 
      // EnumLong
      $PdoArr = array();
      $PdoArr['TabCode']= $TabCode;

      $query = "select \"ParamNo\", \"ParamName\", \"DocParamType\", \"AddParam\" from \"AdmTabFields\" ". 
               "where (\"TypeId\" = :TabCode) order by \"Ord\""; 

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
      
      $Str='';
      $i=0;
      while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
        $SelFields['T'][$dp2['ParamNo']] = $dp2; 
        $cl='';
        if ($i==1) {
          $i=0;
          $cl=' class=even ';
        }
        else 
          $i++;
        
        $Val='';
        $FldN = $dp2['ParamName'];
        if (!empty ($CorrFields['FldT'][$FldN]) ) {
          $Val=" value='{$CorrFields['FldT'][$FldN]}' ";
        }

        //echo ("<br> Val= $Val | $FldN | {$CorrFileds['FldT'][$FldN]} <br> ");
        
        $Str.="<tr$cl>".
              "<td>{$dp2['ParamNo']}</td><td>{$dp2['ParamName']}</td>".
              "<td><button onclick='JavaScript:return SelectTFld(\"{$dp2['ParamNo']}\");'> + </button></td>".
              "<td><input type=text Name=FldT[{$dp2['ParamNo']}] Id=FldT_{$dp2['ParamNo']} size=4 $Val></td></tr>";
      }

      echo ("<table>$Str</table>".
             "</div>"); // CurrTab

      $PdoArr = array();
      $PdoArr['MTabCode']= $MTabCode;
      
      $query = "select \"ParamNo\", \"ParamName\", \"DocParamType\", \"AddParam\" from \"AdmTabFields\" ". 
               "where (\"TypeId\" = :MTabCode) order by \"Ord\""; 

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
      
      $Str='';
      $i=0;
     
      while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
        $cl='';
        if ($i==1) {
          $i=0;
          $cl=' class=even ';
        }
        else 
          $i++;


        $Val='';
        $FldN = $dp2['ParamName'];
        if (!empty ($CorrFields['FldM'][$FldN]) ) {
          $Val=" value='{$CorrFields['FldM'][$FldN]}' ";
        }

        

        $SelFields['M'][$dp2['ParamNo']] = $dp2; 
        $Str.="<tr$cl><td><input type=text Name=FldM[{$dp2['ParamNo']}] Id=FldM_{$dp2['ParamNo']} size=4 $Val></td>".
              "<td><button onclick='JavaScript:return SelectMFld(\"{$dp2['ParamNo']}\");'> + </button></td>".
              "<td>{$dp2['ParamNo']}</td><td>{$dp2['ParamName']}</td>".
              "</tr>";
      }
            
      echo ("<div id=MasterTab style='overflow-y: scroll; height:230px;'><h3>$MasterTabName</h3>");
      echo ("<table>$Str</table>".
             "</div>"); // MasterTab

      echo ("</div>"); // TabCorresp
    
      echo ("</td></tr><tr>");
    }

  
  }
  else
  if ($AddFunc=30){
    // Master table fields copy
    $MasterTabName = GetMasterTabName($pdo, $TabName); 
    echo ("<br>MasterTabName: <b>$MasterTabName</b><br>");

    if ( $MasterTabName!= '') {
      echo ("</tr><tr>");
      echo ("<td align=right>".GetStr($pdo, 'SetFiledsCorr').": </td><td>");

      echo ("<div id=TabCorresp class=Flex>".
            "<div id=CurrTab  style='overflow-y: scroll; height:230px;'><h3>$TabName</h3>");

      $CorrFields = GetMasterCorrFields ($pdo, $dp['Param']); 
      //print_r ($CorrFields);
      $MaxV=0;
      foreach ($CorrFields['FldT']  as $FN=> $FV) {
        if ($FV> $MaxV) {
          $MaxV=$FV;
        }
      }

      echo ("<input type=hidden Name=CurrIndx ID=CurrIndx value=$MaxV>".
            "<input type=hidden Name=IndxT    ID=IndxT value=0>".
            "<input type=hidden Name=IndxM    ID=IndxM value=0>");

      // AdmTabNames
      // TabName, TabDescription, TabCode, TabEditable, 
      // AutoCalc, CalcTableName, ChangeDt, Ver, Param
      $query = "select \"TabCode\",\"TabName\" from \"AdmTabNames\" ". 
               "where (\"TabName\" in ('$MasterTabName', '$TabName'))"; 

      $STH = $pdo->prepare($query);
      $STH->execute();
      
      $TabCode='';
      $MTabCode='';
      while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
        if ($dp2['TabName']==$TabName) {
          $TabCode = $dp2['TabCode'];
        };

        if ($dp2['TabName']==$MasterTabName) {
          $MTabCode = $dp2['TabCode'];
        };
      }

      $CorrFields = GetMasterCorrFields ($pdo, $dp['Param']); 
      //print_r ($CorrFields);
      
      // AdmTabFields
      // TypeId, ParamNo, ParamName, NeedSeria, 
      // DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
      // CalcFormula, AutoInc, Description, BinCollation, ShortInfo, 
      // EnumLong
      $PdoArr = array();
      $PdoArr['TabCode']= $TabCode;

      $query = "select \"ParamNo\", \"ParamName\", \"DocParamType\", \"AddParam\" from \"AdmTabFields\" ". 
               "where (\"TypeId\" = :TabCode) order by \"Ord\""; 

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
      
      $Str='';
      $i=0;
      while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
        $SelFields['T'][$dp2['ParamNo']] = $dp2; 
        $cl='';
        if ($i==1) {
          $i=0;
          $cl=' class=even ';
        }
        else 
          $i++;
        
        $Val='';
        $FldN = $dp2['ParamName'];
        if (!empty ($CorrFields['FldT'][$FldN]) ) {
          $Val=" value='{$CorrFields['FldT'][$FldN]}' ";
        }

        //echo ("<br> Val= $Val | $FldN | {$CorrFileds['FldT'][$FldN]} <br> ");
        
        $Str.="<tr$cl>".
              "<td>{$dp2['ParamNo']}</td><td>{$dp2['ParamName']}</td>".
              "<td><button onclick='JavaScript:return SelectTFld(\"{$dp2['ParamNo']}\");'> + </button></td>".
              "<td><input type=text Name=FldT[{$dp2['ParamNo']}] Id=FldT_{$dp2['ParamNo']} size=4 $Val></td></tr>";
      }

      echo ("<table>$Str</table>".
             "</div>"); // CurrTab

      $PdoArr = array();
      $PdoArr['MTabCode']= $MTabCode;      
      $query = "select \"ParamNo\", \"ParamName\", \"DocParamType\", \"AddParam\" from \"AdmTabFields\" ". 
               "where (\"TypeId\" = :MTabCode) order by \"Ord\""; 

      $sql2 = $pdo->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
      
      $Str='';
      $i=0;
     
      while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
        $cl='';
        if ($i==1) {
          $i=0;
          $cl=' class=even ';
        }
        else 
          $i++;


        $Val='';
        $FldN = $dp2['ParamName'];
        if (!empty ($CorrFields['FldM'][$FldN]) ) {
          $Val=" value='{$CorrFields['FldM'][$FldN]}' ";
        }

        

        $SelFields['M'][$dp2['ParamNo']] = $dp2; 
        $Str.="<tr$cl><td><input type=text Name=FldM[{$dp2['ParamNo']}] Id=FldM_{$dp2['ParamNo']} size=4 $Val></td>".
              "<td><button onclick='JavaScript:return SelectMFld(\"{$dp2['ParamNo']}\");'> + </button></td>".
              "<td>{$dp2['ParamNo']}</td><td>{$dp2['ParamName']}</td>".
              "</tr>";
      }
            
      echo ("<div id=MasterTab style='overflow-y: scroll; height:230px;'><h3>$MasterTabName</h3>");
      echo ("<table>$Str</table>".
             "</div>"); // MasterTab

      echo ("</div>"); // TabCorresp
    
      echo ("</td></tr><tr>");
    }

  
  }




  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='TabName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='AddFunc';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "TabAddFunction", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='Param';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='AdmTabsAddFuncList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='AdmTabsAddFuncDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
 }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>
</body>
</html>
