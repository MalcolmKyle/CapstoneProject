<?php
 require_once 'login.php';
  session_start();
  $connection = new mysqli($hn, $un, $pw, $db);
  
  if ($connection->connect_error) die($connection->connect_error);
  $loginError="";
  $username=$_POST['username'];
  $password=$_POST['password'];
  // Is someone already logged in? If so, forward them to the correct
  // page. (HINT: Implement this last, you cannot test this until
  //              someone can log in.)
 if(isset($_SESSION['first_name']))
  {
    if($_SESSION['type']=="user")
      header('Location: http://pluto.cse.msstate.edu/~dcspd/user_page.php');
    elseif($_SESSION['type']=="admin")
      header('Location: http://pluto.cse.msstate.edu/~dcspd/admin_page.php');
  }

  // Were a username and password provided? If so check them against
  // the database.
  if ((isset($username))&&
      (isset($password)))
  {
    $un_temp = mysql_entities_fix_string($connection, $username);
    $pw_temp = mysql_entities_fix_string($connection, $password);
    /*
    * Malcolm McCullum - 11/2/2016
    *
    */
  
    $userQuery = "SELECT * FROM Users WHERE username='$un_temp'";
    $adminQuery = "SELECT * FROM Admins WHERE user_name='$un_temp'";

    $userResult = $connection->query($userQuery);
    $adminResult = $connection->query($adminQuery);

    if (!$userResult) die($connection->error);
    elseif(!$adminResult) die($connection->errror);

    elseif ($adminResult->num_rows)
    {
      $row = $adminResult->fetch_array(MYSQLI_NUM);
      $adminInfoRow = $userResult->fetch_array(MYSQLI_NUM);
      $userResult->close();
      $adminResult->close();
      $salt1    = "qm&h*";
      $salt2    = "pg!@";
      $token = hash('ripemd128', "$salt1$pw_temp$salt2");
      
     // if ($token == $row[0])
      //if ($pw_temp == $row[0]) ADMIN PASSWORDS?
      if($adminInfoRow[5] == 0)
      {
        if (($un_temp == $row[0]) && $token == $adminInfoRow[0])
        {
              // If username / password were valid, set session variables
              // and forward them to the correct page
              // How to get admin's user's username
              $_SESSION['username'] = $un_temp;
              $_SESSION['password'] = $pw_temp;
              $_SESSION['first_name'] = $adminInfoRow[3];
              $_SESSION['last_name']  = $adminInfoRow[4];
              $_SESSION['type'] = "admin";

              // send them to admin page
              header('Location: http://pluto.cse.msstate.edu/~dcspd/admin_page.php');
        }
        else $loginError="Invalid username/password combination";
      }
      else if($adminInfoRow[5]==1)
      {
        $loginError = "YOU ARE BANNED!";
      }
    }

    elseif ($userResult->num_rows)
    {
        $row = $userResult->fetch_array(MYSQLI_NUM);
        $userResult->close();

        
        $token = hash('ripemd128', "$salt1$pw_temp$salt2");

        if ($row[5]==0)
        {
          if (($un_temp == $row[1]) && ($token == $row[0]))
          {
                // If username / password were valid, set session variables
                // and forward them to the correct page
                $_SESSION['username'] = $un_temp;
                $_SESSION['password'] = $pw_temp;
                $_SESSION['first_name'] = $row[3];
                $_SESSION['last_name']  = $row[4];
                $_SESSION['type']  = "user";
                
                // send them to user page
                header('Location: http://pluto.cse.msstate.edu/~dcspd/user_page.php');
          }
          else  $loginError="Invalid username/password combination";
          
        }

        else if($row[5]==1)
        {
          $loginError="YOU ARE BANNED!";
        }
    }
    
    else $loginError='No user found with that user name';
  }


  $connection->close();
  function mysql_entities_fix_string($connection, $string)
  {
    return htmlentities(mysql_fix_string($connection, $string));
  } 
  function mysql_fix_string($connection, $string)
  {
    if (get_magic_quotes_gpc()) $string = stripslashes($string);
    return $connection->real_escape_string($string);
  }
  
?>
<!DOCTYPE html>
<html>
    <head>
      <title>Log in</title>
      <link rel="shortcut icon" src="tradingpost.ico" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <style type="text/css">
          input{
            width: 25px
          }
        </style>
    </head>

    <body>

      <nav class="navbar navbar-inverse">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="login_page.php">The Trading Post</a>
          </div>
          <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
              <li><a href="aboutUs.html">About Us</a></li>              
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li><a href="createAccount.php"><span class="glyphicon glyphicon-user"></span>Create Account</a></li>
              <li><a href="login_page.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
            </ul>
          </div>
        </div>
      </nav>

      <div class="container">
        <div class="col-sm-4"></div>
        <div class="col-sm-8">
          <h2><img src="tradingpost.jpg" alt="The Trading Post" height="200" width="500"></h2>
        </div>
        
        <form class="form-horizontal" method="POST" action="login_page.php">
          <div class="form-group">
            <label class="control-label col-sm-4"></label>
            <div class="col-sm-4">
               <p style="color: red">
                <!--Placeholder for error messages-->
                 <h3 style="color: red"><?php echo $loginError;?></h3>
                </p>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-4">User Name:</label>
            <div class="col-sm-4">
              <input type="text" class="form-control" id="username" name="username" maxlength="25" value="<?php echo $un_temp?>" required>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-4" for="pwd">Password:</label>
            <div class="col-sm-4">
              <input type="password" class="form-control" name="password" id="password" maxlength="25" value="<?php echo $pw_temp?>" required>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-10">
              <a href="forgotPassword.php" style="font-size: italic;" class="text-primary">Forgot Password?</a>
              <button type="submit" class="btn btn-default">Submit</button>
            </div>
          </div>
        </form>
      </div>
  </body>
</html>