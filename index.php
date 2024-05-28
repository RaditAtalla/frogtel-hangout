<?php
session_start();
require 'functions.php';

// Read posts
$fetchPosts = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC");
$posts = [];
while ($post = mysqli_fetch_assoc($fetchPosts)) {
  $posts[] = $post;
}

// Create posts
if (isset($_POST['post'])) {
  $displayName = $_SESSION['account'];
  $comment = htmlspecialchars($_POST['comment']);

  $insert = mysqli_query($conn, "INSERT INTO posts VALUES('', '$displayName', '$comment')");
  if ($insert) {
    header('Location: ./');
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Frogtel | Hangout</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="px-[1em] lg:px-[10em] py-[3em]">
  <header class="mb-[3rem]">
    <h1 class="text-center lg:text-left font-black text-[3rem] mb-5 text-green-800 leading-none"><a href="./">Frogtel <br><span class="text-black">Hangout</span></a></h1>
  </header>

  <main class="flex flex-col-reverse lg:flex-row gap-[2em]">
    <div class="w-full lg:w-2/3">
      <div class="flex items-center gap-3">
        <h2 class="text-[2em] text-green-800 font-bold">Posts</h2>
        <i data-feather="refresh-cw" class="text-green-800 cursor-pointer" onclick="location.reload()"></i>
      </div>
      <?php foreach ($posts as $post) : ?>
        <div class="mb-[1em] border border-zinc-300 rounded pt-[1.5em] pb-[1em] px-[1em]">
          <h3 class="text-[1.5rem] font-medium"><?= $post['username'] ?></h3>
          <p class="text-[1.25rem]"><?= $post['comment'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (!isset($_SESSION['account'])) : ?>
      <div class="flex flex-col w-full lg:w-1/3">
        <h2 class="text-center text-[2em] font-bold text-green-800 mb-[1em]">Log in to post</h2>
        <a href="login.php" class="text-center text-[1.5rem] font-bold bg-green-800 rounded text-white py-1 px-2 mb-[0.25em]">Log in</a>
        <a href="register.php" class="text-center text-[1.5rem] font-bold border border-green-800 rounded text-green-800 py-1 px-2">Register</a>
      </div>
    <?php else : ?>
      <form action="" method="post" class="flex flex-col w-full lg:w-1/3">
        <h2 class="text-[2em] font-bold text-green-800">Create new post</h2>
        <div class="flex justify-between items-center mb-[1em]">
          <p class="text-[1.5rem] font-medium">Account: <?= $_SESSION['account'] ?></p>
          <a href="logout.php" class="underline text-green-800 font-semibold">log out</a>
        </div>
        <label for="comment" id="comment" class="text-[1.5rem] mb-[0.5em]">What do you want to say?</label>
        <textarea name="comment" id="comment" rows="5" class="resize-none p-2 border border-zinc-400 rounded mb-[1.5em]" required></textarea>
        <button type="submit" name="post" class="text-[1.5rem] font-bold bg-green-800 rounded text-white py-1 px-2">Post</button>
      </form>
    <?php endif; ?>
  </main>

  <script>
    feather.replace();
  </script>
</body>

</html>