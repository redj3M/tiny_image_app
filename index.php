<?php

require_once 'lib/common.php';

session_start();

$pdo = getPDO();
$imgs = getAllImages($pdo);

if ($_POST) {
  $target_dir = 'uploads/';
  $target = $target_dir . basename($_FILES['file']['name']);
  $uploadOk = 1;
  if(isset($_POST['submit'])) {
    $check = getimagesize($_FILES['file']['tmp_name']);
    if ($check === false) {
      echo "File is not an image - " . $check['mime'] . '.';
      $uploadOk = 0;
    }
  }

  if (file_exists($target)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
  }

  if($uploadOk === 0) {
    echo "Sorry, no file uploaded";
  } else {
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
      addImage($pdo, $target, $_POST['title']);
    } else {
      echo "Sorry, there was an error uploading your file.";
    }
  }
}

?>

<!DOCTYPE html?>
<html>
  <head>
    <title>img app</title>
  </head>
  <body>
    <h1>img app</h1>
    <?php if (isLoggedIn()): ?>
      hi <?php echo getUserById($pdo, $_SESSION['userId']) ?>!<br />
      <h2>upload picture</h2>
      <form method="post" enctype="multipart/form-data">
        <input name="file" type="file" /><br />
        <input name="title" type="text" /><br />
        <input type="submit" />
      </form>
    <?php endif ?>
    <a href='users.php'>user galleries</a>
    <a href='view.php'>all pictures</a><br />
    <?php if (isLoggedIn()): ?>
      <a href='manage.php'>manage gallery</a>
      <a href='logout.php'>logout</a>
    <?php else: ?>
      <a href='login.php'>login</a>
      <a href='signup.php'>signup</a>
    <?php endif ?>
  </body>
</html>