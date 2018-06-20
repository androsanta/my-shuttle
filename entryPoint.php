<?php
  session_start();

  if (isset($_SESSION['expire_time']) && time() > $_SESSION['expire_time']) {
    session_unset();
    session_destroy();
    session_start();
  }

  $_SESSION['expire_time'] = time() + 120;

  // check for https
  if($_SERVER['SERVER_PORT'] !== 443 && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
  }

  define("BUS_SEATS", 20);
  include("functions.php");

  /*
  * used to perform action and decide what to render on screen
  * $action possible values are:
  * home - user not logged in
  * login - user that already have an account want to login
  * signup - user does not have an account, signup and login
  * logout - user want to logout 
  * personalPage - view of booking with user details
  */
  $action = "";
  $isLogged = false;

  $userError = false;
  $errorMessage = '';

  $routing = 'home';


  if (!empty($_SESSION)) {
    foreach ($_SESSION as $key => $value) {
      switch ($key) {
        case 'isLogged':
          $isLogged = $value;
          break;
        case 'userError':
          $userError = $value;
          $_SESSION['userError'] = false;
          $errorMessage = $_SESSION['errorMessage'];
          break;
        case 'routing':
          $routing = $value;
          break;
      }
    }
  }

  if (!empty($_GET)) {
    // check which action should be performed
    foreach ($_GET as $key => $value) {
      echo $key . " " . $value . "\n";
      if ($key != "" && $key == "action" && $value != "")
        $action = $value;
    }
  }

  switch ($action) {
    case 'login':
      if (!$isLogged)
        login();
      break;
    case 'signup':
      if (!$isLogged)
        signup();
      break;
    case 'logout':
      logout();
      break;
    case 'book':
      if ($isLogged)
        book();
      break;
    case 'home':
    case 'personalPage':
      $_SESSION['routing'] = $action;
      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
  }

?>