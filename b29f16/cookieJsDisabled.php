<?php include("entryPoint.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <title>My Shuttle</title>
  <link href="landingpStyle.css" rel="stylesheet" />
</head>
<body>

  <div class="container">
    <div class="content">

      <div class="title">
        <h2 class="headerColored">My</h2>
        <h2 class="header">Shuttle</h2>
      </div>

      <h2 class="message">Sorry, this site needs cookies and javascript to be enabled to word properly!</h2>

      <form action="index.php" method="POST">
        <input type="submit" name="return" value="I've enabled both" class="submit" />
      </form>

    </div>
  </div>

</body>
</html>