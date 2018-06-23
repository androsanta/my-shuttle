<?php
  // Login
  function login () {
    // global $isLogged; // actually not used 
    // global $wrongCredentials;

    $email = $_POST['email'];
    $password = $_POST['password'];

    // check for email and password format
    if (!validateEmail($email)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['error'] = true;
      $_SESSION['errorMessage'] = "Email format is not correct!";

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }

    if (!validatePassword($password)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['error'] = true;
      $_SESSION['errorMessage'] = "Password format is not correct!";

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }


    // connect to database
    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      // check for credentials (using sql function md5)
      $query = 'SELECT * FROM users WHERE email = "' . $email . '" AND password = MD5("' . $password . '");';
      echo $query;
      $res = mysqli_query($mydb, $query);
      mysqli_close($mydb);

      $error = false;

      if ($res == true && mysqli_num_rows($res) == 1) {
        $isLogged = true;
        $error = false;
        $_SESSION['email'] = $email;
        $_SESSION['routing'] = 'personalPage';
        mysqli_free_result($res);
      } else {
        $isLogged = false;
        $error = true;
      }

      $_SESSION['isLogged'] = $isLogged;
      $_SESSION['error'] = $error;
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
      $_SESSION['error'] = true;
      $_SESSION['errorMessage'] = "Email format is not correct!";

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }

    if (!validatePassword($password)) {
      $_SESSION['isLogged'] = false;
      $_SESSION['error'] = true;
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
        $_SESSION['routing'] = 'personalPage';
      } catch (Exception $e) {
        mysqli_rollback($mydb);

        $_SESSION['isLogged'] = false;
        $_SESSION['error'] = true;
        $_SESSION['errorMessage'] = "Unable to complete signup procedure";
      }

      mysqli_autocommit($mydb, true);
      mysqli_close($mydb);

      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;

    } else echo "not possible to connect to database";
  }


  function getStops ($db) {
    $query = "SELECT departure FROM users WHERE departure IS NOT NULL;";
    $res = mysqli_query($db, $query);

    $dep = array();
    $dest = array();
    $stops = array();

    if ($res) {
      $i = mysqli_num_rows($res);
      while ($i--) {
        $el = mysqli_fetch_array($res);
        array_push($dep, $el[0]);
      }
      mysqli_free_result($res);
    }

    $query = "SELECT destination FROM users WHERE destination IS NOT NULL;";
    $res = mysqli_query($db, $query);

    if ($res) {
      $i = mysqli_num_rows($res);
      while ($i--) {
        $el = mysqli_fetch_array($res);
        array_push($dest, $el[0]);
      }
      mysqli_free_result($res);
    }

    $stops = array_unique(array_merge($dep, $dest));
    asort($stops);
    return array_values($stops);
  }

  // get only the number of booking given a departure and a destination (without user details)
  function getNumberOfBooking ($db, $dep, $dest) {
    // get how many users will be on the bus during the current stops
    $query = '
      SELECT SUM(seats) 
      FROM users
      WHERE departure <= "' . $dep . '" AND destination >= "' . $dest . '";
    ';

    $nBook = "No booking for this trip";
    $res = mysqli_query($db, $query);
    if ($res) {
      $nBook = mysqli_fetch_array($res, MYSQLI_NUM);

      if ($nBook && $nBook != 0)
        $nBook = $nBook[0];
      
      mysqli_free_result($res);
    }

    return $nBook;
  }

  // get details about booking given a departure and a destination
  function getBookingDetails ($db, $dep, $dest) {
    $query = '
      SELECT seats, email
      FROM users
      WHERE departure <= "' . $dep . '" AND destination >= "' . $dest . '"
      ORDER BY email;
    ';

    $details = '';
    $res = mysqli_query($db, $query);

    if ($res) {
      $i = mysqli_num_rows($res);
      if ($i != 0)
        $details = ' -'; // just to format output

      while ($i--) {
        $line = mysqli_fetch_array($res, MYSQLI_NUM);
        $details = $details . "  " . $line[1] . " (" . $line[0] . ")";
      }
    }

    return $details;
  }

  function getUserStops ($db) {
    $arr = array();
    $arr[0] = '';
    $arr[1] = '';

    if (!isset($_SESSION['email']))
      return $arr;

    $query = '
      SELECT departure, destination
      FROM users
      WHERE email = "'. $_SESSION['email'] .'";
    ';
    $res = mysqli_query($db, $query);
    if ($res && mysqli_num_rows($res) > 0) {
      $el = mysqli_fetch_array($res);
      $arr[0] = $el[0];
      $arr[1] = $el[1];

      mysqli_free_result($res);
    }

    return $arr;
  }

  // Print overview of booking
  function bookOverview ($detailsEnabled) {
    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      $stops = getStops($mydb);
      $n = sizeof($stops);
      $i = 0;

      $currDeparture = $stops[$i];

      $userStops = getUserStops($mydb);

      while ($i++ < $n - 1) {
        $currDestination = $stops[$i];

        $bookInfo = '';
        if ($detailsEnabled)
          $bookInfo = getNumberOfBooking($mydb, $currDeparture, $currDestination) . getBookingDetails($mydb, $currDeparture, $currDestination);
        else
          $bookInfo = getNumberOfBooking($mydb, $currDeparture, $currDestination);

        $depClass = 'tableElement';
        $destClass = 'tableElement';

        if ($detailsEnabled) {
          if ($userStops[0] == $currDeparture)
            $depClass = 'tableElementDep';

          if ($userStops[1] == $currDestination)
            $destClass = 'tableElementDest';
        }

        echo 
          '<tr>
            <td>
              <div class="'. $depClass .'">
                ' . $currDeparture . '
              </div>
            </td>
            <td>
              <div class="'. $destClass .'">
                ' . $currDestination . '
              </div>
            </td>
            <td>
              <div class="tableElement">
                ' . $bookInfo . '
              </div>
            </td>
          </tr>
          ';

        $currDeparture = $currDestination;
      }

      mysqli_close($mydb);
    } else echo "not possible to connect to database";
  }


  // Book and eventually insert new addresses
  function book () {

    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $seats = $_POST['seats'];

    if ($departure >= $destination || $seats < 1 || $seats > BUS_SEATS) {
      $_SESSION['error'] = true;
      $_SESSION['errorMessage'] = "You must insert valid data for stops and seats!";
      header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
      exit;
    }


    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {

      try {
        mysqli_autocommit($mydb, false);


        $query = '
          SELECT *
          FROM users
          WHERE email = "'. $_SESSION['email'] .'" and (departure IS NOT NULL OR destination IS NOT NULL);
        ';

        $res = mysqli_query($mydb, $query);

        if (!$res)
          throw new Exception("Unable to complete booking procedure", 1);

        if (mysqli_num_rows($res) > 0)
          throw new Exception("You already have a booking scheduled", 1);
          
          

        // select and lock for update only the rows of users
        // that has a trip that intersect the new trip
        $query = '
          SELECT departure, destination, seats
          FROM users
          WHERE (departure <= "' . $departure . '" AND destination > "' . $departure . '")
            OR (departure < "' . $destination . '" AND destination >= "' . $destination . '")
            OR (departure >= "' . $departure . '" AND destination <= "' . $destination . '")
            OR (departure <= "' . $departure . '" AND destination >= "' . $destination . '")
          FOR UPDATE;
        ';
        $res = mysqli_query($mydb, $query);

        if (!$res)
          throw new Exception("Unable to complete booking procedure", 1);

        // verify if the new trip exceed the capacity of the bus
        $n = mysqli_num_rows($res);
        $i = 0;
        $entry = array();

        while ($i < $n) {
          $el = mysqli_fetch_array($res, MYSQLI_NUM);
          $entry[$i] = array();
          $entry[$i][0] = $el[0];
          $entry[$i][1] = $el[1];
          $entry[$i][2] = $el[2];
          $i++;
        }

        $stops = getStops($mydb);
        $nStop = sizeof($stops);
        $i = 0;

        $currDeparture = $stops[$i];
        while ($i++ < $nStop - 1) {
          $currDestination = $stops[$i];
          $seatsSum = 0;


          echo "currDeparture " . $currDeparture . " - currDestination " . $currDestination . "\n";

          $j = 0;
          while ($j < $n) { // enter loop only for stops that are contained into 
            echo "comparing " . $entry[$j][0] . " ". $entry[$j][1] . " ". $entry[$j][2] . "\n";

            if (strcmp($entry[$j][0], $currDeparture) <= 0
              && strcmp($entry[$j][1], $currDestination) >= 0) {
              $seatsSum = $seatsSum + $entry[$j][2];
            }
            $j++;
          }

          echo "end seatsum ". $seatsSum . "\n";

          if ($seatsSum + $seats > BUS_SEATS) {
            throw new Exception("Bus has not enough seats for this booking!", 1);
          }

          $currDeparture = $currDestination;
        }

        $query = '
          UPDATE users
          SET departure = "' . $departure . '", destination = "' . $destination . '", seats = "' . $seats . '"
          WHERE email = "' . $_SESSION['email'] . '";
        ';

        $res = mysqli_query($mydb, $query);

        if (!res)
          throw new Exception("Unable to complete booking procedure!", 1);
          
        mysqli_free_result($res);

        mysqli_commit($mydb);

        $_SESSION['error'] = true;
        $_SESSION['errorMessage'] = "Booking procedure completed successful!"; // actually this is not an error
      } catch (Exception $e) {
        mysqli_rollback($mydb);

        $_SESSION['error'] = true;
        $_SESSION['errorMessage'] = $e->getMessage();
      }

      mysqli_autocommit($mydb, true);
      mysqli_close($mydb);

    } else echo "not possible to connect to database";


    header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
    exit;
  }

  function deleteBooking () {
    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      $query = '
        UPDATE users
        SET departure = NULL, destination = NULL
        WHERE email = "'. $_SESSION['email'] .'";
      ';

      $res = mysqli_query($mydb, $query);

      if (!res) {
        $_SESSION['error'] = true;
        $_SESSION['errorMessage'] = "Unable to perform the delete operation";
      } else {
        mysqli_free_result($res);
      }

      mysqli_close($mydb);
    } else echo "not possible to connect to database";

    header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
    exit;
  }

  function printStops () {
    $mydb = mysqli_connect('localhost', 'root', '', 'my_shuttle');

    if ($mydb) {
      $arr = getStops($mydb);

      $n = sizeof($arr);
      $i = 0;
      while ($i < $n)
        echo '<option value="' . $arr[$i++] . '"></option>';

      mysqli_close($mydb);
    } else echo "not possible to connect to database";
  }

  function printSeats () {
    $i = 1;
    while ($i <= BUS_SEATS) {
      echo '<option value="' . $i . '"></option>';
      $i++;
    }
  }
?>