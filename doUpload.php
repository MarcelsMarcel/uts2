    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login first.";
    header("Location: login.php");
    exit();
}
    // ur mission is after create connection to database, you need to implement the following functionalities in this file:
    // 1. Create a POST request to handle file upload form submission & validate the user session to ensure the user is authenticated before allowing file upload
    // 2. Validate the uploaded file to ensure it meets the required criteria (e.g., file type, size limit)
    // try to limit the file size to 5MB and only allow certain file types (e.g., PDF, DOCX, JPG, PNG)
    // 3. Move the uploaded file to a designated directory on the server (e.g., "uploads/")
    // 4. Store the file information (e.g., file name, size, upload date, user ID) in the database for future reference

    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_FILES['fileUpload'])) {
        $user_id = $_SESSION['user_id'];
        $file = $_FILES['fileUpload'];

        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "kegedean jir sus";
            header("location: upload.php");
            exit;
        }

        $docx = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $allowed = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            $docx => 'docx',
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $original_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!isset($allowed[$mime])) {
            $_SESSION['error'] = "Invalid file type. Only PDF, DOCX, JPG, and PNG files are allowed.";
            header("Location: upload.php");
            exit();
        }

        $upload_dir  = __DIR__ . '/uploads/';
        $stored_name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        $target_file = $upload_dir . $stored_name;
        $original_name = basename($file['name']);
        move_uploaded_file($file['tmp_name'], $target_file);


        $conn = new mysqli('localhost', 'root', '', 'uts2');
        $stmt = $conn->prepare("INSERT INTO files (user_id, original_name, stored_name, file_size, file_type) VALUES (?, ?, ?, ?, ?)");
        $size = (int)$file['size'];
        $stmt->bind_param("issis", $user_id, $original_name, $stored_name, $size, $mime);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            $_SESSION['success'] = "file uploaded";
            header("location: index.php");
            exit();
        }

        $stmt->close();
        $conn->close();
        $_SESSION['error'] = "gagal";
        header("location: upload.php");
        exit();
        
    } else { 
        $_SESSION['error'] = "invalid request";
        header("location: upload.php");
        exit;
    }