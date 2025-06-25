<html>
<head><title>Request Log</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
include ("../setup/config.php");
echo ("
<meta http-equiv=\"Content-Language\" content=\"$default_lang\">
</head>
<body>");

//$USER1 = $_ENV[""];
//echo ($USER1);

echo ("
<br>
<form method='get' action='indx2.php' enctype='multipart/form-data'>
Date begin (Example: 2010-02-27)<input type='text' size='10' name='DATE_BEG'><br>
Date end   (Example: 2010-03-30)<input type='text' size='10' name='DATE_END'><br>
<input type='submit' value='Find'>
</form>
<br>
    ");
    

$DateBeg = $_REQUEST['DATE_BEG'];
$DateEnd = $_REQUEST['DATE_END'];

$WhereClause = "";
if (strlen ($DateBeg) != 0) {
  $WhereClause = " (DateIns >= '$DateBeg')"; 
};

if (strlen ($DateEnd) != 0) {
  if (strlen ($WhereClause) != 0) {
    $WhereClause = $WhereClause." AND ";     
  }
  $WhereClause = $WhereClause." (DateIns <= '$DateEnd') ";   
}

if (strlen ($WhereClause) != 0) {
  $WhereClause = 'WHERE '.$WhereClause;

  $sql_txt =   "Select CATEGORY, SERIES_NAME, OUT_ORDER, SUM(ReqQty) Res "  
  ." FROM (  SELECT CATEGORY, PRODUCTION_GROUP, SUM( ArtQty ) ReqQty "
  ." FROM ( SELECT WEBPAGE ART, COUNT( `WebPage` ) ArtQty "
  ." FROM `VDL_LOG2` ".$WhereClause
  ." GROUP BY WEBPAGE )A, VDL_ITEMS I "
  ." WHERE A.ART = I.ARTICLE GROUP BY CATEGORY, PRODUCTION_GROUP) B "
  ." LEFT OUTER JOIN VDL_PROD_GR2SERIES AS SERIES "
  ." ON B.CATEGORY = SERIES.CAT_ID AND "
     ." B.PRODUCTION_GROUP = SERIES.prod_group_id "
     ." Group by OUT_ORDER, SERIES_NAME, CATEGORY ";
     
  //echo ($sql_txt); 

  echo ("
  <H3>Access Log by Items groups from $DateBeg to $DateEnd</H3>    
  <table width=\"50%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\">
      <tr align=\"left\">
	<td width=\"30%\">Cathegory</td>
	<td>Prod Group</td>
	<td>Mapics Group</td>
	<td>Requests Qty</td>
      </tr> 
  ");
  
  $sql4 = mysql_query ($sql_txt)
   or
       die("Could not connect: " . mysql_error());  
  while ($row = mysql_fetch_array($sql4)) {
    echo ("
    <tr>	
    <td width=\"30%\"><a href=\"detailed_info2.php?DATE_BEG=$DateBeg&DATE_END=$DateEnd&SER_NAME=$row[1]\">$row[0]</td>
      <td>$row[1]</td>
      <td>$row[2]</td>
      <td>$row[3]</td>
    </tr>
    ");
  };
  echo (" </table>");
  //echo ($sql3);
  //mysql_close ($sql);
  $sql_txt = "SELECT Reference, `IPAddr`, count( `WebPage` ) ArtQty "
    ."FROM `VDL_LOG2` $WhereClause "
    ." GROUP BY Reference, IPAddr ";

  //echo ($sql_txt); 

  echo ("
  <H3>Access Log from $DateBeg to $DateEnd</H3>    
  <table width=\"50%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\">
      <tr align=\"left\">
	<td width=\"30%\">Computer Name</td>
	<td>IP Address</td>
	<td>Article Qty</td>
      </tr> 
  ");
  
  $sql3 = mysql_query ($sql_txt)
   or
       die("Could not connect: " . mysql_error());  
  while ($row = mysql_fetch_array($sql3)) {
    echo ("
    <tr>	
    <td width=\"30%\"><a href=\"detailed_info.php?DATE_BEG=$DateBeg&DATE_END=$DateEnd&CN=$row[0]\">$row[0]</td>
      <td>$row[1]</td>
      <td>$row[2]</td>
    </tr>
    ");
  };
  echo (" </table>");


}  

?>
</body>
</html>
