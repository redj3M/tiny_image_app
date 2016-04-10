<?php

require_once "lib/common.php";
require_once "vendor/password_compat/lib/password.php";

session_start();

function checkExistance(PDO $pdo, $username){
  $stmt = $pdo->prepare(
    'SELECT id
      FROM user
      WHERE username = :username'
  );
  if ($stmt == false) {
    throw new Exception('There was a problem preparing this query');
  }
  $result = $stmt->execute(array('username' => $username));
  if ($result == false) {
    throw new Exception('There was a problem running this query');
  }
  $check = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$check['id']) {
    return false;
  }
  return true;
}

function createUser(PDO $pdo, $username, $password) {
  $errors = array();
  if ($username == '') {
    $errors[] = 'username cannot be empty';
  }
  if ($password == '') {
    $errors[] = 'password cannot be empty';
  }
  if (checkExistance($pdo, $username)) {
    $errors[] = 'user already exists';
  }
  if (!$errors) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
      'INSERT INTO user (username, password)
      VALUES (:username, :password)'
    );
    if ($stmt == false) {
      throw new Exception('There was a problem preparing this query');
    }
    $result = $stmt->execute(array('username' => $username, 'password' => $hash));
    if ($result == false) {
      throw new Exception('There was a problem running this query');
    }
  }
  return $errors;
}

if (isLoggedIn()) {
  redirectAndExit('index.php');
}

$errors = array();

if ($_POST) {
  $pdo = getPDO();

  $username = $_POST['username'];
  $password = $_POST['password'];

  $errors = createUser($pdo, $username, $password);
  if (!$errors) {
    redirectAndExit('login.php');
  }
}

?>

<!DOCTYPE html>
  <head>
    <title>img app</title>
  </head>
  <body>
    <a href="index.php"><h1>img app</h1></a>
    <h2>signup</h2>
    <form method="POST" enctype="multipart/form-data">
      <input name="username" type="text" /><br />
      <input name="password" type="password" /><br />
      <input type="submit" />
    </form>
  </body>
</html>