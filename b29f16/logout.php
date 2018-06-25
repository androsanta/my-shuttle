<?php
  include('common.php');

  // Logout
  function logout () {
    my_session_start();

    session_unset();
    session_destroy();
    
    header('Location: index.php');
    exit;
  }

  logout();
?>