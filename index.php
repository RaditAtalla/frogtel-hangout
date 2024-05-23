<?php
  $conn = mysqli_connect('localhost', 'root', '', 'frogtel_forum');

  // Read posts
  $fetchPosts = mysqli_query($conn, "SELECT * FROM posts");
  $posts = [];
  while ($post = mysqli_fetch_assoc($fetchPosts)) {
    $posts[] = $post;
  }

  // Create posts
  if(isset($_POST['post'])) {
    $displayName = htmlspecialchars($_POST['display-name']);
    $comment = htmlspecialchars($_POST['comment']);

    $insert = mysqli_query($conn, "INSERT INTO posts VALUES('', '$displayName', '$comment')");
    if($insert) {
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
</head>
<body class="">
  <main class="mx-2 sm:mx-[10rem] my-[3rem]">
    <h1 class="font-black text-5xl mb-5">Frogtel <br>Hangout</h1>

    <form action="" method="post" class="flex flex-col mb-10">
      <h2 class="text-3xl font-bold">Create post</h2>
      <label for="display-name" class="text-xl">Display name</label>
      <input type="text" id="display-name" name="display-name" class="p-2 border border-black mb-3" required>
      <label for="comment" id="comment" class="text-xl">Comment</label>
      <textarea name="comment" id="comment" rows="5" class="p-2 border border-black" required></textarea>
      <button type="submit" name="post" class="text-xl bg-blue-500 text-white py-1 px-2">Post</button>
    </form>

    <div>
      <h2 class="text-3xl font-bold">View other's posts</h2>
      <button type="submit" name="refresh" onclick="location.reload()" class="text-xl bg-blue-500 text-white py-1 px-2">Refresh</button>
      <?php foreach($posts as $post) : ?>
        <div class="bg-gray-200 my-5 p-2">
          <h3 class="text-3xl font-medium"><?= $post['username'] ?></h3>
          <p class="text-2xl"><?= $post['comment'] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
</body>
</html>