<?php

require_once "lib/common.php";
require_once "vendor/password_compat/lib/password.php";

session_start();

if (isLoggedIn()) {
  redirectAndExit('index.php');
}

function tryLogin(PDO $pdo, $username, $password) {
  $stmt = $pdo->prepare(
    'SELECT password, id
      FROM user
      WHERE username = :username'
  );
  if ($stmt == false) {
    throw new Exception('Could not prepare iamge query');
  }
  $result = $stmt->execute(array('username' => $username));
  if ($result == false) {
    throw new Exception('Could not run iamge query');
  }
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (password_verify($password, $row['password'])) {
    return $row['id'];
  } else {
    return false;
  }
}

$error = false;
if ($_POST) {
  $pdo = getPDO();
  $try = tryLogin($pdo, $_POST['username'], $_POST['password']);

  if ($try !== false) {
    login($try);
    redirectAndExit('index.php');
  } else {
    $error = true;
  }
}

?>

<!DOCTYPE html>
  <head>
    <title>img app</title>
  </head>
  <body>
    <a href="index.php"><h1>img app</h1></a>
    <h2>login</h2>
    <?php if ($error): ?>
      <p>some of the information was incorrect</p>
    <?php endif ?>
    <form method="POST" enctype="multipart/form-data">
      <input name="username" type="text" /><br />
      <input name="password" type="password" /><br />
      <input type="submit" />
    </form>
  </body>
</html>