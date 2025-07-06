<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'ToDo_Line')."</h4>");
  $query = "select * FROM ToDo_Line where Id='$Id' order by Id ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'ParentId').'</th>');
  echo('<th>'.GetStr($pdo, 'ToDoCode').'</th>');
  echo('<th>'.GetStr($pdo, 'Description').'</th>');
  echo('<th>'.GetStr($pdo, 'DateBeg').'</th>');
  echo('<th>'.GetStr($pdo, 'DateEnd').'</th>');
  echo('<th>'.GetStr($pdo, 'Status').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td align=center>");
    echo ("{$dpL['ParentId']}</td>");
    echo ("<td>");
    echo ("{$dpL['ToDoCode']}</td>");
    echo ("<td>");
    echo ("{$dpL['Description']}</td>");
    echo ("<td>");
    echo ("{$dpL['DateBeg']}</td>");
    echo ("<td>");
    echo ("{$dpL['DateEnd']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "ToDoStatus", $dpL['Status'])."</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='ToDo_LineCard.php?New=1&Id=$Id'>".GetStr($pdo, "Add")."</a>");
?>
