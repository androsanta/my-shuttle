<?php
  define("BUS_SEATS", 20);
  include("functions.php");
  include('common.php');

  my_session_start();

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

  $error = false;
  $errorMessage = '';

  $routing = 'home';


  if (!empty($_SESSION)) {
    foreach ($_SESSION as $key => $value) {
      switch ($key) {
        case 'isLogged':
          $isLogged = $value;
          break;
        case 'error':
          $error = $value;
          $_SESSION['error'] = false;
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
      if ($key != "" && $key == "action" && $value != "")
        $action = $value;
    }
  }

  switch ($action) {
    case 'signup':
      if (!$isLogged)
        signup();
      break;
    case 'book':
      if ($isLogged)
        book();
      break;
    case 'deleteBooking':
      if ($isLogged)
        deleteBooking();
      break;
    case 'home':
    case 'personalPage':
      $_SESSION['routing'] = $action;
      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
  }

?>