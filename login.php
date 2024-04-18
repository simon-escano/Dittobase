<?php
require 'php/connect.php';

if ($currentUser) session_destroy();
$userAccounts = $db->select('tblUserAccount', '*');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DittoBase | Login</title>
    
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/shared.css">
</head>
<body>
    <section id="container">
        <header>DittoBase</header>

        <section id="banner">
            <img id="banner-img" src="img/banner_login.png">
        </section>
    
        <nav>
            <button id="register" class="nav-button">REGISTER <span>▶</span></button>
            <button id="login" class="nav-button">LOGIN <span>▶</span></button>
            <button id="home" class="nav-button">HOME <span>▶</span></button>
            <button id="about-us" class="nav-button">ABOUT US <span>▶</span></button>
            <button id="contact-us" class="nav-button">CONTACT US <span>▶</span></button>
        </nav>

        <main>
            <div class="card">
                <section class="card-header">
                    <div class="card-img-box">
                        <img src="img/icon_login.png">
                    </div>
                    <p class="card-title">LOGIN</p>
                </section>
                <section class="card-content">
                    <form action="" method="post">
                        <div class="form-input">
                            <label for="username">Username</label>
                            <input id="username" name="username" type="text" required>
                        </div>
                        <div class="form-input">
                            <label for="password">Password</label>
                            <input id="password" name="password" type="password" required>
                        </div>

                        <?php
                        if (!empty($_POST)) {
                            if (isset($_POST['username'], $_POST['password'])) {
                                $username = $_POST['username'];
                                $password = $_POST['password'];
                                if (!empty($username) && !empty($password)) {
                                    foreach($userAccounts as $user) {
                                        if ($user['username'] == $username && $user['password'] == $password) {
                                            $_SESSION['trainerID'] = $user['trainerID'];
                                            header('Location: index.php');
                                            exit();
                                        }
                                    }
                                    echo '<div class="err-msg">Incorrect username or password, Brah!</div>';
                                }
                            }
                        }
                        ?>

                        <input class="submit-button" id="login-button" type="submit" value="Login">
                    </form>
                </section>
            </div>
        </main>
        
        <footer>
            <p>Simon Escaño and Malt Solon</p>
            <p>BSCS-2</p>
        </footer>
    </section>

    <script src="js/shared.js"></script>
</body>
</html>