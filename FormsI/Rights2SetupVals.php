<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Setup User Rights</title></head>
<body>
<?php

include ("Right.php");


$MTArr=array();

function ShowMenuTree (&$pdo, $StartP, $Level, &$MTArr) {
  if ($Level>100) {
    die ("<br> Error: Level> 100");
  }
  // Menu
  // Id, MenuName, Description, Link, 
  // NewWindow, ParentId, Ord, ColumnNo, MenuType, 
  // MenuCode
  $PdoArr = array();
  $PdoArr['StartP']= $StartP;

  try {

  $query = "select * from \"Menu\" ". 
           "where (\"Id\" = :StartP)"; 

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
      
  if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
    if ( empty ($MTArr[$dp2['Id']])) {
      $MTArr[$dp2['Id']]=array('Level'=>$Level, 'Descr'=>$dp2['MenuName'], 'Parent'=>$dp2['ParentId'], 
                               'MenuType'=>$dp2['MenuType']);
      if ($dp2['ParentId'] != 0) {
        ShowMenuTree ($pdo, $dp2['ParentId'], $Level+1, $MTArr);
      }
    
    }
    else {
      echo ("<br> Error: ");
      print_r( $MTArr);
      echo ("<br> Error: Menu {$dp2['Id']} already have in list ");

      return 2;
    }
  }
  else {
    echo ("<br> Error: Menu $StartP is not found");
    return 1;
  }

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }
  
  return 0;
}  



CheckRight1 ($pdo, 'Admin');

  $BegPos = $_REQUEST['BegPos'];
  if ($BegPos==''){
    $BegPos=0;
  }

  $RightSel = $_REQUEST['RightSel'];
  $SubRight = $_REQUEST['Sub'];
  
  try {
  $PdoArr = array();
  $PdoArr['RightSel']= $RightSel;
  $PdoArr['SubRight']= $SubRight;

  if ( $RightSel =='') { 
    die ("<br> Error: RightSel is empty");
  }

  if ( $SubRight =='') { 
    die ("<br> Error: SubRight is empty");
  }

  if ($RightSel == 'Menu') {
    ShowMenuTree ($pdo, $SubRight, 0, $MTArr);
  }
  
  $HaveLoc=0;

  
  $query = "SELECT \"RightSubType\", \"UsrName\" FROM \"UsrRights\" ".
           "WHERE (\"RightType\"= :RightSel) and (\"RightSubType\"=:SubRight) AND (\"Val\") ".
           "ORDER by \"RightSubType\", \"UsrName\"";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  $PredVal='';
  while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    if ($PredVal!= $dp['RightSubType']){
      if ( $PredVal!='') {
        $RSel.='<br>';
      }
      $PredVal= $dp['RightSubType'];
      $RSel.="<b>$PredVal:</b>";
      $Div='';
    }

    $SelectedArr[ $dp['RightSubType'] ][$dp['UsrName']]=1;
    
    $RSel.="$Div {$dp['UsrName']}";
    $Div=',';  
  };
  $PdoArr = array();

  $ORDS = ' order by "usr_id" '; 
  
  $WH = $_REQUEST['WH'];

  $ValF=array ();
  $ValF[ $_REQUEST['WHF'] ]=$WH; 



  $WHS = ''; 
  $WHF=$_REQUEST['WHF'];
  if ($WH !='') {
    $LK ='%'.$WH.'%'; 
    $WHS = " and (".$_REQUEST['WHF']." LIKE :".$_REQUEST['WHF'].") ";
    $PdoArr[$_REQUEST['WHF']]= $LK;
  
  };

  $LN = $_SESSION['LPP'];
  if ($LN=='') {
    $LN=20;  
  };

  $query = "select * FROM \"usrs\" ".
           " WHERE (1=1) ".
           " $WHS $ORDS LIMIT $LN offset $BegPos";

  //echo ("<br>$query<br>");

  $queryCNT = "select COUNT(*) CNT FROM \"usrs\" ".
              "WHERE (1=1) $WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);

  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };

  $CurrPage= round($BegPos/$LN)+1;
  $LastPage= floor($CntLines/$LN)+1;

  echo ('<br><b>'.$CntLines.'</b> total lines Page <b>'.$CurrPage.'</b> from '. $LastPage) ;
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  
  echo ('<table>
      <tr>
      <tr>
      <form method=get action="Rights2SetupVals.php">'.
      "<input type=hidden name='RightSel' value='$RightSel'>".
      "<input type=hidden name='Sub' value='$SubRight'>".
      '<td>'.GetStr ($pdo, 'UserId').':</td>
      <td><input type="text" length=10 size=10 name=WH value='.$ValF["usr_id"].'></td>
      <td><button name="WHF" value="usr_id" type="submit">Filter</button></td>
      </form>
      <form method=post action="Rights2SetupVals.php">
      <input type=hidden name="Sub" value="'.$SubRight.'">
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
      "<input type=text size=30 Name=RightSel1 value='$RightSel1'></td></tr>".
      '<td align=right><a href="RightList.php">'.GetStr ($pdo, 'Right').":</a> </td><td>". 
      GetRightSelection ($pdo, 'RightSel', $RightSel ).'</td></tr>');
   
   
   if ( $HaveLoc != 0) {  
     echo ('<tr><td align=right>'.GetStr ($pdo, 'Location').":</td><td>". 
        GetLocationSelection ($pdo,  'LocSel', $LocSel )."</td></tr>");
   }
   echo ("<tr><td>$RightSel SubRight:$SubRight <a href='Rights2Setup.php?RightSel=$RightSel'>Setup</a></td>".
         "<td><a href='AddUserList.php?Right=$RightSel&Sub=$SubRight' target=AddUser>Add user list</a></td>".
         "<td>$LocSel".
         '</td><td></td><td><button type="submit">Setup</button></td></tr>');
   echo ('</table></form>');



$FieldName=array ('usr_id', 'description');
$AddCols =array ();
$SetupRun=1;

if ($SetupRun ==1) {
 $AddCols[]= GetStr ($pdo, 'Can');
 $AddCols[]= GetStr ($pdo, 'Change');

 echo ('<form method=post action="Rights2SetupValsSave.php">'.
      "<input type=hidden name='RightSel' value='$RightSel'>".
      "<input type=hidden name='WH' value='$WH'>".
      "<input type=hidden name='BegPos' value='$BegPos'>".
      "<input type=hidden name='WHF' value='$WHF'>".
      "<input type=hidden name='LocSel' value='$SubRight'>");
};

echo ('<table><tr class="header">');
      
foreach ($FieldName as $Val) {
  echo ("<th><a href='RigtsSetup.php?ORD=$Val'>".GetStr($pdo, $Val)."</a></th>");
};


echo ("<th>".GetStr($pdo, 'Can')."</th>");
echo ("<th>".GetStr($pdo, 'Change')."</th>");
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
  
  $Checked='';

  if ($SetupRun ==1) {
    $Usr=$dp['usr_id'];
    $Val=$SubRight;
      
      if ($SelectedArr [$Val][$Usr]) 
        $Checked=' checked';
      
      echo ("<td align=center><input type=checkbox $Checked disabled></td>");
      
      echo ("<td align=center><input type=checkbox name='CHV_$KK' value='$Usr'></td>");
      
  }

  echo("</tr>");
};

echo("<tr><td colspan=3 align=right><input type=submit value='Save'></td></tr>");

echo ("</table></form>");

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



$FullRef='?ORD='.$ORD.'&WH='.$WH.'&WHF='.$WHF.'&RightSel='.$RightSel.'&LocSel='.$LocSel;

$PredPage= $BegPos-$LN;
if ($PredPage<0)
  $PredPage = 0; 

$LastPage1= floor($CntLines/$LN) * $LN;

echo('<table><tr class="header">');

if ($CurrPage>1) {
  echo('<td><a href="RigtsSetup.php'.$FullRef.'&BegPos=0"> << '.GetStr('FirstPage').' </a></td>' .
       '<td><a href="RigtsSetup.php'.$FullRef.'&BegPos='.$PredPage.'"> < '.GetStr($pdo, 'PredPage').'</a></td>');

};

echo ('<td><b>'.$CurrPage.'</b></td>');

if ($CurrPage< $LastPage) {
  echo ('<td><a href="RigtsSetup.php'.$FullRef.'&BegPos='.($BegPos+$LN).'"> '.GetStr($pdo, 'NextPage').' > </a></td>');
};

echo ('<td><a href="RigtsSetup.php'.$FullRef.'&BegPos='.$LastPage1.'"> Last Page '.$LastPage.'>> </a></td>'.
       '</tr></table>');

echo ("<br>$RSel<br>");

if ($RightSel == 'Menu') {
  $Div='';
  foreach ($MTArr as $Id => $Arr) {
    echo ("$Div<a href='Rights2SetupVals.php?RightSel=$RightSel&Sub=$Id' target=SetR$Id>$Id</a>");
    $Div=" | ";
  }

}

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