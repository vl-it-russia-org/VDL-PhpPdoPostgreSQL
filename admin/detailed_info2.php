<html>
<head><title>Detailed info log</title>
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
$SeriaName = $_REQUEST['SER_NAME'];

$WhereClause = "";
if (strlen ($DateBeg) != 0) {
  $WhereClause = " DateIns >= '$DateBeg'"; 
};

if (strlen ($DateEnd) != 0) {
  if (strlen ($WhereClause) != 0) {
    $WhereClause = $WhereClause." AND ";     
  }
  $WhereClause = $WhereClause." DateIns <= '$DateEnd'";   
}

if (strlen ($WhereClause) != 0) {
  $WhereClause = 'WHERE '.$WhereClause;

  $sql_txt =   "select Series.Series_NAME, Art, DESCRIPTION, DES_CODE_NEW, Qty  from ".
  "(SELECT WEBPAGE Art, COUNT(WEBPAGE) Qty FROM `VDL_LOG2` ". $WhereClause.
  " GROUP BY Art) A, VDL_ITEMS I, VDL_PROD_GR2SERIES Series ".
  " WHERE (A.ART = I.ARTICLE) AND ".
  "(I.CATEGORY = Series.CAT_ID) AND ".
  "(I.PRODUCTION_GROUP = Series.PROD_GROUP_ID) AND  ".
  "(Series.Series_NAME = '".$SeriaName."')" ;
   
  
  
  //echo ($sql_txt); 

  echo ("
  <H4>Detailed Access Log from $DateBeg to $DateEnd CatID  $SeriaName</H3>    
  <table width=\"90%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\">
      <tr align=\"left\">
	<td>Group</td>
	<td>Article</td>
	<td>Description</td>
	<td>Des.Code</td>	
	<td>Qty</td>
      </tr> 
  ");
  
  
  $sql3 = mysql_query ($sql_txt)
   or
       die("Could not connect: " . mysql_error());  
  while ($row = mysql_fetch_array($sql3)) {
    echo ("
    <tr>	
    <td>$row[0]</td>
      <td><a href=\"../index.php?article=$row[1]\">$row[1]<a></td>
      <td>$row[2]</td>
      <td>$row[3]</td>
      <td>$row[4]</td>
    </tr>
    ");
  };
  echo (" </table>");
  //echo ($sql3);
  //mysql_close ($sql);
}  

?>
</body>
</html>
