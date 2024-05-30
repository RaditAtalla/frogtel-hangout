<?php
session_start();
require 'functions.php';

$users_data = mysqli_query($conn, "SELECT username FROM users");
$users = [];
while ($user = mysqli_fetch_assoc($users_data)) {
  $users[] = $user;
}

$_SESSION['isUsernameAvailable'] = true;
$_SESSION['isPasswordMatch'] = true;

if (isset($_POST['register'])) {
  $username = htmlspecialchars($_POST['username']);
  $password = htmlspecialchars($_POST['password']);
  $confirm_password = htmlspecialchars($_POST['confirm-password']);

  // Check for username availablity
  for ($i = 0; $i < count($users); $i++) {
    if ($username === $users[$i]['username']) {
      $_SESSION['isUsernameAvailable'] = false;
    }
  }

  // Check for password
  $password_match = $password == $confirm_password ? true : false;
  if (!$password_match) {
    $_SESSION['isPasswordMatch'] = false;
  }

  if($_SESSION['isUsernameAvailable'] && $_SESSION['isPasswordMatch']) {
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
  
    // Send data to db
    mysqli_query($conn, "INSERT INTO users VALUES('', '$username', '$hashed_password')");
    $_SESSION['account'] = $username;
    header('Location: ./');
  }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Frogtel | Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="flex flex-col items-center px-[1em] sm:px-0">
  <header class="mt-[3rem] mb-[2rem]">
    <h1 class="font-black text-[3rem] mb-5 text-green-800 leading-none text-center"><a href="./">Frogtel <br><span class="text-black">Hangout</span></a></h1>
  </header>

  <form action="" method="post" class="flex flex-col w-full sm:w-[500px]">
    <h2 class="text-[2em] font-bold text-green-800 text-center">Register Account</h2>
    <label for="username" class="text-[1.5rem]">Username</label>
    <?php if(!$_SESSION['isUsernameAvailable']) : ?>
    <p class="text-red-500 italic">Username Taken</p>
    <?php endif; ?>
    <input type="text" id="username" name="username" class="p-2 border border-zinc-400 rounded mb-[1.5em]" required>
    <label for="password" class="text-[1.5rem]">Password</label>
    <div class="flex items-center gap-[0.5em] border border-zinc-400 rounded mb-[1.5em] p-2">
      <input type="password" id="password" name="password" class="w-full focus:outline-none" required>
      <i data-feather="eye-off" id="eye" class="text-green-800"></i>
    </div>
    <label for="confirm-password" class="text-[1.5rem]">Confirm Password</label>
    <?php if(!$_SESSION['isPasswordMatch']) : ?>
    <p class="text-red-500 italic">Password doesn't match</p>
    <?php endif; ?>
    <div class="flex items-center gap-[0.5em] border border-zinc-400 rounded mb-[1.5em] p-2">
      <input type="password" id="confirm-password" name="confirm-password" class="w-full focus:outline-none" required>
      <i data-feather="eye-off" id="confirm-eye" class="text-green-800"></i>
    </div>
    <a href="login.php" class="underline text-green-800 font-semibold">login here</a>
    <button type="submit" name="register" class="text-[1.5rem] font-bold bg-green-800 rounded text-white py-1 px-2">Register</button>
  </form>
  <script>
    feather.replace();

    let password = document.getElementById("password");
    let confirmPassword = document.getElementById("confirm-password")
    let eye = document.getElementById("eye");
    let confirmEye = document.getElementById("confirm-eye")

    eye.addEventListener("click", () => {
      if(password.getAttribute("type") == "password") {
        password.setAttribute("type", "text")
      } else {
        password.setAttribute("type", "password")
      }
    })

    confirmEye.addEventListener("click", () => {
      if(confirmPassword.getAttribute("type") == "password") {
        confirmPassword.setAttribute("type", "text")
      } else {
        confirmPassword.setAttribute("type", "password")
      }
    })
  </script>
</body>

</html>