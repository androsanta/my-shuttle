<?php
  include('common.php');

  my_session_start();

  if (isset($_POST['routing'])) {
    switch ($_POST['routing']) {
      case 'Personal':
        $_SESSION['routing'] = 'personalPage';
        break;
      
      default:
        $_SESSION['routing'] = 'home';
        break;
    }
  }

  header('Location: index.php');
  exit;
?>