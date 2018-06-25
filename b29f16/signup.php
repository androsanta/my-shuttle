<?php
  include('common.php');

  // Signup
  function signup () {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $msg = '';

    if (!validate($email, $password, $msg)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['error'] = true;
      $_SESSION['errorMessage'] = $msg;

      header('Location: index.php');
      exit;
    }


    // connect to database
    $mydb = new mysqli(MYSQLI_HOST, MYSQLI_USERNAME, MYSQLI_PASSWORD, MYSQLI_DBNAME);

    if (!mysqli_connect_errno()) {
      $query = '
        INSERT INTO users (email, password)
        VALUES (?, MD5(?));
      ';

      $stmt = $mydb->prepare($query);
      $stmt->bind_param('ss', $email, $password);
      $res = $stmt->execute();

      if ($res) {
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

    header('Location: index.php');
    exit;
  }


  my_session_start();
  signup();
?>