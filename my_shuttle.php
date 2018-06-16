<?php include("common.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>My Shuttle</title>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="common.js"></script>
  <?php if ($wrongCredentials)
    echo '<script type="text/javascript">alert("Wrong credentials")</script>';
  ?>
  <link href="styles.css" rel="stylesheet" />
</head>
<body>
  <div class="container">
    <div class="header">
    <div class="title">
      <h2 class="headerColored">My</h2>
      <h2 class="header">Shuttle</h2>
    </div>

    <!-- Header, show login form or welcome message if user is logged in -->
    <?php if (!$isLogged): ?>
      <form action="my_shuttle.php?action=login" method="POST" class="login">
        <div class="email">
          <input id="email" type="text" name="email" placeholder="Email" class="email">
        </div>
        <div class="password">
          <input id="psw" type="password" name="password" class="password" placeholder="Password">
        </div>
        <input type="submit" name="login" value="Login" class="submit" />
      </form>
    <?php else: ?>
      <div class="welcome">
        <h3 class="welcome">Welcome</h3>
        <?php echo '<h3 class="welcomeColored">'.$_SESSION['email'].'</h3>'; ?>
        <form action="my_shuttle.php?action=logout" method="GET">
          <input type="submit" name="logout" value="Logout" class="logout" />
        </form>
      </div>
    <?php endif; ?>

  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <form action="my_shuttle.php?action=signup" method="GET" class="menuEntry">
      <input type="submit" name="signup" value="Signup" class="menuEntry" />
    </form>
    <form action="phpinfo.php" method="GET" class="menuEntry">
      <input type="submit" name="phpinfo" value="Phpinfo" class="menuEntry" />
    </form>
  </div>

  </div>
  <noscript>
    Sorry: Your browser does not support or has
    disabled javascript
  </noscript>
</body>
</html>