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
  <?php if ($error)
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
      <form action="login.php" method="POST" class="login">
        <div class="email">
          <div class="tooltip">
            <span class="tooltipText">Email must be a valid email</span>
            <input id="emailLogin" type="text" name="email" placeholder="Email" class="email">
          </div>
        </div>
        <div class="password">
          <div class="tooltip">
            <span class="tooltipText">At least a lowercase, and at least another char (uppercase or digit)</span>
            <input id="pswLogin" type="password" name="password" class="password" placeholder="Password">
          </div>
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
      <form action="my_shuttle.php?action=home" method="POST" class="menuEntry">
        <input type="submit" name="routing" value="Home" class="menuEntry" />
      </form>
      <?php if ($isLogged): ?>
        <form action="my_shuttle.php?action=personalPage" method="POST" class="menuEntry">
          <input type="submit" name="routing" value="Personal" class="menuEntry" />
        </form>
      <?php endif; ?>
      <form action="phpinfo.php" method="GET" class="menuEntry">
        <input type="submit" name="phpinfo" value="Phpinfo" class="menuEntry" />
      </form>
    </div>

    <!-- Main page content -->
    <div class="mainView">

      <?php if (!$isLogged): ?>
      <!-- Signup form -->
      <div class="contentContainer">
        <div class="signupContent">
          <h2 style="color: white">Don't have an account? Signup!</h2>
          <form action="my_shuttle.php?action=signup" method="POST" class="signup">
            <div class="email">
              <div class="tooltip">
                <span class="tooltipText">Email must be a valid email</span>
                <input id="emailSignup" type="text" name="email" placeholder="Email" class="email">
              </div>
            </div>
            <div class="password">
              <div class="tooltip">
                <span class="tooltipText">At least a lowercase, and at least another char (uppercase or digit)</span>
                <input id="pswSignup" type="password" name="password" class="password" placeholder="Password">
              </div>
            </div>
            <input type="submit" name="signup" value="Signup" class="submit" />
          </form>
        </div>
      </div>
      <?php endif; ?>

      <!-- Booking overview -->
      <div class="contentContainer">
        <div class="overviewContainer">
          <div class="tableContainer">
            <table style="width: 90%;">
              <tr>
                <th><h3 class="tableHeader">Departure<h3></th>
                <th><h3 class="tableHeader">Destination<h3></th>
                <th><h3 class="tableHeader">Bookings<h3></th>
              </tr>
              <?php bookOverview($isLogged && $routing == 'personalPage') ?>
            </table>
          </div>
        </div>
      </div>

      <!-- Booking form -->
      <?php if ($isLogged && $routing == 'personalPage'): ?>
        <div class="contentContainer">
          <div class="bookContent">
            <h3 style="color: #00abff">Book for a trip!</h2>
            <form action="my_shuttle.php?action=book" method="POST" class="book">
              <div class="bookInput">
                <input list="departure" type="text" name="departure" placeholder="Departure" class="book">
                <datalist id="departure">
                  <?php printStops() ?>
                </datalist>
              </div>
              <div class="bookInput">
                <input list="destination" type="text" name="destination" class="book" placeholder="Destination">
                <datalist id="destination">
                  <?php printStops() ?>
                </datalist>
              </div>
              <div class="bookInput">
                <input list="seats" type="text" name="seats" class="bookSmall" placeholder="Seats">
                <datalist id="seats">
                  <?php printSeats() ?>
                </datalist>
              </div>
              <input type="submit" name="book" value="Book" class="submit" />
            </form>
            <form action="my_shuttle.php?action=deleteBooking" method="POST" class="book">
              <div class="delete">
                <input type="submit" name="deleteBooking" class="deleteBooking" value="Delete booking">
              </div>
            </form>
          </div>
        </div>
      <?php endif; ?>

    </div>

  </div>

  </div>
  <noscript>
    Sorry: Your browser does not support or has
    disabled javascript
  </noscript>
</body>
</html>