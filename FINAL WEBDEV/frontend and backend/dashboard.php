<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../index.php'); 
    exit();
}

$name = $_SESSION['name'];
$userId = $_SESSION['id'];

$sqlVideo = "SELECT * FROM videos WHERE user_id = '$userId'";
$resultVideo = mysqli_query($conn, $sqlVideo);
$html = [];
if (mysqli_num_rows($resultVideo) > 0) {
    while ($rowVideo = mysqli_fetch_assoc($resultVideo)) {
        $videoId = $rowVideo['id'];
        $videoFile = htmlspecialchars($rowVideo['name']);
        $videoTitle = htmlspecialchars($rowVideo['title']);
        $videoPath = "uploads/" . $videoFile;
        $html[] =  "<div class='video-slide'>
                <video controls>
                    <source src='$videoPath' type='video/mp4'>
                    Your browser does not support the video tag.
                </video>
                <p class='video-title'>$videoTitle</p>
                <form action='delete_video.php' method='POST' onsubmit='return confirm(\"Delete this video?\")'>
                    <input type='hidden' name='video_id' value='$videoId'>
                    <input type='hidden' name='video_file' value='$videoFile'>
                    <button type='submit' name='delete'>Delete</button>
                </form>
            </div>";
    }
} else {
    $html[] = "<p class='no-videos'>You have no uploaded videos yet.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
</head>
<body>

<header class="topbar">
    <a href="Homepage.php" class="logo" style="color:black;">Ani</a><a href="Homepage.php" class="logo" style="color:red;">Mate</a>

    <div class="searchbar">
        <form method="GET" action="dashboard.php">
            <input 
                type="text" 
                name="search" 
                placeholder="Search Your Anime" 
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
            >
        </form>
    </div>


    <div class="help">
        <button id="helpBtn">Help</button>
    </div>
</header>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="profile">
            <div class="avatar-circle"><?= strtoupper($name[0]) ?></div>
            <h2>Your Otaku Studio</h2>
            <p><?= htmlspecialchars($name) ?></p>
        </div>
        <nav class="sidebar-nav">
            <a href="#" class="active">Dashboard</a>
            <a href="#">Post</a>
            <a href="#">Analytics</a>
            <a href="#">Community</a>
            <a href="#">Earning</a>
            <a href="#">Settings</a>
            <a href="#">Feedback</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1>Dashboard</h1>
        <h2>Your Anime</h2>
        <div class="video-slider-wrapper">
            <div class="video-slider">
                <?php echo implode("\n", $html); ?>
            </div>

            <div class="upload-form">
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <input type="text" name="video_title" class="input-title" placeholder="Enter video title" required>
                    <label for="file-upload" class="custom-file-upload" placeholder="Select a Video">
                        Choose a File
                    </label>
                    <input id="file-upload" type="file" name="file" class="file-upload" required>

                    <!-- <input type="file" name="file" class="file-upload" required> -->
                    <button type="submit" name="submit">Upload</button>
                </form>
            </div>


            <h2 class="playlistsTitle">Your Playlist</h2>
            <div class="playlists">
                <p>You have no playlists yet.</p>
            </div>
        </div>
    </main>
</div>
</body>
</html>
