<?php
// include ("../setup/OutXlsHTML.php");
//==========================================================================================
function StartXls($FN ) {

header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$FN.'.xls');

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
function StartXlsStyle($FN ) {

$S = file_get_contents ("../setup/style1c.css");

header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$FN.'.xls');

echo ('<html xmlns:x="urn:schemas-microsoft-com:office:excel">
 <head>
  <meta http-equiv=Content-Type content="text/html; charset=windows-1251">
  <style>'.$S.'
  </style>
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
function NewLineXls($Cl='') {
  echo ("\r\n</tr><tr$Cl>");
}
//==========================================================================================
function EndFileXls() {
  echo ("\r\n</tr></table></body></html>");
}

//==========================================================================================


function OutStrXls( $Str) {
  echo ("<td x:str>".htmlspecialchars_decode(iconv( 'UTF-8', 'Windows-1251', $Str))."</td>\r\n");
}
//==========================================================================================
function OutDecXls( $Dec) {
  echo ("<td x:num>$Dec</td>\r\n");
}
//==========================================================================================
function OutDateXls($Dec) {
  echo ("<td x:dat>".substr($Dec,8,2).'.'.substr($Dec,5,2).'.'.substr($Dec,0,4)."</td>\r\n");
}
//==========================================================================================
?>