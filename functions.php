<?php

  // Login
  function login () {
    // global $isLogged; // actually not used 
    // global $wrongCredentials;

    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      $query = 'SELECT * FROM users WHERE email = "' . $_POST['email'] . '" AND password = "' . $_POST['password'] . '";';
      $res = mysqli_query($mydb, $query);
      mysqli_close($mydb);

      $error = false;

      if ($res == true && mysqli_num_rows($res) == 1) {
        $isLogged = true;
        $error = false;
        $_SESSION['email'] = $_POST['email'];
      } else {
        $isLogged = false;
        $error = true;
      }

      $_SESSION['isLogged'] = $isLogged;
      $_SESSION['userError'] = $error;
      $_SESSION['errorMessage'] = "Wrong Credentials";

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;

    } else echo "not possible to connect to database";
  }


  // Logout
  function logout () {
    echo "logout";
    session_unset();
    session_destroy();
    session_start();
    
    header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
    exit;
  }


  // Signup
  function signup () {
    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      try {
        mysqli_autocommit($mydb, false);

        $query = 'INSERT INTO users (email, password) VALUES("' . $_POST['email'] . '", "' . $_POST['password'] . '");';
        if (!mysqli_query($mydb, $query))
          throw new Exception("Insertion of new user failed", 1);

        mysqli_commit($mydb);

        $_SESSION['isLogged'] = true;
        $_SESSION['email'] = $_POST['email'];
      } catch (Exception $e) {
        mysqli_rollback($mydb);

        $_SESSION['isLogged'] = false;
        $_SESSION['userError'] = true;
        $_SESSION['errorMessage'] = "Unable to complete signup procedure";
      }

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;

    } else echo "not possible to connect to database";
  }
?>