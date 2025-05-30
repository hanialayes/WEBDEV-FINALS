<?php
session_start();
include 'dbh.php';

if (!isset($_SESSION['name'])) {
    header('Location: ../index.php');
    exit();
}

if (isset($_POST['submit'])) {
    $videoTitle = mysqli_real_escape_string($conn, $_POST['video_title']);

    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('mp4', 'mov', 'mkv');

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 314572800) { // 300MB max
                $fileNameNew = uniqid('', true) . "." . $fileActualExt;
                $fileDestination = 'uploads/' . $fileNameNew;
                move_uploaded_file($fileTmpName, $fileDestination);

                // Get user ID from username
                $userId = $_SESSION['id'];

                // Insert into videos table including video_title
                $stmt = $conn->prepare("INSERT INTO videos (user_id, name, title, status) VALUES (?, ?, ?, 0)");
                $stmt->bind_param("iss", $userId, $fileNameNew, $videoTitle);
                $stmt->execute();

                header("Location: dashboard.php?uploadsuccess");
                exit();
            } else {
                echo "Your file is too big.";
            }
        } else {
            echo "There was an error uploading your file!";
        }
    } else {
        echo "You can't upload files of this type.";
    }
}
?>
