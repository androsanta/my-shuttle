<?php
  
  // Get all stops that bus has to do
  function getStops ($db) {
    $dep = array();
    $dest = array();
    $stops = array();

    // Get all departure from users in db
    $query = "SELECT departure FROM users WHERE departure IS NOT NULL;";
    $res = $db->query($query);

    if ($res) {
      $i = $res->num_rows;
      while ($i--) {
        $el = $res->fetch_array(MYSQLI_NUM);
        array_push($dep, $el[0]);
      }

      $res->free();
    }


    // Get all destination from users in db
    $query = "SELECT destination FROM users WHERE destination IS NOT NULL;";
    $res = $db->query($query);

    if ($res) {
      $i = $res->num_rows;
      while ($i--) {
        $el = $res->fetch_array(MYSQLI_NUM);
        array_push($dest, $el[0]);
      }

      $res->free();
    }

    // merge the two array obtained
    $stops = array_unique(array_merge($dep, $dest));
    asort($stops);
    return array_values($stops);
  }

  // Print all stops using the option tag, that will be used by datalist
  function printStops () {
    $mydb = new mysqli(MYSQLI_HOST, MYSQLI_USERNAME, MYSQLI_PASSWORD, MYSQLI_DBNAME);

    if (!mysqli_connect_errno()) {
      $arr = getStops($mydb);
      $i = 0;
      $n = sizeof($arr);

      while ($i < $n)
        echo '<option value="' . $arr[$i++] . '"></option>';

      $mydb->close();
    } else {
      header("Location: dbUnavailable.php");
      exit;
    }
  }

  // Print all possible seats value, using the option tag, that will be used by datalist
  function printSeats () {
    $i = 0;

    while ($i++ <= BUS_SEATS)
      echo '<option value="' . $i . '"></option>';
  }


  // Get only the number of booking given a departure and a destination (without user details)
  function getNumberOfBooking ($db, $dep, $dest) {
    $nBook = 0;
    $query = '
      SELECT SUM(seats) 
      FROM users
      WHERE departure <= ? AND destination >= ?;
    ';

    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $dep, $dest);
    $res = $stmt->execute();

    $val = '';
    $stmt->bind_result($val);

    if ($res) {
      $stmt->fetch();

      if ($val)
        $nBook = $val;
    }

    $stmt->close();
    return $nBook;
  }

  // Get details about booking given a departure and a destination
  function getBookingDetails ($db, $dep, $dest) {
    $query = '
      SELECT seats, email
      FROM users
      WHERE departure <= "' . $dep . '" AND destination >= "' . $dest . '"
      ORDER BY email;
    ';

    $details = '';
    $res = $db->query($query);

    if ($res) {
      $i = $res->num_rows;
      if ($i != 0)
        $details = ' -'; // just to format output

      while ($i--) {
        $line = $res->fetch_array(MYSQLI_NUM);
        $details = $details . "  " . $line[1] . " (" . $line[0] . ")";
      }

      $res->free();
    }

    return $details;
  }

  // Get stops booked by user, if any
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
    $res = $db->query($query);
    if ($res && $res->num_rows > 0) {
      $el = $res->fetch_array(MYSQLI_NUM);
      $arr[0] = $el[0];
      $arr[1] = $el[1];

      $res->free();
    }

    return $arr;
  }

  // Print overview of booking, with or without user details
  function bookOverview ($detailsEnabled) {
    $mydb = new mysqli(MYSQLI_HOST, MYSQLI_USERNAME, MYSQLI_PASSWORD, MYSQLI_DBNAME);

    if (!mysqli_connect_errno()) {
      $stops = getStops($mydb);

      $n = sizeof($stops);
      if ($n == 0) {
        $mydb->close();
        return;
      }

      $i = 0;

      $currDeparture = $stops[$i];
      $userStops = getUserStops($mydb);

      while ($i++ < $n - 1) {
        $currDestination = $stops[$i];

        $depClass = 'tableElement';
        $destClass = 'tableElement';
        $bookInfo = getNumberOfBooking($mydb, $currDeparture, $currDestination) . "/" . BUS_SEATS;

        if ($bookInfo == 0)
          $bookInfo = $bookInfo .  " - No booking for this trip";

        if ($detailsEnabled) {
          $bookInfo = $bookInfo . getBookingDetails($mydb, $currDeparture, $currDestination);
          if ($userStops[0] == $currDeparture) {
            $depClass = 'tableElementCurrent';
          }

          if ($userStops[1] == $currDestination) {
            $destClass = 'tableElementCurrent';
          }
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

      $mydb->close();
    } else {
      header("Location: dbUnavailable.php");
      exit;
    }
  }
?>