<?php
require 'config.php';

if (isset($_GET['short'])) {
    $short_url = $_GET['short'];
    $stmt = $pdo->prepare("SELECT original_url, unique_code, click_count FROM links WHERE short_url = ?");
    $stmt->execute([$short_url]);
    $link = $stmt->fetch();

    if ($link) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $unique_code = $_POST['unique_code'];
            $decrypted_unique_code = decrypt($link['unique_code']); // Dekripsi kode unik

            if ($unique_code === $decrypted_unique_code) {
                $original_url = decrypt($link['original_url']);

                // Increment the click count
                $stmt = $pdo->prepare("UPDATE links SET click_count = click_count + 1 WHERE short_url = ?");
                $stmt->execute([$short_url]);

                header("Location: " . $original_url);
                exit();
            } else {
                $error_message = "Invalid unique code. Please try again.";
            }
        }
    } else {
        $error_message = "URL not found.";
    }
}
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
            <button class="navbar-toggler border-0 bg-primary text-white rounded-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
                <ul class="navbar-nav nav-pills ms-auto mb-2 mb-lg-0">
                    <li class="nav-item mx-4 my-2 rounded-0">
                        <form class="d-flex" role="search" id="userNav">
                            <a href="login.php" class="btn btn-outline-secondary fw-semibold rounded-5 px-4 me-2">Login</a>
                            <a href="register.php" class="btn btn-primary fw-semibold rounded-5 px-4">SignUp</a>
                         </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div data-bs-spy="scroll" data-bs-target="#navbar" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" class="scrollspy-example" tabindex="0">
        <section id="home">
            <div class="container py-5 my-5">
                <div class="row my-5 py-3 px-5">
                <?php if (isset($_GET['short']) && $link): ?>
                    <div class="col-12 col-xl-6 text-center text-xl-start bg-white rounded-0 bg-opacity-75 py-5">
                    <h2 class="heading text-black mb-3">Enter Unique Code</h2>
                    <?php if (isset($error_message)): ?>
                                    <p style="color: red;"><?php echo $error_message; ?></p>
                                <?php endif; ?>
                                <form method="post">
                                    <input type="text" name="unique_code" required><br>
                                    <button type="submit" class="btn btn-primary text-white mt-2 py-2 px-4 rounded-0">Submit</button>
                                </form>
                    </div>
                                
                                
                        <?php else: ?>
                            
                    <div class="col-12 col-xl-6 text-center text-xl-start bg-white rounded-0 bg-opacity-75 py-5">
                        <h2 class="heading text-black mb-3">You Are The Owner Of Your Link</h2>
                        <a href="register.php"><button type="button" data-bs-toggle="modal" class="btn btn-primary text-white py-2 px-4 rounded-0">GET STARTED</button></a> 
                    </div>
                    <?php endif; ?>
                    <div class="col-6 d-none d-xl-block">
                        <img src="assets/svg/Main.svg" alt="Our Content" class="img-main">
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="card-footer bg-dark text-light text-center fixed-bottom">
    Copyright © Indonesia 2024.Contact: <a href="mailto:dzikriamrillah86@gmail.com" class="text-light">dzikriamrillah86@gmail.com</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
