<?php
  // BUS SEATS AVAILABILITY
  define('BUS_SEATS', 20);
  // define('MYSQLI_USERNAME', 's251579');
  define('MYSQLI_USERNAME', 'root');
  // define('MYSQLI_PASSWORD', 'stakedle');
  define('MYSQLI_PASSWORD', '');
  // define('MYSQLI_DBNAME', 's251579');
  define('MYSQLI_DBNAME', 'my_shuttle');
  define('MYSQLI_HOST', 'localhost');


  /*** Validation functions ***/
  function validateEmail ($str) {
    $re = "/[a-zA-Z0-9]+\@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/";
    return preg_match($re, $str);
  }

  function validatePassword ($str) {
    $lower = "/[a-z]+/";
    $upper = "/[A-Z]+/";
    $digit = "/[0-9]+/";
    return preg_match($lower, $str) && (preg_match($upper, $str) || preg_match($digit, $str));
  }

  function validate ($email, $password, &$errorMessage) {
    // prevent php code injection and check for email and password format

    if ($email != strip_tags($email) || !validateEmail($email)) {
      $errorMessage = "Email format is not correct!";
      return false;
    }

    if ($password != strip_tags($password) || !validatePassword($password)) {
      $errorMessage = "Password format is not correct!";
      return false;
    }

    return true;
  }

  function validateBook ($dep, $dest, $seats, &$msg) {
    if ($dep != strip_tags($dep)) {
      $msg = 'Departure value has wrong format';
      return false;
    }

    if ($dest != strip_tags($dest)) {
      $msg = 'Destination value has wrong format';
      return false;
    }

    if (strcmp($dep, $dest) >= 0) {
      $msg = 'Departure must precede destination!';
      return false;
    }

    if ($seats != strip_tags($seats) || $seats < 1 || $seats > BUS_SEATS) {
      $msg = 'Seats must be a value between 1 and ' . BUS_SEATS . "!";
      return false;
    }

    return true;
  }
  

  // start timed session and eventually redirect to https
  function my_session_start () {
    session_start();

    if (isset($_SESSION['expire_time']) && time() > $_SESSION['expire_time']) {
      session_unset();
      session_destroy();
      session_start();
    }

    $_SESSION['expire_time'] = time() + 120;

    // check for https
    if($_SERVER['SERVER_PORT'] !== 443 && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
      header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
      exit;
    }
  }
?>