<?php

require_once "lib/common.php";
$pdo = getPDO();

if ($_GET) {
  $imgs = getUserImages($pdo, $_GET['usr']);
  $title = 'gallery for user: ' . getUserById($pdo, $_GET['usr']);
} else {
  $imgs = getAllImages($pdo);
  $title = 'all images';
}

?>

<!DOCTYPE html>
  <head>
    <title>img app</title>
  </head>
  <body>
    <a href="index.php"><h1>img app</h1></a>
    <h2><?php echo $title ?></h2>
    <div class="image-list">
      <?php foreach ($imgs as $img): ?>
        <h3><?php echo $img['title'] ?></h3>
        <p><em>by <?php echo getUserById($pdo, $img['author']) ?></em> @ <?php echo $img['cdate'] ?></p>
        <a href="viewimg.php?img=<?php echo $img['id'] ?>">
          <img src="<?php echo $img['ipath'] ?>" />
        </a>
      <?php endforeach ?>
    </div>
  </body>
</html>