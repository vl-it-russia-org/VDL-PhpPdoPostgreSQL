<?php
session_start();

include ("../setup/common_pg.php");
//include ("../commoni.php");

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="style.css">
<title>Setup User Rights</title></head>
<body>
<?php

include ("Right.php");

//print_r($_REQUEST);
$SubType = $_REQUEST['SubType'];

$BegPos = $_REQUEST['BegPos']+0;
if ($BegPos==''){
  $BegPos=0;
}

$RightSel1 =$_REQUEST['RightSel1'];
$RightSel = $_REQUEST['RightSel1'];
if ( $RightSel =='') { 
  $RightSel = $_REQUEST['RightSel'];
}


if ($RightSel=='Route.Can') {
  CheckRight1 ($pdo, 'Route.Edit');
}
else {
  CheckRight1 ($pdo, 'RIGHT_EDIT');
}
  
$RS = '';

$EnumRights='';

try {
  $LocSel = $_REQUEST['LocSel'];
  $SubRight='-';
  $HaveLoc=0;
  if ($RightSel!='') {
    
    // Rights
    // RightType, RightDescription, HelpLink, NeedLocation, EnumRight, 
    // HaveTable, TabName, FieldName, FldDescription
    $query = "select * FROM \"Rights\" ".
             "WHERE (\"RightType\"= :RightSel) ";

    
    
    $PdoArr = array();
    $PdoArr['RightSel']= $RightSel;
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);


    //          echo ("<br> Line ".__LINE__.": $query<br>");
    //          print_r($PdoArr);
    //          echo ("<br>");


    if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
      //echo ("<br> 73 DP: ");
      // print_r($dp);

      $HaveLoc=$dp['NeedLocation'];  
      $RS= $dp['TabName'];

      $EnumRights=$dp['EnumRight'];
    
    };
  }

  $SetupRun=0;
  $RSel='';
  if ($RightSel!='') {
    if ($HaveLoc==1) {
      $SetupRun= ($LocSel !='');
      $SubRight= $LocSel;
    }
    else {
      $SetupRun=1;
      $SubRight='-';
    }    
  
    $Div='';
    $query = "SELECT \"RightSubType\", \"UsrName\" FROM \"UsrRights\" ".
             "WHERE (\"RightType\"= :RightSel) AND \"Val\" ORDER by \"RightSubType\", \"UsrName\"";

    $PdoArr = array();
    $PdoArr['RightSel']= $RightSel;
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
    
    $PredVal='';


    $PdoArr = array();
    $UsrArr=array();
    
    $query = "select * from \"usrs\" ". 
             "where (\"usr_id\" = :UN)"; 
    
    $STH21 = $pdo->prepare($query);
    $PdoArr21 = array();
    
    
    while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
      if ($PredVal!= $dp['RightSubType']){
        if ( $PredVal!='') {
          $RSel.='<br>';
        }
        $PredVal= $dp['RightSubType'];
      
        if ( $EnumRights != '') {
          $RSel.="<b>[$PredVal] ".GetEnum($pdo, $EnumRights, $PredVal).":</b>";
        }
        else {
          $RSel.="<b>$PredVal:</b>";

        }
      
      }

      $UN= $dp['UsrName'];
      if (empty ($UsrArr[$UN])) {
        $Res='D';
        // usrs
        // usr_id, usr_pwd, description, admin, 
        // email, phone, passwd_duedate, new_passwd, passwd_last_change, 
        // SFUser, Blocked, WebCookie, Position, Department, 
        // Company, FirstName, LastName, PatronymicName__c, PwdCoded

        $PdoArr21['UN']= $UN;
        $STH21->execute($PdoArr21);
                              
        if ($dp21 = $STH21->fetch(PDO::FETCH_ASSOC)) {
        
          if ($dp21['Blocked']==0) {
            $Res='H';
          }
        }
        $UsrArr[$UN]=$Res;
      }

      $SB='';
      $SE='';
      if ($UsrArr[$UN]=='D') {
        $SB='<s>';
        $SE='</s>';
      }
      
      $RSel.="$Div $SB{$dp['UsrName']}$SE";
      $Div=',';  
    };
  } 

  $ORD = $_REQUEST['ORD'];
  $ORDS = ' order by "usr_id" '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  };
  
  
  $WH = $_REQUEST['WH'];

  $ValF=array ();
  $ValF[$_REQUEST['WHF'] ]=$WH; 



  $WHS = '(1=1)'; 
  $WHF=$_REQUEST['WHF'];
  if ($WH !='') {
    $WHS .= " and (".$_REQUEST['WHF']." LIKE '%".$WH."%') ";
  };

  $LN = $_SESSION['LPP'];
  if ($LN=='') {
    $LN=20;  
  };

  if ($SubType!='') {
    if ($SubRight=='-') {
      $SubRight=$SubType;
    }
  }

  $query = "select * FROM \"usrs\" ".
           "WHERE $WHS $ORDS LIMIT $LN OFFSET $BegPos";

  //echo ("<br>$query<br>");

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"usrs\" ".
              "WHERE $WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute();


  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };

  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  $EnumVal = $_REQUEST['EnumVal'];

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  //echo ("<br> Line ".__LINE__.": $query<br>");
  //              print_r($PdoArr);
  //              echo ("<br>");
  
  
  
  echo ('<br><b>'.$CntLines.'</b> total lines Page <b>'.$CurrPage.'</b> from '. $LastPage) ;
  
  echo ('<table><tr><tr>
      <form method=get action="RigtsSetup.php">'.
      "<input type=hidden name='RightSel' value='$RightSel'>".
      "<input type=hidden name='LocSel' value='$SubRight'>".
      '<td>'.GetStr ($pdo, 'UserId').':</td>
      <td><input type="text" length=10 size=10 name=WH value='.$ValF["usr_id"].'></td>
      <td><button name="WHF" value="usr_id" type="submit">Filter</button></td>
      </form>
      <form method=post action="RigtsSetup.php">
      <td>'.GetStr ($pdo, 'UserName').':</td>
      <td><input type="text" length=20 size=20 name=WH value='.$ValF["description"].'></td>
      <td><button name="WHF" value="description" type="submit">Filter</button></td>
      </form>
      </tr>
      </table>');

   echo ('<form method=post action="RigtsSetup.php">'.
         "<input type=hidden name='WH' value='$WH'>".
         "<input type=hidden name='WHF' value='$WHF'>".
   '<table>
      <tr>
      
      <td align=right>'.GetStr ($pdo, 'Right').":</td><td>". 
      "<input type=text size=30 Name=RightSel1 value='$RightSel1'></td>".
      "<td>$SubType</td>".
      "</tr>".
      '<td align=right><a href="RightList.php">'.GetStr ($pdo, 'Right').":</a> </td><td>". 
      GetRightSelection ($pdo, 'RightSel', $RightSel ).'</td></tr>');
   
   
   if ( $HaveLoc != 0) {  
     /*
     echo ('<tr><td align=right>'.GetStr ($pdo, 'Location').":</td><td>". 
        GetLocationSelection ($pdo,  'LocSel', $LocSel )."</td>".
        "<td><a href='CleanupAllRights.php?Right=$RightSel&Loc=$LocSel'>CleanUp All</a>".
        " | <a href='SetRightsByList.php?Right=$RightSel&Loc=$LocSel'>Set by list</a></td>".
        "</tr>");
     */
   }
   if ($RS != '') {
     $RS=" <a href='Rights2Setup.php?RightSel=$RightSel'>Setup2</a>";  
   }

   $TxtEnumVal='';

   
   if ($EnumRights != '') {
     echo ("<tr><td align=right>Enum:</td><td> $EnumVal ".EnumSelection($pdo, $EnumRights, 'EnumVal', $EnumVal)."</td></tr>"); 
     if ($EnumVal != '') {
       $LocSel = $EnumVal;
       $TxtEnumVal=' '.GetEnum($pdo, $EnumRights, $EnumVal).' '  ; 
       $SubRight = $EnumVal; 
     }
   
   }

   
   
   
   echo ('<tr><td>'.$RightSel.'</td><td>'.$LocSel.$TxtEnumVal.$RS.
         '</td><td></td><td><button type="submit">Setup</button></td></tr>');
   echo ('</table></form>');



$FieldName=array ('usr_id', 'description');
$AddCols =array ();

if ($SetupRun ==1) {
 $AddCols[]= GetStr ($pdo, 'Can');
 $AddCols[]= GetStr ($pdo, 'Change');

 echo ('<form method=post action="RightsAllChange.php">'.
      "<input type=hidden name='SubType' value='$SubType'>".
      "<input type=hidden name='RightSel' value='$RightSel'>".
      "<input type=hidden name='WH' value='$WH'>".
      "<input type=hidden name='BegPos' value='$BegPos'>".
      "<input type=hidden name='WHF' value='$WHF'>".
      "<input type=hidden name='EnumVal' value='$EnumVal'>".
      "<input type=hidden name='LocSel' value='$SubRight'>");
};

echo ('<table><tr class="header">');
      
foreach ($FieldName as $Val) {
  echo ("<th><a href='RigtsSetup.php?ORD=$Val'>".GetStr($pdo, $Val)."</a></th>");
};

if ($SetupRun ==1) {
  foreach ($AddCols as $Val) {
    if ($Val== GetStr ($pdo, 'Change')) {
      echo ("<th>$Val<br><input type=checkbox ID=ALL_SEL onclick='SelectAll()'></th>");
    }
    else
      echo ("<th>$Val</th>");
  }
};

echo ("</tr>");

$n=0;
$KK=0;
    


while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $classtype="";
  $n++;
  $KK++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }
  echo ("<tr".$classtype.">");

  foreach ($FieldName as $Val) {
    if ($Val=='usr_id') {
      echo ("<td><a href='RigtsSetupAcc.php?Usr={$dp[$Val]}&RightSel=$RightSel'>".$dp[$Val]."</a></td>");
    }
    else { 
      echo ("<td>".$dp[$Val]."</td>");
    }
  }
  
  if ($SetupRun ==1) {
    $Checked='';
    $Usr=$dp['usr_id'];
    if (HaveRight2 ( $pdo, $Usr, $RightSel, $SubRight)) $Checked=' checked';
    echo ("<td align=center><input type=checkbox name='CV_$KK' value='$Usr' $Checked disabled></td>");
    echo ("<td align=center><input type=checkbox name='CHV_$KK' ID='CHV_$KK' value='$Usr'></td>");
  }

  echo("</tr>");
};

if ($SetupRun ==1) {
  echo ('<tr><td></td><td></td><td></td>'.
    "<td><input type=submit value='Change'<td></tr>");  
}
echo ("</table>");

if ($SetupRun ==1) {
  echo ('</form>');
}

//---------------------------------------------------------------------
// All checked/unchecked
echo ("<script>
function SelectAll(){
  Val=document.getElementById('ALL_SEL').checked;
  for (i=1; i<= $KK; i++) {
    El=document.getElementById('CHV_'+i );
    El.checked = Val;
  }
}
</script>");

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }



$FullRef='?ORD='.$ORD.'&WH='.$WH.'&WHF='.$WHF.'&RightSel='.$RightSel.'&LocSel='.$LocSel;

$PredPage= $BegPos-$LN;
if ($PredPage<0)
  $PredPage = 0; 

$LastPage1= floor($CntLines/$LN) * $LN;

echo('<table><tr class="header">');

if ($CurrPage>1) {
  echo('<td><a href="RigtsSetup.php'.$FullRef.'&BegPos=0"> << '.GetStr($pdo,'FirstPage').' </a></td>' .
       '<td><a href="RigtsSetup.php'.$FullRef.'&BegPos='.$PredPage.'"> < '.GetStr($pdo,'PredPage').'</a></td>');

};

echo ('<td><b>'.$CurrPage.'</b></td>');

if ($CurrPage< $LastPage) {
  echo ('<td><a href="RigtsSetup.php'.$FullRef.'&BegPos='.($BegPos+$LN).'"> '.GetStr($pdo,'NextPage').' > </a></td>');
};



echo ('<td><a href="RigtsSetup.php'.$FullRef.'&BegPos='.$LastPage1.'"> Last Page '.$LastPage.'>> </a></td>'.
       "<td><a href='../General/RespPersonsList.php'>Resp. persons</a></td>".
       '</tr></table>');
//------------------------------------------------------------------------------
// копирование прав

echo ("<script>
      function SelectFldUM(Id) {
        WN='UserSelect.php';
        SType='".GetStr($pdo, 'Select').' '.
            GetStr($pdo, 'User')."';
        ResId='&ResId='+Id;
        SubVal = document.getElementById(Id).value;
        a=window.open(WN+'?SubStr='+SubVal+ResId, SType,
              'width=650,height=620,resizable=yes,scrollbars=yes');
        return false;
      }
      </script>");



echo ('<form method=post action="RightsUser2UserCopy.php">'.
    '<table><tr class="header">'.
     '<td>Copy from: </td>'.
    '<td><input type="text" size=15 length=50 name=FromUser id="FromUser"> '.
    '<button onclick="return SelectFldUM(\'FromUser\');">...</button></td> '. 
    '<td><input type="submit" value=" >> Copy to >> "> </td> '.
    '<td><input type="text" size=15 length=50 name=ToUser id="ToUser"> '.
    '<button onclick="return SelectFldUM(\'ToUser\');">...</button></td> '. 
    '</tr><tr><td colspan=2 align=right>Replace E-Mail:</td>'.
    '<td><input type=checkbox name=ReplaceEmail value=1></td></tr></table>');



//==============================================================================
echo ("<br>$RSel<br>");
?>
</body>
</html>