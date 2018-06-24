<?php
  include('common.php');
  include('utilityFunctions.php');

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

?>