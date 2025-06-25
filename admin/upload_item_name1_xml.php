<?php>
session_start();
isset($_SESSION['login']) or die('вы не вошли');
echo 'Здравствуйте, ' . $_SESSION['login'].'<br>';
include "set_passw.php"
?>

<html>
<head><title>Upload XML Item Names. Vladislav +7(903) 736 7000</title></head>
<body>
<?php


echo("
 Information Vladislav +7(903) 736 7000

 <form method='post' action='upload_item_nameXML.php' enctype='multipart/form-data'>
   <table border='0'>
   <tr>
     <td>Upload data File Name (csv file)</td>
    </tr>
    <tr>
      <td>Choose XML file with Item Name (Reference,Description,Vendor,Warehouse)</td> 
      <td><input type='file' value='File name' name='userfile'></td>
    </tr>
    <tr>
      <td><input type='submit' value='Upload'></td>
    </tr> 
    </table>
 </form>
    ");

 AdminFooter ();
?>
</body>
</html>
