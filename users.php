<?php

require_once "lib/common.php";
$pdo = getPDO();
$users = getAllUsers($pdo);

?>

<!DOCTYPE html>
  <head>
    <title>img app</title>
  </head>
  <body>
    <a href="index.php"><h1>img app</h1></a>
    <h2>user galleries</h2>
    <ul>
      <?php foreach ($users as $user): ?>
        <li><a href="view.php?usr=<?php echo $user['id'] ?>"><?php echo $user['username'] ?></a></li>
      <?php endforeach ?>
    </ul>
  </body>
</html>