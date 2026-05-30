<?php
$pdo=new PDO('sqlite:C:\xampp\htdocs\Connect\database\database.sqlite');
$stmt=$pdo->query("PRAGMA table_info('users')");
$cols=$stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($cols as $c){ echo $c['cid'] . "\t" . $c['name'] . "\t" . $c['type'] . PHP_EOL; }
