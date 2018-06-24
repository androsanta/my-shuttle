<?php
  include('common.php');
  include('utilityFunctions.php');

  // Book and eventually insert new addresses
  function book () {
    $email = $_SESSION['email'];
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $seats = $_POST['seats'];
    $msg = '';

    // Validate received input
    if (!validateBook($departure, $destination, $seats, $msg)) {
      $_SESSION['error'] = true;
      $_SESSION['errorMessage'] = $msg;

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }


    $mydb = new mysqli('localhost', 'root', '', 'my_shuttle');

    if (!mysqli_connect_errno()) {

      try {
        $mydb->autocommit(false);

        // Check if the user has already a booking
        $query = '
          SELECT *
          FROM users
          WHERE email = ? and (departure IS NOT NULL OR destination IS NOT NULL);
        ';

        $stmt = $mydb->prepare($query);
        $stmt->bind_param('s', $email);
        $res = $stmt->execute();
        $stmt->store_result();

        if (!$res)
          throw new Exception("Unable to complete booking procedure", 1);

        if ($stmt->num_rows > 0)
          throw new Exception("You already have a booking scheduled", 1);

        $stmt->free_result();
        $stmt->close();


        /*$query = '
          SELECT departure, destination, seats
          FROM users
          WHERE (departure <= "' . $departure . '" AND destination > "' . $departure . '")
            OR (departure < "' . $destination . '" AND destination >= "' . $destination . '")
            OR (departure >= "' . $departure . '" AND destination <= "' . $destination . '")
            OR (departure <= "' . $departure . '" AND destination >= "' . $destination . '")
          FOR UPDATE;
        ';*/


        // Select and lock for update only the rows of users
        // that has a trip that intersect the new trip
        $query = '
          SELECT departure, destination, seats
          FROM users
          WHERE (departure <= ? AND destination > ?)
            OR (departure < ? AND destination >= ?)
            OR (departure >= ? AND destination <= ?)
            OR (departure <= ? AND destination >= ?)
          FOR UPDATE;
        ';

        $stmt = $mydb->prepare($query);
        $stmt->bind_param(
          'ssssssss',
          $departure, $departure,
          $destination, $destination,
          $departure, $destination,
          $departure, $destination
        );
        $res = $stmt->execute();

        if (!$res)
          throw new Exception("Unable to complete booking procedure", 1);

        $resDep = '';
        $resDest = '';
        $resSeat = 0;
        $stmt->bind_result($resDep, $resDest, $resSeat);

        $i = 0;
        $entry = array();


        // Save the query result into an array
        while ($stmt->fetch()) {
          $entry[$i] = array();
          $entry[$i][0] = $resDep;
          $entry[$i][1] = $resDest;
          $entry[$i][2] = $resSeat;
          $i++;
        }

        $n = $i;

        $stmt->close();

        print_r($entry);


        // Verify if the new trip exceed the capacity of the bus
        $stops = getStops($mydb);
        $nStop = sizeof($stops);
        $i = 0;

        $currDeparture = $stops[$i];
        while ($i++ < $nStop - 1) {
          $currDestination = $stops[$i];
          $seatsSum = 0;

          $j = 0;
          while ($j < $n) {
            if (strcmp($entry[$j][0], $currDeparture) <= 0
              && strcmp($entry[$j][1], $currDestination) >= 0) {
              $seatsSum = $seatsSum + $entry[$j][2];
            }
            $j++;
          }

          if ($seatsSum + $seats > BUS_SEATS) {
            throw new Exception("Bus has not enough seats for this booking!", 1);
          }

          $currDeparture = $currDestination;
        }


        // Update booking values for the user
        $query = '
          UPDATE users
          SET departure = ?, destination = ?, seats = ?
          WHERE email = ?;
        ';

        $stmt = $mydb->prepare($query);
        $stmt->bind_param('ssss', $departure, $destination, $seats, $email);
        $res = $stmt->execute();

        if (!$res)
          throw new Exception("Unable to complete booking procedure!", 1);
          
        $stmt->close();
        $mydb->commit();

        $_SESSION['error'] = true; // actually this is not an error
        $_SESSION['errorMessage'] = "Booking procedure completed successful!";
      } catch (Exception $e) {
        $mydb->rollback();

        $_SESSION['error'] = true;
        $_SESSION['errorMessage'] = $e->getMessage();
      }

      $mydb->autocommit(true);
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
    book();
  else {
    $_SESSION['error'] = true;
    $_SESSION['errorMessage'] = "To book a trip you must be logged in!";

    header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
    exit;
  }
?>