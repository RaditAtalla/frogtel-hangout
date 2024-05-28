<?php
session_start();
require 'functions.php';

if(isset($_SESSION['account'])) {
  header('Location: ./');
}

if (isset($_POST['login'])) {
  $username = htmlspecialchars($_POST['username']);
  $password = htmlspecialchars($_POST['password']);

  $user_data = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

  if(mysqli_affected_rows($conn) == 0) {
    echo 'Username doesn\'t exist';
    return;
  }

  $datas = [];
  while ($data = mysqli_fetch_assoc($user_data)) {
    $datas[] = $data;
  }

  $data_password = $datas[0]['password'];
  $is_password_correct = password_verify($password, $data_password);

  if(!$is_password_correct) {
    echo 'Password incorrect';
    return;
  }

  $_SESSION['account'] = $username;
  header('Location: ./');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Frogtel | Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="flex flex-col items-center px-[1em] sm:px-0">
  <header class="mt-[3rem] mb-[2rem]">
    <h1 class="font-black text-[3rem] mb-5 text-green-800 leading-none text-center"><a href="./">Frogtel <br><span class="text-black">Hangout</span></a></h1>
  </header>

  <form action="" method="post" class="flex flex-col w-full sm:w-[500px]">
    <h2 class="text-[2em] font-bold text-green-800 text-center">Log in</h2>
    <label for="username" class="text-[1.5rem] mb-[0.5em]">Username</label>
    <input type="text" id="username" name="username" class="p-2 border border-zinc-400 rounded mb-[1.5em]" required>
    <label for="password" id="password" class="text-[1.5rem] mb-[0.5em]">Password</label>
    <input type="password" id="password" name="password" class="p-2 border border-zinc-400 rounded mb-[1.5em]" required>
    <a href="register.php" class="underline text-green-800 font-semibold">register here</a>
    <button type="submit" name="login" class="text-[1.5rem] font-bold bg-green-800 rounded text-white py-1 px-2">Log in</button>
  </form>
  <script>
    feather.replace();
  </script>
</body>

</html>