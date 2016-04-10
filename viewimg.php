<?php

require_once 'lib/common.php';

session_start();

if (isset($_GET['img'])) {
  $imgId = $_GET['img'];
} else {
  $imgId = 0;
}

$pdo = getPDO();
$img = getImageById($pdo, $imgId);

if ($_POST) {
  addComment($pdo, $imgId, $_POST['name'], $_POST['body']);
}

?>


<!DOCTYPE html>
<html>
  <head>
    <title>img app</title>
  </head>
  <body>
    <h1><a href="index.php">img app</a></h1>
    <h2><?php echo $img['title'] ?></h2>
    <p><em>by <?php echo getUserById($pdo, $img['author']) ?></em> @ <?php echo $img['cdate'] ?></p>
    <img src="<?php echo $img['ipath'] ?>" />
    <h3>Comments</h3>
    <?php foreach (getImageComments($pdo, $imgId) as $comment): ?>
      <h4>
        Comment from <?php echo hesc($comment['name']) ?>
        on <?php echo $comment['cdate'] ?>
      </h4>
      <p><?php echo $comment['body'] ?></p>
    <?php endforeach ?>
    <h3>Add comment</h3>
      <form method="POST">
        <label for="name">
          Name:
        </label>
        <input id="name" name="name" type="text" /><br />
        <label for="body">
          Comment:
        </label>
        <textarea id="body" name="body" rows"8" cols="70"></textarea></br>
        <input type="submit" />
      </form>
  </body>
</html>