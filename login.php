<?php
  include('common.php');

  // Login
  function login () {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!validate($email, $password, $msg)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['error'] = true;
      $_SESSION['errorMessage'] = $msg;

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }


    // connect to database
    $mydb = new mysqli('localhost', 'root', '', 'my_shuttle');

    if (!mysqli_connect_errno()) {
      // check for credentials (using sql function md5)
      $query = '
        SELECT email
        FROM users
        WHERE email = ? AND password = MD5(?);
      ';

      $stmt = $mydb->prepare($query);
      $stmt->bind_param('ss', $email, $password);
      $res = $stmt->execute();
      $stmt->store_result();

      if ($res && $stmt->num_rows == 1) {
        $_SESSION['isLogged'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['routing'] = 'personalPage';
      } else {
        $_SESSION['isLogged'] = false;
        $_SESSION['error'] = true;
        $_SESSION['errorMessage'] = "Wrong Credentials";
      }

      $stmt->free_result();
      $stmt->close();
      $mydb->close();
    } else {
      $_SESSION['error'] = true;
      $_SESSION['errorMessage'] = "Error while accessing to database";
    }

    header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
    exit;
  }


  my_session_start();
  login();
?>