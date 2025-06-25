<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();
CheckRight1 ($pdo, 'Admin');

?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>XML enum file upload</title></head>
<body>
<?php

$FileName='Enum';       
echo("
 Information Vladislav +7(903) 736 7000
 <form method='post' action='XmlReadEnum_Upl.php' enctype='multipart/form-data'>
   <table border='0'>
   <tr>
     <td>Upload $FileName XML file </td>
    </tr>
    <tr>
      <td><input type='file' value='File name:' name='userfile'></td>
    </tr>
    <tr>
      <td align=right><input type='submit' value='Upload'></td>
    </tr> 
    </table>
 </form>");
//
?>

</body>
</html>
