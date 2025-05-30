<?php
session_start();
include 'dbh.php';

if (!isset($_SESSION['name']) || !isset($_POST['delete'])) {
    header('Location: dashboard.php');
    exit();
}

$videoId = intval($_POST['video_id']);
$videoFile = $_POST['video_file'];
$videoPath = "uploads/" . basename($videoFile);

// Make sure the video belongs to the logged-in user
$userId = $_SESSION['id'];

$check = mysqli_query($conn, "SELECT * FROM videos WHERE id = $videoId AND user_id = $userId");
if (mysqli_num_rows($check) > 0) {
    // Delete file from folder
    if (file_exists($videoPath)) {
        unlink($videoPath);
    }

    // Delete from DB
    mysqli_query($conn, "DELETE FROM videos WHERE id = $videoId");
} 
header('Location: dashboard.php');
exit();
?>
