<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function generateRandomString($length = 6) {
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $original_url = $_POST['original_url'];
    $custom_alias = $_POST['custom_alias'];
    $unique_code = $_POST['unique_code'];

    if (!empty($custom_alias) && preg_match('/\s/', $custom_alias)) {
        $error_message = "Custom alias should not contain spaces. Please choose another one.";
    } elseif (empty($unique_code)) {
        $error_message = "Unique code is required.";
    } else {
        if (empty($custom_alias)) {
            // Generate a default alias if no custom alias is provided
            do {
                $custom_alias = generateRandomString();
                $stmt = $pdo->prepare("SELECT * FROM links WHERE short_url = ?");
                $stmt->execute([$custom_alias]);
            } while ($stmt->rowCount() > 0); // Ensure the alias is unique
        } else {
            // Check if custom alias is already taken
            $stmt = $pdo->prepare("SELECT * FROM links WHERE short_url = ?");
            $stmt->execute([$custom_alias]);
            if ($stmt->rowCount() > 0) {
                $error_message = "Custom alias already taken. Please choose another one.";
            }
        }

        if (empty($error_message)) {
            $encrypted_url = encrypt($original_url);
            $encrypted_unique_code = encrypt($unique_code); // Enkripsi kode unik
            $stmt = $pdo->prepare("INSERT INTO links (original_url, short_url, user_id, unique_code) VALUES (?, ?, ?, ?)");
            $stmt->execute([$encrypted_url, $custom_alias, $_SESSION['user_id'], $encrypted_unique_code]);
            header("Location: dashboard.php");
            exit();
        }        
    }
}

// Fetch all links created by the logged-in user
$stmt = $pdo->prepare("SELECT * FROM links WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$links = $stmt->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WELCOME TO WEB'S SHORTLINK</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<nav id="navbar" class="navbar navbar-expand-lg bg-white border-bottom border-2 shadow-sm fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <div class="brand d-none d-lg-block">
                <img src="assets/svg/brand.svg" alt="hayuJoin">
            </div>
            <div class="d-block d-lg-none">
                <img src="assets/icon/iconColored.png" alt="hayuJoin" width="30" height="30">
                <span class="text-black">HayuJoin</span>
            </div>
        </a>
        <button class="navbar-toggler border-0 bg-primary text-white rounded-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar" aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="nav nav-pills ms-auto mb-2 mb-lg-0">
                <li class="nav-item mx-4 my-2 rounded-0">
                    <a class="nav-link text-black" href="#home">HOME</a>
                </li>
                <li class="nav-item mx-4 my-2 rounded-0">
                    <a class="nav-link text-black" href="#short">SHORT</a>
                </li>
                <li class="nav-item mx-4 my-2 rounded-0">
                    <a class="nav-link text-black" href="logout.php">LOG OUT</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div data-bs-spy="scroll" data-bs-target="#navbar" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" class="scrollspy-example" tabindex="0">
    <section id="home">
        <div class="container py-5 my-5">
            <div class="row my-5 py-3 px-5">
                <div class="col-12 col-xl-6 text-center text-xl-start bg-white rounded-0 bg-opacity-75 py-5">
                    <h2 class="heading text-black mb-3">We Create Content that ranks</h2>
                    <a href="#short" class="btn btn-primary text-white py-2 px-4 rounded-0">URL SHORTENER</a>
                </div>
                <div class="col-6 d-none d-xl-block">
                    <img src="assets/svg/Main.svg" alt="Our Content" class="img-main">
                </div>
            </div>
        </div>
    </section>
    <section id="short">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Warning !!! </strong><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header text-center">
                            <h5>Halaman Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h5>
                        </div>
                        <form method="post">
                            <div class="card-body">
                                <label class="form-label"><h6>Original URL:</h6></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                                            <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                            <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                        </svg>
                                    </span>
                                    <input type="text" class="form-control" name="original_url" required placeholder="Masukan Original Url" aria-describedby="basic-addon3">
                                </div>
                                <label class="form-label"><h6>Custom Alias (optional):</h6></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                                            <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                            <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 1 0 1 0-4.243-4.243z"/>
                                        </svg>
                                    </span>
                                    <input type="text" class="form-control" name="custom_alias" placeholder="Masukan Custom Alias (Opsional)" aria-describedby="basic-addon3">
                                </div>
                                <label class="form-label"><h6>Unique Code:</h6></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key" viewBox="0 0 16 16">
                                            <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"/>
                                            <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                        </svg>
                                    </span>
                                    <input type="text" class="form-control" name="unique_code" required placeholder="Masukan Unique Code" aria-describedby="basic-addon3">
                                </div>
                                <div class="row">
                                    <button type="submit" class="btn btn-primary">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card mt-5">
                <?php
                // Fungsi untuk memotong URL
                function shorten_url($url, $length = 50) {
                    if (strlen($url) > $length) {
                        return substr($url, 0, $length) . '...';
                    } else {
                        return $url;
                    }
                }
                ?>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center">
                        <h5 class="text-center">Link yang anda miliki :</h5>
                        <thead>
                        <tr>
                            <th scope="col">Short URL</th>
                            <th scope="col">Original URL</th>
                            <th scope="col">Unique Code</th>
                            <th scope="col">Click Count</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($links as $link): ?>
                            <tr>
                                <td><a href="<?= $link['short_url'] ?>" target="_blank">http://localhost/shortlink/<?= $link['short_url'] ?></a></td>
                                <td><?= htmlspecialchars(shorten_url(decrypt($link['original_url']))) ?></td>
                                <td><?= htmlspecialchars(decrypt($link['unique_code'])) ?></td>
                                <td><?= htmlspecialchars($link['click_count']) ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $link['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete.php?id=<?= $link['id'] ?>" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to delete this link?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    
    <footer class="footer text-white">
        <div class="container py-1">
            <p class="m-0 text-center">2022 © All rights reserved by HayuJoin</p>
        </div>
    </footer>
</div>

<div class="card-footer bg-dark text-light text-center mt-5">
    Copyright © Indonesia 2024.Contact: <a href="mailto:dzikriamrillah86@gmail.com" class="text-light">dzikriamrillah86@gmail.com</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

</body>
</html>
