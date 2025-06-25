<?php
session_start();

include ("../setup/common.php");
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>SystemDescription Card</title></head>
<body>
<?php
// Checklogin1();
CheckRight1 ($pdo, 'ExtProj.Admin');

 
  echo ("<form method='post' action='SystemDescription-UploadXlsx.php' enctype='multipart/form-data'>
   <table border='0'>
   <tr>
     <td>Upload XLSX file to SystemDescription with up to 6 columns:<br>Id, ParagraphNo, ElType, <br>
      Description, Ord1, ParentId</td>
    </tr>
    <tr>
      <td><input type='file' accept='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' value='File name:' name='userfile'></td>
    </tr> ".
    "<tr>".
     //"<td>Add lines to project: <input type=checkbox name='AddToProject' value=1></td>".
    "</tr>".

    "<tr>
      <td align=right><input type='submit' value='Upload'></td>
    </tr> 
    </table>
 </form>");
  //---------------------------------------------------------------------------------  
?>
</body></html>