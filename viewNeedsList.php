<?php
    require_once 'login.php';
    session_start();

    if ((isset($_SESSION['username']))&&
          $_SESSION['type']=='user')
    {
      $username = $_SESSION['username'];
      $password = $_SESSION['password'];
      $first_name = $_SESSION['first_name'];
      $last_name  = $_SESSION['last_name'];
    }
    else  
    {
        // send to ACCESS FORBIDDEN
         header('Location: accessForbidden.php');
    }

    $connection = new mysqli($hn, $un, $pw, $db);
  
    if ($connection->connect_error) die($connection->connect_error);
    
    $needsQuery ="SELECT item_name, category, description, Items.item_id
                  FROM Items INNER JOIN Has INNER JOIN Users
                  ON Items.item_id = Has.item_id
                  WHERE Has.user_name = '$username'
                  AND Items.is_need=1 ";

    $needsResults = $connection->query($needsQuery);
    if (!$needsResults) die($connection->error);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Needs List</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
      <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
          <ul class="nav navbar-nav">
            <li><a href="login_page.php" style="font-size: 24px;">The Trading Post</a></li>
            <li><a href="aboutUs.html">About Us</a></li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Update Lists <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="updateHavesList.php">Update Haves List</a></li>
                <li><a href="updateNeedsList.php">Update Needs List</a></li>  
              </ul>
            </li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Views <span class="caret"></span></a>
              <ul class="dropdown-menu">
                  <li><a href="viewItems.php">View All Items</a></li>
                <li><a href="viewNeedsList.php">View Needs</a></li>
                <li><a href="viewHavesList.php">View Haves</a></li>
                <li><a href="viewOffers.php">View Offers</a></li> 
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="updateAccount.php"><span class="glyphicon glyphicon-user"></span> Account</a></li>
            <li><a href="logout_page.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
      </div>
      </div>
    </nav>

    <br />
    <br />

    <div class="container">
    <br />
    <br />
    <h2>View Personal Needs</h2>
      <?php 
          if ($needsResults->num_rows >0 )
          {
      ?>
      <table class="table table-hover table-bordered table-stripped">
        <thead><tr><th>Name</th><th>Category</th><th>Description</th><th>Reports</th></tr></thead>
        <tbody>
          <?php
            // output data as table
            while ($row=$needsResults->fetch_array())
              {?>
                <tr>
                  <td><?php echo $row[0]; ?></td>
                  <td><?php echo $row[1]; ?></td>
                  <td><?php echo $row[2]; ?></td>
                  <td><a href="viewOffers.php">
                        <?php 
                        // Has offers list
                          $offersQuery = "SELECT *
                                          FROM Items NATURAL JOIN Participates
                                          WHERE Participates.offerer = '$username'
                                          AND Offers.offer_id = '$row[3]' ";

                          $offersResult = $connection->query($offersQuery);
                          if (!$offersResult) die($connection->error);

                          if($offersResult->num_rows>0){
                            echo $offersResult->num_rows;
                          }
                          else{
                            echo '0'; 
                            
                          }
                        ?></a>
                  </td>
                </tr>
             <?php } ?>
        </tbody>
      </table>
      <?php
          }
          else
          {
            echo "<h3>No needs listed </h3>";
          }
          $needsResults->close();
          $connection->close();
      ?> 
    </div>        
    </body>
</html>
