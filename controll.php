<?php
require 'config.php';

// Verifikasi apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Ambil data dari database
$stmt = $pdo->query("SELECT * FROM links");
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

function shorten_url($url, $length = 50) {
    if (strlen($url) > $length) {
        return substr($url, 0, $length) . '...';
    } else {
        return $url;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Web's Shortlink</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container mt-5">
        <h1 class="mb-4">Admin Panel</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Short URL</th>
                    <th>Original URL (Encrypted)</th>
                    <th>Unique Code (Encrypted)</th>
                    <th>Click Count</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($links as $link): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($link['id']); ?></td>
                        <td><?php echo htmlspecialchars($link['short_url']); ?></td>
                        <td><?php echo htmlspecialchars(shorten_url($link['original_url'])); ?></td>
                        <td><?php echo htmlspecialchars($link['unique_code']); ?></td>
                        <td><?php echo htmlspecialchars($link['click_count']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-dark text-light text-center mt-5">
    Copyright © Indonesia 2024.Contact: <a href="mailto:dzikriamrillah86@gmail.com" class="text-light">dzikriamrillah86@gmail.com</a>
</div>
</body>
</html>
