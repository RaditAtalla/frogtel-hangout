  <?php
  session_start();
  require 'functions.php';

  // redirect to homepage if already logged in
  if (isset($_SESSION['account']) && $_SESSION['account'] != '') {
    header('Location: ./');
  }

  $_SESSION['isUsernameExist'] = True;
  $_SESSION['isPasswordCorrect'] = True;

  // run when login is pressed
  if (isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $user_data = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    // check username
    if (mysqli_affected_rows($conn) == 0) {
      $_SESSION['isUsernameExist'] = false;
      goto end;
    }

    // fetch user data
    $datas = [];
    while ($data = mysqli_fetch_assoc($user_data)) {
      $datas[] = $data;
    }

    // check password
    $data_password = $datas[0]['password'];
    $is_password_correct = password_verify($password, $data_password);
    if (!$is_password_correct) {
      $_SESSION['isPasswordCorrect'] = false;
    }

    end:

    // redirect to homepage on login success
    if ($_SESSION['isUsernameExist'] && $_SESSION['isPasswordCorrect']) {
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
    <title>Frogtel | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
  </head>

  <body class="flex flex-col items-center px-[1em] sm:px-0">
    <header class="mt-[3rem] mb-[2rem]">
      <h1 id="title" class="font-black text-[3rem] mb-5 text-green-800 leading-none text-center"><a href="./">Frogtel <br><span class="text-black">Hangout</span></a></h1>
    </header>

    <form action="" method="post" class="flex flex-col w-full sm:w-[500px]">
      <h2 class="text-[2em] font-bold text-green-800 text-center">Log in</h2>
      <label for="username" class="text-[1.5rem]">Username</label>
      <?php if ($_SESSION['isUsernameExist'] == false) : ?>
        <p class="text-red-500 italic">Username not found!</p>
      <?php endif; ?>
      <input type="text" id="username" name="username" class="p-2 border border-zinc-400 rounded mb-[1.5em]" required>
      <label for="password" class="text-[1.5rem]">Password</label>
      <?php if ($_SESSION['isPasswordCorrect'] == false) : ?>
        <p class="text-red-500 italic">Wrong password</p>
      <?php endif; ?>
      <div class="flex items-center gap-[0.5em] border border-zinc-400 rounded mb-[1.5em] p-2">
        <input type="password" id="password" name="password" class="w-full focus:outline-none" required>
        <i data-feather="eye-off" id="eye" class="text-green-800"></i>
      </div>
      <a href="register.php" class="underline text-green-800 font-semibold">register here</a>
      <button type="submit" name="login" class="text-[1.5rem] font-bold bg-green-800 rounded text-white py-1 px-2">Log in</button>
    </form>
    <script>
      feather.replace();

      let password = document.getElementById("password");
      let eye = document.getElementById("eye");

      eye.addEventListener("click", () => {
        if(password.getAttribute("type") == "password") {
          password.setAttribute("type", "text")
          password.setAttribute("data-feather", "eye")
        } else {
          password.setAttribute("type", "password")
          password.setAttribute("data-feather", "eye-off")
        }
      })

    </script>
  </body>

  </html>