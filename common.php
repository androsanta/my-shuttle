<?php
  // check for https
  if($_SERVER['SERVER_PORT'] !== 443 && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
  }

  ini_set('session.gc_maxlifetime', 120);  
  session_start();

  /*
  * used to perform action and decide what to render on screen
  * $action possible values are:
  * home - user not logged in
  * login - user that already have an account want to login
  * signup - user does not have an account, signup and login
  * logout - user want to logout 
  */
  $action = "home";
  $isLogged = false;
  $wrongCredentials = false;


  if (!isset($_SESSION['my_shuttle'])) {
    $_SESSION['my_shuttle'] = array();
  }

  if (!empty($_GET)) {
    // check which action should be performed
    foreach ($_GET as $key => $value) {
      if ($key != "" && $key == "action" && $value != "")
        $action = $value;
    }
  }

  if ($action == "login") {
    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      // mysqli_select_db($mydb, 'my_shuttle');

      $query = 'SELECT * FROM users WHERE email = "'.$_POST['email'].'" AND password = "'.$_POST['password'].'";';
      $res = mysqli_query($mydb, $query);

      if ($res == true && mysqli_num_rows($res) == 1) {
        $isLogged = true;
        $_SESSION['email'] = $_POST['email'];
      } else {
        $isLogged = false;
        $wrongCredentials = true;
      }

      mysqli_close($mydb);
    } else echo "not possible to connect to database";
  }
?>