<?php
//==========================================================================================
function StartXls ($FName) {
$add_str=date('Ymd_His');

header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$FName.'-'.$add_str.'.xls');

echo ('<html xmlns:x="urn:schemas-microsoft-com:office:excel">
 <head>
  <meta http-equiv=Content-Type content="text/html; charset=windows-1251">
  <xml>
   <x:ExcelWorkbook>
    <x:ExcelWorksheets>
     <x:ExcelWorksheet>
      <x:Name>Page 1</x:Name>
      <x:WorksheetOptions>
       <x:NoSummaryRowsBelowDetail/>
       <x:ProtectContents>False</x:ProtectContents>
       <x:ProtectObjects>False</x:ProtectObjects>
       <x:ProtectScenarios>False</x:ProtectScenarios>
      </x:WorksheetOptions>
     </x:ExcelWorksheet>
    </x:ExcelWorksheets>
   </x:ExcelWorkbook>
  </xml>
 </head>
 <body>
  <table style="white-space:nowrap">
   <tr>');

}
//==========================================================================================
function NextRow () {
  echo ("</tr><tr>\r\n");
}
//==========================================================================================
function OutCreated( $Cols=6) {
  echo ("<td x:str colspan=$Cols>Created by ".To1251($_SESSION['login'])." ".date ('d-m-Y H:i:s')."<td>\r\n");
}
//==========================================================================================
function OutStr($Str, $cols=1) {
  if ($cols>1) {
    echo ("<td x:str colspan=$cols>".htmlspecialchars_decode(iconv( 'UTF-8', 'Windows-1251', $Str))."</td>\r\n");
  }
  else { 
    echo ("<td x:str>".htmlspecialchars_decode(iconv( 'UTF-8', 'Windows-1251', $Str))."</td>\r\n");
  }
}
//==========================================================================================
function OutDec( $Dec) {
  echo ("<td x:num>$Dec</td>\r\n");
}
//==========================================================================================
function OutDate($Dec) {
  echo ("<td x:dat>".substr($Dec,8,2).'.'.substr($Dec,5,2).'.'.substr($Dec,0,4)."</td>\r\n");
}
//==========================================================================================
function FinishXls() {

echo("   
   </tr>
   <tr >
   </tr>
  </table>
 </body>
</html>");

}
?>