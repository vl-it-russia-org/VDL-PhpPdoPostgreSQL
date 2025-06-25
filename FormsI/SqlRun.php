<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" href="../Img/SQL.ico" type="image/x-icon">
<title>SQL results</title></head>
<body>
<?php

CheckLogin1();

CheckRight1 ($pdo, 'Admin');

//print_r($_REQUEST);
//die ();

$HaveSelect=0;

if ( $_REQUEST['SqlTxt']!='') {
  $query = trim ($_REQUEST['SqlTxt']);

  $Sel = strtoupper(substr($query, 0, 6));
  $Sel2 = strtoupper(substr($query, 0, 5));
  
  try {
  $STH = $pdo->prepare($query);
  $STH->execute();


  if ( ($Sel == 'SELECT') OR ($Sel2 == 'SHOW ')) {
    $FT=1;
    $i=0;
    echo ("SELECT: $query");
    
    
    $LN=0;


    while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
      $LN++;
      if ($FT==1) {
        $HaveSelect=1;
        echo ("<table><tr class=head><th>Row</th>");

        foreach ($dp2 as $Fld => $Val) {
          echo ("<th>$Fld</th>");
        }

        $FT=0;
      }

      $i= NewLine($i);
      echo ("<td align=right>$LN</td>");
      foreach ($dp2 as $Fld => $Val) {
        echo ("<td>$Val</td>");
      }
    }
    echo ("</table>");


    //=======================================================================

    // SqlLog
    // Id, User, OpDate, TabNo, 
    // Description, SqlText
    $Usr=$_SESSION['login'];
    $PdoArr = array();
    $PdoArr['Usr']= $Usr;
    $PdoArr['Sel']= "SELECT $LN rows";
    $PdoArr['q']= $query;

    
    $query = "insert into \"SqlLog\" (\"User\", \"OpDate\", \"TabNo\", \"Description\", \"SqlText\") ". 
             "values (:Usr, now(), -10, :Sel, :q)"; 

    $STH21 = $pdo->prepare($query);
    $STH21->execute($PdoArr);

    //=======================================================================

  
  }
  else {
    $RA = $STH->rowCount();
    echo ("<br> Rows affeced: $RA ");
  
    //=======================================================================

    // SqlLog
    // Id, User, OpDate, TabNo, 
    // Description, SqlText
    $Usr=$_SESSION['login'];
    $PdoArr = array();
    $PdoArr['Usr']= $Usr;
    $PdoArr['Sel']= "$RA rows affected";
    $PdoArr['q']= $query;
    
    
    $query = "insert into \"SqlLog\" (\"User\", \"OpDate\", \"TabNo\", \"Description\", \"SqlText\") ". 
             "values (:Usr, now(), -10, :Sel, :q)"; 

    $STH21 = $pdo->prepare($query);
    $STH21->execute($PdoArr);

    //=======================================================================
  
  
  }
  
  echo ("<br>Ok<br>");
  
  if ($HaveSelect==1) {
    $SqlTxt=addslashes($_REQUEST['SqlTxt']);
    echo ("<table><tr><td><a href='FrmSql.php'>SQL Run</a></td><td>".
          "<form method=post action=GetSmallList.php>".
          "<input type=hidden Name=SqlTxt Value='$SqlTxt'>".
          "<input type=Submit value='Small list'></form></td></tr></table>"); 
  }
  else {
  
    echo ("<a href='FrmSql.php'>SQL Run</a>"); 
  }
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

}




?>
</body>
</html>
                       