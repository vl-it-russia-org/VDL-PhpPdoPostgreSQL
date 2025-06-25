<?php

function SaveHist ( $TabName, $WhereClause, $LogMsg, $LogType, $LogParam1, $LogParam2) {
  $HistId=MakeAdminRec ($_SESSION['login'], $LogType, $LogParam1, 
                        $LogParam2, $LogMsg);

  $query = "  insert into ".$TabName."_hist (".
         "select $HistId, * ".
         "FROM $TabName ".
         " WHERE $WhereClause";
  
  //$sql2 = mysql_query ($query)
  //               or die("Invalid query:<br>$query<br>" . mysql_error());
                        
};  

?>