<?php
require 'php/connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DittoBase | About Us</title>
    
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
            <img id="banner-img" src="img/banner_contact-us.png">
        </section>
    
        <nav>
            <?php
                if (!$currentUser) {
                    echo '<button id="register" class="nav-button">REGISTER <span>▶</span></button>';
                    echo '<button id="login" class="nav-button">LOGIN <span>▶</span></button>';
                } else {
                    echo '<button id="login" class="nav-button">LOGOUT <span>▶</span></button>';
                }
            ?>
            <button id="home" class="nav-button">HOME <span>▶</span></button>
            <button id="about-us" class="nav-button">ABOUT US <span>▶</span></button>
            <button id="contact-us" class="nav-button">CONTACT US <span>▶</span></button>
        </nav>

        <main>
            <div class="partition-v">
                <div class="card">
                    <section class="card-header">
                        <div class="card-img-box">
                            <img src="img/icon_mail.png">
                        </div>
                        <p class="card-title">CONTACT US</p>
                    </section>
                    <section class="card-content">
                        <p class="card-txt email-link">
                            realdittobase@gmail.com
                        </p>
                    </section>
                </div>

                <div class="partition-h">
                    <div class="card">
                        <section class="card-header">
                            <div class="card-img-box">
                                <img src="img/icon_gengar.png">
                            </div>
                            <p class="card-title">MALT SOLON</p>
                        </section>
                        <section class="card-content">
                            <p class="card-txt">
                                Malt's favorite Pokemon is Gengar because he thinks they have the same vibes. He also thinks they look alike.
                            </p>
                            <p class="card-txt email-link">
                                moltsolon@gmail.com
                            </p>
                        </section>
                    </div>
                    <div class="card">
                        <section class="card-header">
                            <div class="card-img-box">
                                <img src="img/icon_pikachu.png">
                            </div>
                            <p class="card-title">SIMON ESCAÑO</p>
                        </section>
                        <section class="card-content">
                            <p class="card-txt">
                                Simon likes Pokemon. His favorite pokemon is Pikachu because they are cute and the "most powerful" pokemon.
                            </p>
                            <p class="email-link">
                                escanosimonlyster@gmail.com
                            </p>
                        </section>
                    </div>
                </div>
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