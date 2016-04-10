<?php

require_once 'lib/common.php';

function installBlog() {
  $root = getRootPath();
  $database = getDatabasePath();

  $error = '';

  if(is_readable($database) && filesize($database) > 0) {
    $error = 'please delete the existing database manually before installing it afresh';
  }

  if (!$error) {
    $createdOk = @touch($database);
    if (!$createdOk) {
      $error = sprintf(
        'Could not create the database, please allow the server to create new files in \'%s\'',
        dirname($database)
      );
    }
  }

  if (!$error) {
    $sql = file_get_contents($root . '/data/init.sql');
    if ($sql === false) {
      $error = 'Cannot find sql file';
    }
  }

  if(!$error) {
    $pdo = getPDO();
    $result = $pdo->exec($sql);
    if ($result === false) {
      $error = 'Could not run SQL: ' . print_r($pdo->errorInfo(), true);
    }
  }

  $count = null;
  if (!$error) {
    $sql = "SELECT COUNT * AS c FROM img";
    $stmt = $pdo->query($sql);
    if ($stmt) {
      $count = $stmt->fetchCollumn;
    }
  }

  return array($count, $error);
}

session_start();

if ($_POST) {
  list($_SESSION['count'], $_SESSION['error']) = installBlog();
}


$attempted = false;
if ($_SESSION) {
  $attempted = true;
  $count = $_SESSION['count'];
  $error = $_SESSION['error'];
  unset($_SESSION['count']);
  unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html>
  <head>
    <title>img storage app</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <style type="text/css">
      .box {
        border: 1px dotted silver;
        border-radius: 5px;
        padding: 4px;
      }
      .error {
        background-color: #ff6666;
      }
      .success {
        background-color: #88ff88;
      }
    </style>
  </head>
  <body>
    <?php if ($attempted): ?>
      <?php if ($error): ?>
        <div class="error box">
          <?php echo $error ?>
        </div>
      <?php else: ?>
        <div class="success box">
          <?php echo $count ?> new rows were created.
        </div>
      <?php endif ?>
    <?php else: ?>
      <p>Click the install button to reset the database.</p>
      <form method="post">
        <input name="install" type="submit" value="Install" />
      </form>
    <?php endif ?>
  </body>
</html>