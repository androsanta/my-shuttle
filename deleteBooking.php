<?php
  include('common.php');

  // Delete booking
  function deleteBooking () {
    // connect to database
    $mydb = new mysqli('localhost', 'root', '', 'my_shuttle');

    if (!mysqli_connect_errno()) {
      $query = '
        UPDATE users
        SET departure = NULL, destination = NULL
        WHERE email = ?;
      ';

      $stmt = $mydb->prepare($query);
      $stmt->bind_param('s', $_SESSION['email']);
      $res = $stmt->execute();

      if (!$res) {
        $_SESSION['error'] = true;
        $_SESSION['errorMessage'] = "Unable to perform delete operation";
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
  if (isset($_SESSION) && isset($_SESSION['isLogged']) && $_SESSION['isLogged'])
    deleteBooking();
  else {
    $_SESSION['error'] = true;
    $_SESSION['errorMessage'] = "To delete a booking you must be logged in!";

    header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
    exit;
  }
?>