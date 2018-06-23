<?php
  include('common.php');

  // Logout
  function logout () {
    my_session_start();

    session_unset();
    session_destroy();
    
    header('Location: https://' . $_SERVER['HTTP_HOST'] . "/my-shuttle");
    exit;
  }

  logout();
?>