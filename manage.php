<?php

session_start();

require_once "lib/common.php";

$pdo = getPDO();

if (!isLoggedIn()) {
  redirectAndExit('index.php');
}

if ($_POST) {
  $toDelete = getImageById($pdo, array_keys($_POST['delete'])[0]);
  if ($toDelete['author'] === $_SESSION['userId']) {
    removeImage($pdo, array_keys($_POST['delete'])[0]);
    redirectAndExit('manage.php');
  }
}

$imgs = getUserImages($pdo, $_SESSION['userId']);

?>

<!DOCTYPE html>
  <head>
    <title>img app</title>
  </head>
  <body>
    <a href="index.php"><h1>img app</h1></a>
    <h2>manage gallery</h2>
    <form method="POST">
      <table style="border: 1px solid black">
        <tr style="border: 1px solid black">
          <th>name</th>
          <th>date</th>
          <th>thumbnail</th>
          <th>delete</th>
        </tr>
        <?php foreach ($imgs as $img): ?>
          <tr style="border: 1px solid black">
            <td style="border: 1px solid black"><?php echo $img['title'] ?></td>
            <td style="border: 1px solid black"><?php echo $img['cdate'] ?></td>
            <td style="border: 1px solid black"><img style="width: 100px" src="<?php echo $img['ipath'] ?>" /></td>
            <td style="border: 1px solid black"><input name="delete[<?php echo $img['id'] ?>]" type="submit" value="delete" /></td>
          </tr>
        <?php endforeach ?>
      </table>
    </form>
  </body>
</html>