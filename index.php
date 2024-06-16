<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['account'])) {
  $_SESSION['account'] = '';
}

// Create posts
if (isset($_POST['post'])) {
  $displayName = $_SESSION['account'];
  $comment = nl2br(htmlspecialchars($_POST['comment']));
  $image_basename = '';

  if ($_FILES['image-upload']['error'] != 4) {
    $upload_directory = 'uploads/';
    $image_basename = basename($_FILES['image-upload']['name']);
    $image_name = $upload_directory . $image_basename;
    $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

    $check_image = getimagesize($_FILES['image-upload']["tmp_name"]);
    if ($check_image === false) {
      echo "file must be image";
      return;
    }

    if ($_FILES['image-upload']['size'] > 5000000) {
      echo "file must be below 5mb";
      return;
    }

    if ($image_extension != "jpg" && $image_extension != "png" && $image_extension != "jpeg" && $image_extension != "webp") {
      echo "file must be either jpg, png, jpeg, or webp";
      return;
    }

    if (!move_uploaded_file($_FILES['image-upload']['tmp_name'], $image_name)) {
      echo "file not uploaded, error occurred";
      return;
    }
  }

  $insert = mysqli_query($conn, "INSERT INTO posts VALUES('', '$displayName', '$comment', '$image_basename', NOW())");
  if ($insert) {
    header('Location: ./');
  }
}

// Read posts
$fetchPosts = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC");
$posts = [];
while ($post = mysqli_fetch_assoc($fetchPosts)) {
  $posts[] = $post;
}

// Delete posts
if (isset($_POST['delete'])) {
  $post_id = $_POST['deleteID'];

  foreach ($posts as $post) {
    if ($post_id == $post['id']) {
      unlink("uploads/" . $post["image"]);
    }
  }

  if ($_SESSION['account'] == 'super_admin') {
    mysqli_query($conn, "UPDATE posts SET comment = '<p class=\"italic text-neutral-500 text-[1.25rem]\">[Post redacted by admin]</p>', image = '' WHERE id = $post_id ");
    header('Location: ./');
    return;
  }

  mysqli_query($conn, "DELETE FROM posts WHERE id=$post_id");
  header('Location: ./');
}

// get account info
if($_SESSION['account'] != '') {
  $login = $_SESSION['account'];
  $fetch_account_data = mysqli_query($conn, "SELECT * FROM users WHERE username = '$login'");
  $account_datas = [];
  while ($account_data = mysqli_fetch_array($fetch_account_data)) {
    $account_datas[] = $account_data;
  }
  $account_id = $account_datas[0]['id'];
}

// Like post
if (isset($_POST['like'])) {
  if ($_SESSION['account'] == '') {
    header("Location: login.php");
    return;
  }

  $like_id = $_POST['likeID'];

  $check_duplicate = mysqli_query($conn, "SELECT * FROM post_likes WHERE user_id=$account_id AND post_id=$like_id");
  if (mysqli_num_rows($check_duplicate) > 0) {
    mysqli_query($conn, "DELETE FROM post_likes WHERE user_id=$account_id AND post_id=$like_id ");
    header('Location: ./');
  } else {
    mysqli_query($conn, "INSERT INTO post_likes VALUES('', '$like_id', '$account_id')");
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
          <div class="flex justify-between items-center">
            <div>
              <h3 class="text-[1.5rem] font-medium leading-none"><?= $post['username'] ?></h3>
              <p class="mt-[3px] text-neutral-400 font-medium"><?= $post['upload_date'] ?></p>
            </div>
            <?php if ($post['username'] == $_SESSION['account'] || $_SESSION['account'] == 'super_admin') : ?>
              <form action="" method="post">
                <input type="text" hidden name="deleteID" value="<?= $post['id'] ?>">
                <button type="submit" onclick="alert('Post deleted')" name="delete"><i data-feather="trash" class="text-green-800 hover:text-red-800 transition cursor-pointer"></i></button>
              </form>
            <?php endif ?>
          </div>
          <p class="text-[1.25rem] mb-[0.5em]"><?= $post['comment'] ?></p>
          <?php if ($post['image'] != '') : ?>
            <img loading="lazy" src="uploads/<?= $post['image'] ?>" class="w-full rounded">
          <?php endif ?>
          <form method="post" class="flex gap-[0.5em] items-center mt-[1em]">
            <input type="text" name="likeID" value="<?= $post['id'] ?>" class="hidden">
            <?php
            if ($_SESSION['account'] != '') {
              $post_id = $post['id'];
              $already_liked_query = mysqli_query($conn, "SELECT * FROM post_likes WHERE post_id = $post_id AND user_id = $account_id");
              $num_rows = mysqli_num_rows($already_liked_query);
              $already_liked = $num_rows > 0 ? 'fill-green-800' : 'fill-light';
            }
            ?>
            <button type="submit" name="like"><i data-feather="heart" class="text-green-800 w-[18px] h-[18px] transition <?php if(isset($already_liked)) echo $already_liked ?>"></i></button>
            <p><?php $post_id = $post['id']; echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM post_likes WHERE post_id = $post_id"));  ?></p>
          </form>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ($_SESSION['account'] == '') : ?>
      <div class="flex flex-col w-full lg:w-1/3">
        <h2 class="text-center text-[2em] font-bold text-green-800 mb-[1em]">Log in to post</h2>
        <a href="login.php" class="text-center text-[1.5rem] font-bold bg-green-800 rounded text-white py-1 px-2 mb-[0.25em]">Log in</a>
        <a href="register.php" class="text-center text-[1.5rem] font-bold border border-green-800 rounded text-green-800 py-1 px-2">Register</a>
      </div>
    <?php else : ?>
      <form action="" method="post" enctype="multipart/form-data" class="flex flex-col w-full lg:w-1/3">
        <h2 class="text-[2em] font-bold text-green-800">Create new post</h2>
        <div class="flex justify-between items-center mb-[1em]">
          <p class="text-[1.5rem] font-medium">Account: <?= $_SESSION['account'] ?></p>
          <a href="logout.php" class="underline text-green-800 font-semibold">log out</a>
        </div>
        <label for="comment" id="comment" class="text-[1.5rem] mb-[0.5em]">What do you want to say?</label>
        <div class="p-2 border border-zinc-400 rounded mb-[1.5em]">
          <textarea name="comment" id="comment" rows="5" class="h-auto resize-none w-full focus:outline-none" required></textarea>
          <label for="image-upload" class="flex gap-[0.5em] cursor-pointer">
            <i data-feather="image" class="text-green-800"></i>
            <p class="text-green-800 hover:underline">Add image</p>
          </label>
          <input type="file" onchange="loadPreview(event)" id="image-upload" name="image-upload" class="hidden">
          <div id="image-preview-container" class="mt-[1em] relative"></div>
        </div>
        <button type="submit" name="post" class="text-[1.5rem] font-bold bg-green-800 rounded text-white py-1 px-2">Post</button>
      </form>
    <?php endif; ?>
  </main>

  <script>
    feather.replace();

    function loadPreview(event) {
      let image = URL.createObjectURL(event.target.files[0]);
      let imagePreviewContainer = document.getElementById("image-preview-container")

      let imagePreview = document.createElement("img")
      imagePreview.setAttribute("class", "w-full rounded object-cover")
      imagePreview.setAttribute("id", "image-preview")
      imagePreview.setAttribute("src", image)

      let removeButton = document.createElement("p")
      removeButton.innerText = "Remove"
      removeButton.setAttribute("class", "cursor-pointer bg-green-800 text-white font-medium absolute rounded-full px-[1em] py-[0.25em] right-[1em] top-[1em]")
      removeButton.setAttribute("onclick", "removeImage()")
      removeButton.setAttribute("id", "remove-button")

      imagePreviewContainer.appendChild(imagePreview)
      imagePreviewContainer.appendChild(removeButton)
    }

    function removeImage() {
      let inputImage = document.getElementById("image-upload")
      let imagePreviewContainer = document.getElementById("image-preview-container")

      inputImage.value = ""
      imagePreviewContainer.innerHTML = ""
    }
  </script>
</body>

</html>