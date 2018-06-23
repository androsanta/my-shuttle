<?php
  include('common.php');

  // Signup
  function signup () {
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
      $query = '
        INSERT INTO users (email, password)
        VALUES (?, MD5(?));
      ';

      $stmt = $mydb->prepare($query);
      $stmt->bind_param('ss', $email, $password);
      $res = $stmt->execute();

      if ($res && $stmt->affected_rows() == 1) {
        $_SESSION['isLogged'] = true;
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['routing'] = 'personalPage';
      } else {
        $_SESSION['isLogged'] = false;
        $_SESSION['error'] = true;
        $_SESSION['errorMessage'] = "Unable to complete signup procedure";
      }

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
  signup();
?>