<?php include("entryPoint.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>My Shuttle</title>
  <!--
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  -->
  <script type="text/javascript" src="jquery-1.7.2.js"></script>
  <script type="text/javascript" src="common.js"></script>
  <?php if ($userError)
    echo '<script type="text/javascript">alert("' . $errorMessage . '")</script>';
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
          <input id="emailLogin" type="text" name="email" placeholder="Email" class="email">
        </div>
        <div class="password">
          <input id="pswLogin" type="password" name="password" class="password" placeholder="Password">
        </div>
        <input type="submit" name="login" value="Login" class="submit" />
      </form>
    <?php else: ?>
      <div class="welcome">
        <h3 class="welcome">Welcome</h3>
        <?php echo '<h3 class="welcomeColored">'.$_SESSION['email'].'</h3>'; ?>
        <form action="my_shuttle.php?action=logout" method="POST">
          <input type="submit" name="logout" value="Logout" class="logout" />
        </form>
      </div>
    <?php endif; ?>

  </div>

  <div class="content">

    <!-- Sidebar -->
    <div class="sidebar">
      <form action="phpinfo.php" method="GET" class="menuEntry">
        <input type="submit" name="phpinfo" value="Phpinfo" class="menuEntry" />
      </form>
    </div>

    <!-- Main page content -->
    <div class="mainView">

      <?php if (!$isLogged): ?>
      <!-- Signup form -->
      <div class="signup">
        <div class="signupContent">
          <h2 style="color: white">Don't have an account? Signup!</h2>
          <form action="my_shuttle.php?action=signup" method="POST" class="signup">
            <div class="email">
              <input id="emailSignup" type="text" name="email" placeholder="Email" class="email">
            </div>
            <div class="password">
              <input id="pswSignup" type="password" name="password" class="password" placeholder="Password">
            </div>
            <input type="submit" name="signup" value="Signup" class="submit" />
          </form>
        </div>
      </div>
      <?php endif; ?>

      <!-- Booking overview (without authentication) -->
      

      <!-- Booking overview after authentication -->

    </div>

  </div>

  </div>
  <noscript>
    Sorry: Your browser does not support or has
    disabled javascript
  </noscript>
</body>
</html>