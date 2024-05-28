<?php
session_start();
require 'functions.php';

$users_data = mysqli_query($conn, "SELECT username FROM users");
$users = [];
while ($user = mysqli_fetch_assoc($users_data)) {
  $users[] = $user;
}

if (isset($_POST['register'])) {
  $username = htmlspecialchars($_POST['username']);
  $password = htmlspecialchars($_POST['password']);
  $confirm_password = htmlspecialchars($_POST['confirm-password']);

  // Check for username availablity
  for ($i = 0; $i < count($users); $i++) {
    if ($username === $users[$i]['username']) {
      echo '<script>alert("Username has been taken");</script>';
    }
  }

  // Check for password
  $password_match = $password == $confirm_password ? true : false;
  if (!$password_match) {
    echo '<script>alert("Password doesn\'t match!");</script>';
    return;
  }

  // Hash password
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

  // Send data to db
  mysqli_query($conn, "INSERT INTO users VALUES('', '$username', '$hashed_password')");
  $_SESSION['account'] = $username;
  header('Location: ./');
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

<body class="flex flex-col items-center">
  <header class="mt-[3rem] mb-[2rem]">
    <h1 class="font-black text-[3rem] mb-5 text-green-800 leading-none text-center"><a href="./">Frogtel <br><span class="text-black">Hangout</span></a></h1>
  </header>

  <form action="" method="post" class="flex flex-col w-[500px]">
    <h2 class="text-[2em] font-bold text-green-800 text-center">Register Account</h2>
    <label for="username" class="text-[1.5rem] mb-[0.5em]">Username</label>
    <input type="text" id="username" name="username" class="p-2 border border-zinc-400 rounded mb-[1.5em]" required>
    <label for="password" id="password" class="text-[1.5rem] mb-[0.5em]">Password</label>
    <input type="password" id="password" name="password" class="p-2 border border-zinc-400 rounded mb-[1.5em]" required>
    <label for="confirm-password" id="confirm-password" class="text-[1.5rem] mb-[0.5em]">Confirm Password</label>
    <input type="password" id="confirm-password" name="confirm-password" class="p-2 border border-zinc-400 rounded mb-[1.5em]" required>
    <a href="login.php" class="underline text-green-800 font-semibold">login here</a>
    <button type="submit" name="register" class="text-[1.5rem] font-bold bg-green-800 rounded text-white py-1 px-2">Register</button>
  </form>
  <script>
    feather.replace();
  </script>
</body>

</html>