<?php
session_start();

$server = 'localhost';
$username = 'root';
$password = '';
$dbname = 'library';

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
   die('Connection failed: ' . $conn->connect_error);
} elseif (empty($_SESSION['email'])) {
   header('Location: login.html');
} else {
   $email = $_SESSION['email'];
   $Usql = "select * from Users where email = '$email'";
   $Uresult = $conn->query($Usql);
}
$searchFlag = false;
$editFlag = false;
$deleteFlag = false;
$insertFlag = false;
$invSearchFlag = false;

function Qsearch($given, $conn, $union)
{
   if (!$union) {
      $Squery = "select * from Books where lower(title) like lower('%$given%') or lower(genre) like lower('%$given%') or lower(author) like lower('%$given%') or lower(year) like lower('%$given%') limit 5";
   } else {
      $Squery = "(select * from Books where id = $given) union (select * from Books where id != $given order by rand() limit 4)";
   }

   $Sresult = $conn->query($Squery);

   return $Sresult;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   if (isset($_POST['search_books'])) {
      $Sgiven = $_POST['search'];
      $Sresult = Qsearch($Sgiven, $conn, false);

      $searchFlag = true;
   }

   if (isset($_POST['editForm'])) {
      $title = $_POST['title'];
      $genre = $_POST['genre'];
      $author = $_POST['author'];
      $year = $_POST['year'];
      $quantity = $_POST['quantity'];
      $id = $_POST['id'];

      $Upsql = "update Books set title = '$title', genre = '$genre', author = '$author', year = '$year', quantity = '$quantity' where id = '$id'";
      $conn->query($Upsql);

      $editFlag = true;

      $Bresult = Qsearch($id, $conn, true);
   }

   if (isset($_POST['deleteForm'])) {
      $id = $_POST['delid'];
      $Dsql = "delete from Books where id = '$id'";
      $conn->query($Dsql);

      $deleteFlag = true;
   }

   if (isset($_POST['addForm'])) {
      $title = $_POST['title'];
      $genre = $_POST['genre'];
      $author = $_POST['author'];
      $year = $_POST['year'];
      $quantity = $_POST['quantity'];

      $Isql = "insert into Books (title, genre, author, year, quantity) values ('$title', '$genre', '$author', '$year', '$quantity')";
      $conn->query($Isql);

      $insertFlag = true;

      $query = "(select * from Books order by id desc limit 1) union (select * from Books order by rand() limit 4)";
      $Bresult = $conn->query($query);
   }

   if (isset($_POST['invsearch'])) {
      $Sgiven = $_POST['invval'];
      $Sresult = Qsearch($Sgiven, $conn, false);
      $invSearchFlag = true;
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1" />
   <title>Bootstrap demo</title>
   <link rel="stylesheet" href="new1.css" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
</head>

<body>

   <nav class="navbar bg-dark border-bottom border-body navbar-expand-lg" data-bs-theme="dark">
      <div class="container-fluid">
         <a class="navbar-brand" href="#">
            <img src="images/lib.png" alt="brand" width="40" height="40" class="d-inline-block align-text-center me-2" />
            Library Management System
         </a>
         <a href="logout.php" class="btn btn-outline-danger btn-sm" id="logout">Sign Out</a>
      </div>
   </nav>

   <div>
      <div class="d-flex align-items-start">
         <div class="nav flex-column nav-pills p-3 sidebar" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="min-width: 200px">
            <button class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">
               Profile
            </button>

            <?php
            $row = $Uresult->fetch_assoc();
            if ($row['accounttype'] == 3) {
               echo '<button
                        class="nav-link"
                        id="v-pills-bookinv-tab"
                        data-bs-toggle="pill"
                        data-bs-target="#v-pills-bookinv"
                        type="button"
                        role="tab"
                        aria-controls="v-pills-bookinv"
                        aria-selected="false"
                     >
                        Book Inventory
                     </button>';
            }
            if ($row['accounttype'] == 2 or $row['accounttype'] == 3) {
               echo '<button
                        class="nav-link"
                        id="v-pills-borrow-tab"
                        data-bs-toggle="pill"
                        data-bs-target="#v-pills-borrow"
                        type="button"
                        role="tab"
                        aria-controls="v-pills-borrow"
                        aria-selected="false"
                     >
                        Borrow
                     </button>';

               echo '<button
                        class="nav-link"
                        id="v-pills-return-tab"
                        data-bs-toggle="pill"
                        data-bs-target="#v-pills-return"
                        type="button"
                        role="tab"
                        aria-controls="v-pills-return"
                        aria-selected="false"
                     >
                        Return
                     </button>';
            }

            ?>

            <button class="nav-link" id="v-pills-search-tab" data-bs-toggle="pill" data-bs-target="#v-pills-search" type="button" role="tab" aria-controls="v-pills-search" aria-selected="false">
               Search
            </button>
         </div>
         <div class="tab-content m-3 d-flex justify-content-center flex-grow-1 align-content-center" id="v-pills-tabContent">
            <div class="tab-pane fade show active w-100" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab" tabindex="0">
               <div class="card text-start">
                  <div class="card-header fw-bolder fs-4">
                     Personal Information
                  </div>
                  <div class="card-body">
                     <div class="card-text">
                        <table class="table table-bordered">
                           <tbody>

                              <?php

                              if ($Uresult->num_rows > 0) {
                                 // Output data of each row
                                 echo '<tr>';
                                 echo "<td class='fw-bold'>Name</td>";
                                 echo '<td>' . $row['fname'] . ' ' . $row['lname'] . '</td>';
                                 echo '</tr>';

                                 echo '<tr>';
                                 echo "<td class='fw-bold'>Registration Number</td>";
                                 echo '<td>' . $row['register_no'] . '</td>';
                                 echo '</tr>';

                                 echo '<tr>';
                                 echo "<td class='fw-bold'>Email</td>";
                                 echo '<td>' . $row['email'] . '</td>';
                                 echo '</tr>';

                                 echo '<tr>';
                                 echo "<td class='fw-bold'>Phone Number</td>";
                                 echo '<td>' . $row['phoneno'] . '</td>';
                                 echo '</tr>';

                                 echo '<tr>';
                                 echo "<td class='fw-bold'>Gender</td>";
                                 echo '<td>' . $row['gender'] . '</td>';
                                 echo '</tr>';

                                 echo '<tr>';
                                 echo "<td class='fw-bold'>Account Type</td>";
                                 if ($row['accounttype'] == '3') {
                                    echo '<td>Administrator</td>';
                                 } elseif ($row['accounttype'] == '2') {
                                    echo '<td>Librarian</td>';
                                 } elseif ($row['accounttype'] == '1') {
                                    echo '<td>Student / Staff';
                                 }
                                 echo '</tr>';

                                 echo '<tr>';
                                 echo "<td class='fw-bold'>Account Created</td>";
                                 echo '<td>' . $row['accountcreated'] . '</td>';
                                 echo '</tr>';
                              } else {
                                 echo '0 results';
                              }
                              ?>

                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
            <div class="tab-pane fade w-100" id="v-pills-bookinv" role="tabpanel" aria-labelledby="v-pills-bookinv-tab" tabindex="0">
               <div>
                  <?php
                  if ($editFlag) {
                     echo '<p class="text-success text-center bg-success-subtle border border-success rounded-3 p-2">
               <strong>Success.</strong> The changes has been saved
         </p>';
                  }

                  if ($deleteFlag) {
                     echo '<p class="text-danger text-center bg-danger-subtle border border-danger rounded-3 p-2">
               The book has been deleted <strong>Successfully.</strong>
                     </p>';
                  }

                  if ($insertFlag) {
                     echo '<p class="text-success text-center bg-success-subtle border border-success rounded-3 p-2">
               <strong>Success.</strong> The book has been added
         </p>';
                  }

                  ?>

               </div>

               <div class="mx-auto" style="width: 85%">
                  <div class="mb-4">
                     <form class="d-flex" id="invsearch" method="post">
                        <input type="hidden" name="invsearch" value="1" />
                        <input class="form-control me-2" type="search" placeholder="Search" name="invval" />
                        <button class="btn btn-outline-success" type="submit">
                           Search
                        </button>
                     </form>
                  </div>
                  <div>
                     <table class="table">
                        <thead>
                           <tr>
                              <th class="col-3">Title</th>
                              <th class="col-2">Author</th>
                              <th>Year</th>
                              <th>Genre</th>
                              <th>Quantity</th>
                              <th>Actions</th>
                           </tr>
                        </thead>
                        <tbody>

                           <?php


                           if ($editFlag) {
                              $editFlag = false;
                              while ($Sbooks = $Bresult->fetch_assoc()) {
                                 echo '<tr>';
                                 echo '<td>' . $Sbooks['title'] . '</td>';
                                 echo '<td>' . $Sbooks['author'] . '</td>';
                                 echo '<td>' . $Sbooks['year'] . '</td>';
                                 echo '<td>' . $Sbooks['genre'] . '</td>';
                                 echo '<td>' . $Sbooks['quantity'] . '</td>';
                                 echo '<td>
                                          <i
                                             class="bi bi-pencil-square me-2 fs-5 text-success btn"
                                             data-bs-toggle="modal"
                                             data-bs-target="#editModal"
                                             d-title="' . $Sbooks['title'] . '"
                                             d-author="' . $Sbooks['author'] . '"
                                             d-year="' . $Sbooks['year'] . '"
                                             d-genre="' . $Sbooks['genre'] . '"
                                             d-quantity="' . $Sbooks['quantity'] . '"
                                             d-id="' . $Sbooks['id'] . '"
                                          ></i>
                                          <i class="bi bi-trash fs-5 text-danger btn"
                                          data-bs-toggle="modal"
                                          data-bs-target="#deleteModal"
                                          d-id="' . $Sbooks['id'] . '"
                                          ></i>
                                       </td>';
                                 echo '</tr>';
                              }
                           } elseif ($insertFlag) {

                              $insertFlag = false;
                              while ($Sbooks = $Bresult->fetch_assoc()) {
                                 echo '<tr>';
                                 echo '<td>' . $Sbooks['title'] . '</td>';
                                 echo '<td>' . $Sbooks['author'] . '</td>';
                                 echo '<td>' . $Sbooks['year'] . '</td>';
                                 echo '<td>' . $Sbooks['genre'] . '</td>';
                                 echo '<td>' . $Sbooks['quantity'] . '</td>';
                                 echo '<td>
                                          <i
                                             class="bi bi-pencil-square me-2 fs-5 text-success btn"
                                             data-bs-toggle="modal"
                                             data-bs-target="#editModal"
                                             d-title="' . $Sbooks['title'] . '"
                                             d-author="' . $Sbooks['author'] . '"
                                             d-year="' . $Sbooks['year'] . '"
                                             d-genre="' . $Sbooks['genre'] . '"
                                             d-quantity="' . $Sbooks['quantity'] . '"
                                             d-id="' . $Sbooks['id'] . '"
                                          ></i>
                                          <i class="bi bi-trash fs-5 text-danger btn"
                                          data-bs-toggle="modal"
                                          data-bs-target="#deleteModal"
                                          d-id="' . $Sbooks['id'] . '"
                                          ></i>
                                       </td>';
                                 echo '</tr>';
                              }
                           } else {

                              if (!$invSearchFlag) {
                                 $bookquery = 'select * from Books order by rand() limit 5';
                                 $Sresult = $conn->query($bookquery);
                              }

                              if ($Sresult->num_rows > 0) {
                                 while ($Brow = $Sresult->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $Brow['title'] . '</td>';
                                    echo '<td>' . $Brow['author'] . '</td>';
                                    echo '<td>' . $Brow['year'] . '</td>';
                                    echo '<td>' . $Brow['genre'] . '</td>';
                                    echo '<td>' . $Brow['quantity'] . '</td>';
                                    echo '<td>
                                          <i
                                             class="bi bi-pencil-square me-2 fs-5 text-success btn"
                                             data-bs-toggle="modal"
                                             data-bs-target="#editModal"
                                             d-title="' . $Brow['title'] . '"
                                             d-author="' . $Brow['author'] . '"
                                             d-year="' . $Brow['year'] . '"
                                             d-genre="' . $Brow['genre'] . '"
                                             d-quantity="' . $Brow['quantity'] . '"
                                             d-id="' . $Brow['id'] . '"
                                          ></i>
                                          <i class="bi bi-trash fs-5 text-danger btn"
                                          data-bs-toggle="modal"
                                          data-bs-target="#deleteModal"
                                          d-id="' . $Brow['id'] . '"
                                          ></i>
                                       </td>';
                                    echo '</tr>';
                                 }
                              }
                           }

                           ?>

                        </tbody>
                     </table>
                  </div>
               </div>

               <!-- Modal -->

               <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Modal</h1>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editForm" method="post">
                           <input type="hidden" name="editForm" />
                           <input type="hidden" id="mid" name="id" value="" />
                           <div class="d-flex flex-column modal-body gap-3">
                              <div class="text-danger border border-danger rounded-3 bg-danger-subtle text-center p-2 d-none" id="wvalue">
                                 Quantity must be greater than or equal to 0
                              </div>
                              <div>
                                 <label>Title</label>
                                 <input type="text" class="form-control" id="mtitle" name="title" value="" />
                              </div>
                              <div class="d-flex justify-content-between gap-3">
                                 <div>
                                    <label>Author</label>
                                    <input type="text" class="form-control" id="mauthor" name="author" value="" />
                                 </div>
                                 <div>
                                    <label>Year</label>
                                    <input type="text" class="form-control" id="myear" name="year" value="" />
                                 </div>
                              </div>
                              <div>
                                 <label>Genre</label>
                                 <input type="text" class="form-control" id="mgenre" name="genre" value="" />
                              </div>
                              <div>
                                 <label>Quantity</label>
                                 <input type="number" class="form-control" id="mquantity" name="quantity" value="" />
                              </div>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="button" class="btn btn-primary" id="save">Save changes</button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>

               <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                     <form id="deleteForm" method="post">
                        <input type="hidden" name="deleteForm" />
                        <input type="hidden" id="did" value="" name="delid" />
                        <div class="modal-content">
                           <div class="modal-body">
                              <h4 class="fw-bold">
                                 Are you sure you want to delete this book?
                              </h4>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-danger">Delete</button>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>

               <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h1 class="modal-title fs-5">Add New Book</h1>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addForm" method="post">
                           <div class="modal-body">
                              <input type="hidden" name="addForm" />
                              <div class="d-flex flex-column gap-3">
                                 <div class="text-danger border border-danger rounded-3 bg-danger-subtle text-center p-2 d-none" id="wavalue">
                                    Quantity must be greater than or equal to 0
                                 </div>
                                 <div>
                                    <label>Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" required />
                                 </div>
                                 <div class="d-flex justify-content-between gap-3">
                                    <div>
                                       <label>Author <span class="text-danger">*</span></label>
                                       <input type="text" class="form-control" name="author" required />
                                    </div>
                                    <div>
                                       <label>Year <span class="text-danger">*</span></label>
                                       <input type="text" class="form-control" name="year" required />
                                    </div>
                                 </div>
                                 <div>
                                    <label>Genre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="genre" required />
                                 </div>
                                 <div>
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="aquantity" name="quantity" required />
                                 </div>
                              </div>

                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="button" class="btn btn-primary" id="addbook">Add Book</button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>

               <div style="position: absolute; bottom: 50px; right: 100px" data-bs-toggle="modal" data-bs-target="#addModal">
                  <button class="btn btn-primary">
                     <i class="bi bi-plus-lg"></i>
                     <span class="fw-bold">Add Book</span>
                  </button>
               </div>
            </div>
            <div class="tab-pane fade w-100" id="v-pills-borrow" role="tabpanel" aria-labelledby="v-pills-borrow-tab" tabindex="0">
               <div class="mx-auto" style="width: 85%">
                  <div class="mb-4">
                     <form class="d-flex justify-content-evenly align-items-center">
                        <div class="d-flex w-75 align-items-center">
                           <label for="email" class="fw-bold">User Email:</label>
                           <input class="form-control ms-4 w-75" type="email" />
                        </div>
                        <button class="btn btn-outline-primary" type="submit">
                           <i class="bi bi-check-lg"></i>
                           <span class="fw-bold">Check</span>
                        </button>
                     </form>
                  </div>
                  <div>
                     <div class="card">
                        <div class="card-header">Books Available</div>
                        <div class="card-body">
                           <div class="mb-4">
                              <form class="d-flex" role="search">
                                 <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                                 <button class="btn btn-outline-success" type="submit">
                                    Search
                                 </button>
                              </form>
                           </div>
                           <table class="table">
                              <thead>
                                 <tr>
                                    <th class="col-3">Title</th>
                                    <th class="col-2">Author</th>
                                    <th>Year</th>
                                    <th>Genre</th>
                                    <th>Actions</th>
                                 </tr>
                              </thead>
                              <tbody>

                                 <tr>
                                    <td>Book 1</td>
                                    <td>Author 1</td>
                                    <td>2000</td>
                                    <td>Horror, Fantasy, Romance</td>
                                    <td>
                                       <button class="btn btn-primary">
                                          Select
                                       </button>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>Book 2</td>
                                    <td>Author 2</td>
                                    <td>2000</td>
                                    <td>Horror, Fantasy, Romance</td>
                                    <td>
                                       <button class="btn btn-primary">
                                          Select
                                       </button>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>Book 3</td>
                                    <td>Author 3</td>
                                    <td>2000</td>
                                    <td>Horror, Fantasy, Romance</td>
                                    <td>
                                       <button class="btn btn-primary">
                                          Select
                                       </button>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>Book 4</td>
                                    <td>Author 4</td>
                                    <td>2000</td>
                                    <td>Horror, Fantasy, Romance</td>
                                    <td>
                                       <button class="btn btn-primary">
                                          Select
                                       </button>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>Book 5</td>
                                    <td>Author 5</td>
                                    <td>2000</td>
                                    <td>Horror, Fantasy, Romance</td>
                                    <td>
                                       <button class="btn btn-primary">
                                          Select
                                       </button>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="mt-4 d-flex justify-content-center">
                     <button class="btn btn-primary fw-bold" id="borrow">
                        Borrow
                     </button>
                  </div>
               </div>
            </div>
            <div class="tab-pane fade w-100" id="v-pills-return" role="tabpanel" aria-labelledby="v-pills-return-tab" tabindex="0">
               <div class="mx-auto" style="width: 85%">
                  <div class="mb-4">
                     <form class="d-flex justify-content-evenly align-items-center">
                        <div class="d-flex w-75 align-items-center">
                           <label for="email" class="fw-bold">User Email:</label>
                           <input class="form-control ms-4 w-75" type="email" />
                        </div>
                        <button class="btn btn-outline-primary" type="submit">
                           <i class="bi bi-check-lg"></i>
                           <span class="fw-bold">Check</span>
                        </button>
                     </form>
                  </div>
                  <div>
                     <div class="card">
                        <div class="card-header">
                           Books Owned by the User
                        </div>
                        <div class="card-body">
                           <table class="table">
                              <thead>
                                 <tr>
                                    <th class="col-3">Title</th>
                                    <th class="col-2">Author</th>
                                    <th>Year</th>
                                    <th>Genre</th>
                                    <th>Actions</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr>
                                    <td>Book 1</td>
                                    <td>Author 1</td>
                                    <td>2000</td>
                                    <td>Horror, Fantasy, Romance</td>
                                    <td>
                                       <button class="btn btn-primary">
                                          Select
                                       </button>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>Book 2</td>
                                    <td>Author 2</td>
                                    <td>2000</td>
                                    <td>Horror, Fantasy, Romance</td>
                                    <td>
                                       <button class="btn btn-primary">
                                          Select
                                       </button>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>Book 3</td>
                                    <td>Author 3</td>
                                    <td>2000</td>
                                    <td>Horror, Fantasy, Romance</td>
                                    <td>
                                       <button class="btn btn-primary">
                                          Select
                                       </button>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="mt-4 d-flex justify-content-center">
                     <button class="btn btn-primary fw-bold">Return</button>
                  </div>
               </div>
            </div>
            <div class="tab-pane fade w-100" id="v-pills-search" role="tabpanel" aria-labelledby="v-pills-search-tab" tabindex="0">
               <div>
                  <div>
                     <div class="card">
                        <div class="card-header">Books Available</div>
                        <div class="card-body">
                           <div class="mb-4">
                              <form id="searchForm" class="d-flex" role="search" method="post">
                                 <input type="hidden" name="search_books" />
                                 <input class="form-control me-2" type="search" name="search" placeholder="Search" aria-label="Search" required />
                                 <button class="btn btn-outline-success" type="submit" id="searchbtn">
                                    Search
                                 </button>
                              </form>

                           </div>
                           <table class="table">
                              <thead>
                                 <tr>
                                    <th class="col-3">Title</th>
                                    <th class="col-2">Author</th>
                                    <th>Year</th>
                                    <th>Genre</th>
                                    <th>Quantity</th>
                                 </tr>
                              </thead>
                              <tbody>

                                 <?php

                                 if (!$searchFlag) {
                                    $bookquery = 'select * from Books order by rand() limit 5';
                                    $Bresult = $conn->query($bookquery);

                                    if ($Bresult->num_rows > 0) {
                                       while ($Brow = $Bresult->fetch_assoc()) {
                                          echo '<tr>';
                                          echo '<td>' . $Brow['title'] . '</td>';
                                          echo '<td>' . $Brow['author'] . '</td>';
                                          echo '<td>' . $Brow['year'] . '</td>';
                                          echo '<td>' . $Brow['genre'] . '</td>';
                                          echo '<td>' . $Brow['quantity'] . '</td>';
                                          echo '</tr>';
                                       }
                                    }
                                 } else {
                                    while ($Sbooks = $Sresult->fetch_assoc()) {
                                       echo '<tr>';
                                       echo '<td>' . $Sbooks['title'] . '</td>';
                                       echo '<td>' . $Sbooks['author'] . '</td>';
                                       echo '<td>' . $Sbooks['year'] . '</td>';
                                       echo '<td>' . $Sbooks['genre'] . '</td>';
                                       echo '<td>' . $Sbooks['quantity'] . '</td>';
                                       echo '</tr>';
                                    }
                                 }

                                 ?>


                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
   <script>
      window.addEventListener('DOMContentLoaded', (event) => {
         // Check if the search button was clicked
         let searchClicked = sessionStorage.getItem('searchClicked');
         let bookInventory = sessionStorage.getItem('bookInventory');

         console.log(searchClicked, bookInventory);

         if (searchClicked === 'true') {
            console.log("search tab");

            // Set the active tab and pane for the search tab
            document.getElementById("v-pills-profile-tab").classList.remove('active');
            document.getElementById("v-pills-profile").classList.remove('active', 'show');
            document.getElementById("v-pills-search-tab").classList.add('active');
            document.getElementById("v-pills-search").classList.add('active', 'show');
         }

         if (bookInventory === 'true') {
            console.log("bookinv tab");

            // Set the active tab and pane for the bookinv tab
            document.getElementById("v-pills-profile-tab").classList.remove('active');
            document.getElementById("v-pills-profile").classList.remove('active', 'show');
            document.getElementById("v-pills-bookinv-tab").classList.add('active');
            document.getElementById("v-pills-bookinv").classList.add('active', 'show');
         }


      });

      document.getElementById('v-pills-search-tab').addEventListener('click', (event) => {
         sessionStorage.setItem("searchClicked", true);
         sessionStorage.setItem("bookInventory", false);
         console.log("clicked search");
      })

      document.getElementById('v-pills-bookinv-tab').addEventListener('click', (event) => {
         sessionStorage.setItem("bookInventory", true);
         sessionStorage.setItem("searchClicked", false);
         console.log("clicked bookinv");
      })

      document.getElementById('logout').addEventListener('click', (event) => {
         console.log("clicked signOut");
         sessionStorage.setItem("searchClicked", false);
         sessionStorage.setItem("bookInventory", false);
      })
   </script>
   <script>
      document.getElementById('editModal').addEventListener('show.bs.modal', (event) => {
         var modal = event.relatedTarget;
         var title = modal.getAttribute('d-title');
         var author = modal.getAttribute('d-author');
         var year = modal.getAttribute('d-year');
         var genre = modal.getAttribute('d-genre');
         var quantity = modal.getAttribute('d-quantity');
         var id = modal.getAttribute('d-id');

         document.getElementById('mtitle').value = title;
         document.getElementById('mauthor').value = author;
         document.getElementById('myear').value = year;
         document.getElementById('mgenre').value = genre;
         document.getElementById('mquantity').value = quantity;
         document.getElementById('mid').value = id;
      })

      document.querySelector('#deleteModal').addEventListener('show.bs.modal', (event) => {
         var modal = event.relatedTarget;
         var id = modal.getAttribute('d-id');
         console.log(id);
         document.querySelector('#did').value = id;
      })
   </script>
   <script>
      document.querySelector("#save").addEventListener("click", (event) => {
         value = document.querySelector("#mquantity").value;
         if (value >= 0) {
            document.querySelector("#editForm").submit();
            document.querySelector("#wvalue").classList.add("d-none");
         } else {
            document.querySelector("#wvalue").classList.remove("d-none");
            event.preventDefault();
         }
      })

      document.querySelector("#addbook").addEventListener("click", (event) => {
         value = document.querySelector("#aquantity").value;
         console.log(value);
         if (value >= 0) {
            document.querySelector("#addForm").submit();
            document.querySelector("#wavalue").classList.add("d-none");
         } else {
            console.log('fail');
            document.querySelector("#wavalue").classList.remove("d-none");
            event.preventDefault();
         }
      })
   </script>

</body>

</html>