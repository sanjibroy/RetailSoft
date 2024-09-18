<?php
include("api/configs.php");
include("api/sql-functions.php");
session_start();

$_SESSION['session_expires']=false;

if(isset($_SESSION['PH_USER_ID']))
{
    header("Location: dashboard/dashboard.php");
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $username = mysqli_real_escape_string($dbh, $_POST["username"]);
    $password = mysqli_real_escape_string($dbh, $_POST["password"]);

    // Prepare and execute the SQL insert statement
    $sql = "SELECT `user_id`, `user_name`, `user_type`, `user_pass`, `user_status`, `user_display_name` FROM `tbl_login` WHERE user_name='$username' AND user_pass='$password' AND user_status='1'";
    $data=GetData($sql,$dbh);
    if(count($data)>0){

      $_SESSION["PH_USER_ID"]        =   $data[0][0];
      $_SESSION["PH_USER_NAME"]      =   $data[0][1];
      $_SESSION["PH_USER_TYPE"]      =   $data[0][2];
      $_SESSION["PH_USER_DISPLAY"]   =   $data[0][3];

      ini_set('session.gc_maxlifetime', 86400);

      header("Location: dashboard/dashboard.php");

    }else{
      echo "passwords didnt match";
    }
    
    // Close the database connection
    $dbh->close();

}


?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
    integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="assets/css/main.css">
  <title>Pharma | Login</title>
</head>


  <body class="login-body">
    <main class="login">
        <form method="post">
            <div class="logo">
                <!-- <img src="assets/images/ucw-logo.png" alt=""> -->
            </div>
            <div class="heading text-center mt-3">Sign In To Start Session</div>
            <div class="row">
                <div class="f-control">
                    <input type="text" name="username" placeholder="User ID">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                </div>
                <div class="f-control">
                    <input type="password" name="password" placeholder="Password" class="password">
                    <div class="icon password-icon"><i class="fas fa-eye-slash"></i></div>
                </div>
                <div class="f-control">
                    <button class="btn w-100">Sign In</button>
                </div>

                <a href="">Forgot Password?</a>
            </div>
        </form>
    </main>
    <!--Container Main end-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/nav.js">
    </script>
    <!-- <script src="js/index.js"></script> -->
    <script>
      var passwordIcon = document.querySelector(".password-icon");
      passwordIcon.addEventListener("click", ()=>{
        if(passwordIcon.innerHTML == `<i class="fas fa-eye-slash"></i>`){
          passwordIcon.innerHTML = `<i class="fas fa-eye"></i>`
          document.querySelector(".password").setAttribute("type", "text");
        }
        else{
          passwordIcon.innerHTML = `<i class="fas fa-eye-slash"></i>`
          document.querySelector(".password").setAttribute("type", "password");
        }
      })
    </script>
  </body>

</html>