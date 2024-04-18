<?php
require 'php/connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DittoBase | Register</title>
    
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
            <img id="banner-img" src="img/banner_register.png">
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
                        <img src="./img/icon_register.png">
                    </div>
                    <p class="card-title">REGISTER</p>
                </section>
                <section class="card-content">
                    <form action="" method="post">
                        <div class="form-inputs">
                            <div class="form-input">
                                <label for="firstname">First name</label>
                                <input id="firstname" name="firstname" type="text" required>
                            </div>
                            <div class="form-input">
                                <label for="lastname">Last name</label>
                                <input id="lastname" name="lastname" type="text" required>
                            </div>
                        </div>
                        <div class="form-input">
                            <label for="region">Region</label>
                            <select id="region" name="region">
                                <option value="Kanto">Kanto</option>
                                <option value="Johto">Johto</option>
                                <option value="Hoenn">Hoenn</option>
                                <option value="Sinnoh">Sinnoh</option>
                                <option value="Unova">Unova</option>
                                <option value="Kalos">Kalos</option>
                                <option value="Alola">Alola</option>
                                <option value="Galar">Galar</option>
                            </select>
                        </div>
                        <div class="form-input">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" required>
                        </div>
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
                                if (isset($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['region'], $_POST['username'], $_POST['password'])) {
                                    $firstname = $_POST['firstname'];
                                    $lastname = $_POST['lastname'];
                                    $email = $_POST['email'];
                                    $region = $_POST['region'];
                                    $username = $_POST['username'];
                                    $password = $_POST['password'];
                            
                                    if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($region) && !empty($username) && !empty($password)) {
                                        $err = null;
                                        $userAccounts = $db->select("tblUserAccount");
                                        foreach ($userAccounts as $user) {
                                            if ($user['emailAdd'] == $email) {
                                                $err = "Someone already has that email, Brah!";
                                                break;
                                            }
                                            if ($user['username'] == $username) {
                                                $err = "Someone already has that username, Brah!";
                                                break;
                                            }
                                        }
                            
                                        if (!$err) {
                                            if ($db->insert('tblUser', [
                                                'firstname' => $firstname,
                                                'lastname' => $lastname,
                                                'email' => $email,
                                                'region' => $region,
                                                'username' => $username
                                            ])) {
                                                $userID = (($db->query("SELECT userID FROM tblUser ORDER BY userID DESC LIMIT 1;"))->fetch_assoc())['userID'];
                                                if ($db->insert('tblTrainerAccount', [
                                                    'firstname' => $firstname,
                                                    'lastname' => $lastname,
                                                    'birthdate' => '2004-06-10',
                                                    'region' => $region,
                                                    'team' => 'Mystic',
                                                    'type' => 'Normal'
                                                ])) {
                                                    $trainerID = (($db->query("SELECT trainerAccountID FROM tblTrainerAccount ORDER BY trainerAccountID DESC LIMIT 1;"))->fetch_assoc())['trainerAccountID'];
                                                    if ($db->insert('tblUserAccount', [
                                                        'emailAdd' => $email,
                                                        'username' => $username,
                                                        'password' => $password,
                                                        'userID' => $userID,
                                                        'trainerID' => $trainerID
                                                    ])) {
                                                        header('Location: login.php');
                                                        die();
                                                    }
                                                }
                                            }
                                        } else {
                                            echo "<div class='err-msg'>" . $err . "</div>";
                                        }
                                    }
                                }
                            }
                        ?>

                        <input class="submit-button" id="register-button" name="" type="submit" value="Register">
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