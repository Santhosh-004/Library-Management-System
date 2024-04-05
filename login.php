<?php

session_start();
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

   $server = "localhost";
   $username = "root";
   $password = "";
   $dbname = "library";

   $conn = new mysqli($server, $username, $password, $dbname);

   if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
   } else {
      $email = $_POST["email"];
      $password = $_POST["password"];
      $fail = false;

      //echo $_POST["email"] . " " . $_POST["password"];

      $checkerQ = "select count(email) from Login where email = '$email' and password = '$password'";

      $result = $conn->query($checkerQ);

      if ($result->fetch_row()[0] == 1) {
         // Redirect to the dashboard page
         $_SESSION["email"] = $email;
         header("Location: dashboard.php");
         exit();
      } else {
         $fail = true;
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
   <link rel="stylesheet" href="new1.css" />
   <title>Login</title>
</head>

<body>
   <nav class="navbar bg-dark border-bottom border-body" data-bs-theme="dark">
      <div class="container-fluid">
         <a class="navbar-brand" href="index.html">
            <img src="images/srm.jpg" alt="brand" width="60" height="60" class="d-inline-block align-text-center me-2 rounded-1" />
         </a>
         <h4 class="text-white">Date&Time</h4>
      </div>
   </nav>
   <div class="container" style="height: 80vh">
      <div class="h-100 d-flex align-items-center justify-content-center">
         <div class="card w-75" style="max-width: 500px">
            <div class="card-header">
               <h4 class="m-0 p-0 fw-bold text-center">Login Page</h4>
            </div>
            <div class="card-body">
               <div>
                  <?php
                  if ($fail) {
                     echo '<p
                           class="text-danger text-center bg-danger-subtle border border-danger rounded-3 p-2"
                        >
                           Wrong Email/Password. Please try again.
                        </p>';
                  }
                  if ($_SESSION["register"]) {
                     echo '<p
                           class="text-success text-center bg-success-subtle border border-success rounded-3 p-2"
                        >
                           Registration Successful. Please login.
                        </p>';
                     $_SESSION["register"] = false;
                  }
                  ?>

               </div>
               <form class="d-flex flex-column gap-2 align-items-center" method="post" action="login.php">
                  <div class="labelInputL textStart w-100">
                     <label for="email">Email</label>
                     <input type="email" id="email" name="email" class="form-control" required />
                  </div>
                  <div class="labelInputL textStart w-100">
                     <label for="password">Password</label>
                     <input type="password" id="password" name="password" class="form-control" minlength="8" required />
                  </div>
                  <input type="submit" value="Login" class="btn btn-primary" />
               </form>
               <div class="textStart mt-3">
                  <p>New User? <a href="register.html">Register here</a></p>
                  <p>
                     Forgot Password? <a href="forgot.html">Click here</a>
                  </p>
               </div>
            </div>
         </div>
      </div>
   </div>
   <p class="position-absolute w-100 text-center">Made for 18CSE365J</p>
</body>

</html>