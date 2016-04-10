<?php

function getRootPath() {
  return realpath(__DIR__ . "/..");
}

function getDatabasePath() {
  return getRootPath() . '/data/data.sqlite';
}

function getDsn() {
  return 'sqlite:'. getDatabasePath();
}

function getPDO() {
  $pdo = new PDO(getDsn());
  $result = $pdo->query('PRAGMA foreign_keys = ON');

  if ($result === false) {
    throw new Exception('Could not turn on foreign key constraints');
  }
  return $pdo;
}

function getCurrentDate() {
  return date('Y-m-d H:i:s');
}

function redirectAndExit($script) {
  $relativeUrl = $_SERVER['PHP_SELF'];
  $urlFolder = substr($relativeUrl, 0, strrpos($relativeUrl, '/') + 1);

  $host = $_SERVER['HTTP_HOST'];
  $fullUrl = 'http://' . $host . $urlFolder . $script;
  header('Location: ' . $fullUrl);
  exit();
}

function hesc($str) {
  return htmlspecialchars($str, ENT_HTML5, 'UTF-8');
}

function getAllImages(PDO $pdo) {
  $stmt = $pdo->query(
    'SELECT id, title, ipath, cdate, author
      FROM img'
  );
  if ($stmt == false) {
    throw new exception('There was a problem running this query');
  }
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserImages(PDO $pdo, $userId) {
  $stmt = $pdo->prepare(
    'SELECT id, title, ipath, cdate, author
      FROM img
      WHERE author = :author'
  );
  $result = $stmt->execute(array('author' => $userId));
  if ($result === false) {
    throw new Exception('Could not run query');
  }
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getImageById(PDO $pdo, $id) {
  $stmt = $pdo->prepare(
    'SELECT id, cdate, title, ipath, author
      FROM img
      WHERE id = :id'
  );
  if ($stmt == false) {
    throw new Exception('Could not prepare iamge query');
  }
  $result = $stmt->execute(array('id' => $id));
  if ($result === false) {
    throw new Exception('Could not run query');
  }
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addImage(PDO $pdo, $path, $title) {
  if (!isset($_SESSION['userId'])) {
    redirectAndExit('index.php');
  } else {
    $sql = 
      'INSERT INTO img (cdate, title, ipath, author)
        VALUES (:cdate, :title, :ipath, :author)';
    $stmt = $pdo->prepare($sql);
    if ($stmt == false) {
      throw new Exception('Could not prepare upload query');
    }

    $result = $stmt->execute(array(
      'cdate' => getCurrentDate(),
      'title' => $title,
      'ipath' => $path,
      'author' => $_SESSION['userId']
    ));
    if ($result === false) {
      throw new Exception('Could not run query');
    }

    return $pdo->lastInsertId();
  }
}

function removeImage(PDO $pdo, $imgId) {

  $stmt = $pdo->prepare(
    'SELECT ipath
      FROM img
      WHERE id = :id'
  );
  if ($stmt == false) {
    throw new Exception('Could not prepare delete query');
  }
  $result = $stmt->execute(array('id' => $imgId));
  if ($result === false) {
    throw new Exception('Could not run query');
  }
  $path = $stmt->fetchColumn();

  if ($result === false) {
    throw new Exception('Could not run query');
  }
  $stmt = $pdo->prepare(
    'DELETE FROM img
      WHERE id = :id'
  );
  if ($stmt == false) {
    throw new Exception('Could not prepare delete query');
  }
  $result = $stmt->execute(array('id' => $imgId));
  if ($result === false) {
    throw new Exception('Could not run query');
  }

  unlink(getRootPath() . '/' . $path);
}

function getImageComments(PDO $pdo, $id){
  $stmt = $pdo->prepare(
    'SELECT *
      FROM comment
      WHERE img = :img'
  );
  if ($stmt == false) {
    throw new Exception('Could not prepare query');
  }
  $result = $stmt->execute(array('img' => $id));
  if ($result === false) {
    throw new Exception('Could not run query');
  }

  return $stmt->fetchAll(PDO::FETCH_ASSOC);

}

function addComment(PDO $pdo, $id, $name, $body){
  $stmt = $pdo->prepare(
    'INSERT INTO comment (img, cdate, name, body)
      VALUES (:img, :cdate, :name, :body)'
  );
  if ($stmt == false) {
    throw new Exception('Could not prepare query');
  }
  $result = $stmt->execute(array(
    'img' => $id,
    'cdate' => getCurrentDate(),
    'name' => $name,
    'body' => $body
  ));
  if ($result === false) {
    throw new Exception('Could not run query');
  }

  return $pdo->lastInsertId();
}

function getAllUsers(PDO $pdo) {
  $stmt = $pdo->query('SELECT id, username FROM user');
  if ($stmt == false) {
    throw new exception('There was a problem running this query');
  }
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isLoggedIn() {
  return isset($_SESSION['userId']);
}

function login($userId) {
  session_regenerate_id();
  $_SESSION['userId'] = $userId;
}

function logout() {
  unset($_SESSION['userId']);
}

function getUserById(PDO $pdo, $userId) {
  $stmt = $pdo->prepare(
    'SELECT username
      FROM user
      WHERE id = :id'
  );
  if ($stmt == false) {
    throw new Exception('Could not prepare query');
  }
  $result = $stmt->execute(array('id' => $userId));
  if ($result === false) {
    throw new Exception('Could not run query');
  }

  return $stmt->fetchColumn();
}

?>