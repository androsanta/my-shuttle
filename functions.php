<?php
  // Validation functions
  function validateEmail ($str) {
    $re = "/[a-zA-Z0-9]+\@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/";
    return preg_match($re, $str);
  }

  function validatePassword ($str) {
    $lower = "/[a-z]+/";
    $upper = "/[A-Z]+/";
    $digit = "/[0-9]+/";
    echo preg_match($upper, $str);
    return preg_match($lower, $str) && (preg_match($upper, $str) || preg_match($digit, $str));
  }

  // Login
  function login () {
    // global $isLogged; // actually not used 
    // global $wrongCredentials;

    $email = $_POST['email'];
    $password = $_POST['password'];

    // check for email and password format
    if (!validateEmail($email)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['userError'] = true;
      $_SESSION['errorMessage'] = "Email format is not correct!";

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }

    if (!validatePassword($password)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['userError'] = true;
      $_SESSION['errorMessage'] = "Password format is not correct!";

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }


    // connect to database
    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      // check for credentials (using sql function md5)
      $query = 'SELECT * FROM users WHERE email = "' . $email . '" AND password = MD5("' . $password . ')";';
      $res = mysqli_query($mydb, $query);
      mysqli_close($mydb);

      $error = false;

      if ($res == true && mysqli_num_rows($res) == 1) {
        $isLogged = true;
        $error = false;
        $_SESSION['email'] = $email;
        mysqi_free_result($res);
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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // check for email and password format
    if (!validateEmail($email)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['userError'] = true;
      $_SESSION['errorMessage'] = "Email format is not correct!";

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }

    if (!validatePassword($password)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['userError'] = true;
      $_SESSION['errorMessage'] = "Password format is not correct!";

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }

    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      try {
        mysqli_autocommit($mydb, false);

        $query = 'INSERT INTO users (email, password) VALUES("' . $email . '", MD5("' . $password . '"));';
        $res = mysqli_query($mydb, $query);
        if (!$res)
          throw new Exception("Insertion of new user failed", 1);
        mysqli_free_result($res);

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