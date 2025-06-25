<?php
function HtmlTxt($FromTxtIn) {
  // HtmlSubst
  // TagFrom, TagTo
  $FromTxt=array('[/a]', '[/b]', '[/li]', '[/ul]', '[a]', '[br]', '[b]', '[f]', 
                 '[li]', '[p]',  '[ul]' ,'[html]','[/html]','[body]','[/body]', '[sup]', '[/sup]' , '[sp]', '[beg]');
  $ToTxt=array(  '</a>', '</b>', '</li>', '</ul>', '<a',  '<br>', '<b>', '>',
                 '<li>', '<p>',  '<ul>','<html>', '</html>','<body>','</body>', '<sup>', '</sup>' , '<sup', '<');

  return str_replace($FromTxt, $ToTxt,$FromTxtIn);  
}

?>