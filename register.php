<?php

session_start();

$passmatch = false;
$agematch = false;
$exist = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $server = 'localhost';
   $username = 'root';
   $password = '';
   $dbname = 'library';

   $conn = new mysqli($server, $username, $password, $dbname);

   if ($conn->connect_error) {
      die('Connection failed: ' . $conn->connect_error);
   } else {
      $fname = $_POST['fname'];
      $lname = $_POST['lname'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $cpassword = $_POST['cpassword'];
      $dob = $_POST['dob'];
      $gender = $_POST['gender'];
      $phone = $_POST['phone'];
      $regno = strtoupper($_POST['regno']);

      //echo $fname . ' ' . $lname . ' ' . $email . ' password: ' . $password . ' confirm: ' . $cpassword . ' ' . $dob . ' ' . $gender . ' ' . $phone . ' ' . $regno;

      if ($password == $cpassword) {
         $year = date('Y', strtotime($dob));
         $passmatch = true;
         if (2024 - intval($year) >= 18) {
            $agematch = true;
         }
      }

      if ($passmatch && $agematch) {
         $checker1 = "select count(register_no) from Users where register_no = '$regno' or email = '$email'";
         $cresult = $conn->query($checker1);

         // echo 'check ' . $cresult->fetch_row()[0];

         if (!$cresult->fetch_row()[0]) {
            $exist = true;
         }
      }

      // echo 'pass ' . $passmatch . ' age ' . $agematch . ' exist ' . $exist;
      if ($exist) {
         $now = date('Y-m-d');
         $writeQ = "insert into Users (register_no, fname, lname, email, dob, gender, phoneno, accountcreated, booksOwned) values ('$regno', '$fname', '$lname', '$email', '$dob', '$gender', '$phone', '$now', '')";
         $iresult = $conn->query($writeQ);
         $writeQ = "insert into Login (email, password) values ('$email', '$password')";
         $iresult = $conn->query($writeQ);
      }

      if ($iresult) {
         $_SESSION['register'] = true;
         header('Location: login.php');
         exit();
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
   <title>Register</title>
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
   <div class="container h-100 mt-4 d-flex flex-column align-items-center justify-content-center">
      <div class="w-75">
         <?php
         if (!$passmatch) {
            echo '<p class="text-danger text-center bg-danger-subtle border border-danger rounded-3 p-2">Passwords do not match. Try again.</p>';
         } elseif (!$agematch) {
            echo '<p class="text-danger text-center bg-danger-subtle border border-danger rounded-3 p-2">Age must be greater than 18. Try again.</p>';
         } elseif (!$exist) {
            echo '<p class="text-danger text-center bg-danger-subtle border border-danger rounded-3 p-2">User Already exist. Login in.</p>';
         }
         ?>
      </div>
      <div class="card w-100">
         <div class="card-header">
            <h2 class="m-0 p-0 fw-bold text-center">Registration Page</h2>
         </div>
         <div class="card-body">
            <form class="d-flex flex-column align-items-center" action="register.php" method="post">
               <div class="d-flex justify-content-center w-100">
                  <div class="labelInput textStart me-5">
                     <label for="firstName">First Name</label>
                     <input type="text" id="firstName" class="form-control" name="fname" required />
                  </div>
                  <div class="labelInput textStart">
                     <label for="lastName">Last Name</label>
                     <input type="text" id="lastName" class="form-control" name="lname" required />
                  </div>
               </div>
               <div class="d-flex justify-content-center w-100">
                  <div class="labelInput textStart me-5">
                     <label for="email">Email</label>
                     <input type="email" id="email" class="form-control" name="email" required />
                  </div>
                  <div class="labelInput textStart">
                     <label for="regno">Register Number</label>
                     <input type="text" id="regno" class="form-control" name="regno" required />
                  </div>
               </div>
               <div class="d-flex justify-content-center w-100">
                  <div class="labelInput textStart me-5">
                     <label for="password">Password</label>
                     <input type="password" id="password" class="form-control" name="password" minlength="8" required />
                  </div>
                  <div class="labelInput textStart">
                     <label for="confirmPassword">Confirm Password</label>
                     <input type="password" id="cpassword" class="form-control" name="cpassword" minlength="8" required />
                  </div>
               </div>
               <div class="d-flex justify-content-center w-100">
                  <div class="labelInput textStart me-5">
                     <label for="phone">Phone Number</label>
                     <input type="tel" id="phone" class="form-control" name="phone" minlength="10" required />
                  </div>
                  <div class="labelInput textStart" style="height: 62px">
                     <label for="dob">Date of Birth</label>
                     <input type="date" id="dob" class="form-control" name="dob" required />
                  </div>
               </div>
               <div class="d-flex gap-4 mt-3">
                  <label for="gender">Gender: </label>
                  <div class="align">
                     <input type="radio" id="male" name="gender" value="Male" required />
                     <label for="male">Male</label>
                  </div>
                  <div class="align">
                     <input type="radio" id="female" name="gender" value="Female" />
                     <label for="female">Female</label>
                  </div>
                  <div class="align">
                     <input type="radio" id="other" name="gender" value="Other" />
                     <label type="radio" id="other">Other</label>
                  </div>
               </div>
               <input type="submit" value="Register" class="btn btn-primary mt-3" />
            </form>
            <div class="mt-2">
               <p>
                  Already have an account?
                  <a href="login.html">Login here</a>
               </p>
            </div>
         </div>
      </div>
   </div>
   <p class="position-absolute w-100 text-center">Made for 18CSE365J</p>
</body>

</html>