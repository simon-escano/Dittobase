<?php
require 'php/connect.php';

$pokedex = $db->select('tblPokedex', '*');
$pokemon = $db->select('tblPokemon', '*');
$battles = $db->select('tblBattle', '*');
$trainers = $db->select('tblTrainerAccount', '*');
$arenas = $db->select('tblArena', '*');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DittoBase | Home</title>
    
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
            <img id="banner-img" src="img/banner_home.png">
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
                <div class="partition-v">
                    <?php
                        if ($currentUser) {
                            foreach ($trainers as $trainer) {
                                if ($trainer['trainerAccountID'] == $currentUser) {
                                    new Card("icon_pokeball.png", "Hello, " . $trainer['firstname'] . "!", "Welcome to DittoBase!");
                                }
                            }
                        }
                    ?>
                    <div class="card">
                        <section class="card-header">
                            <div class="card-img-box">
                                <img src="img/icon_pokeball.png">
                            </div>
                            <p class="card-title">ARENAS</p>
                        </section>
                        <section id="pokedex" class="card-content">
                        <?php
                            foreach ($arenas as $arena) {
                                new Card("icon_pokeball.png", $arena['name'], '
                                <p class="dex-moves"> Located in '. $arena['region'] .'</p>
                                <p class="arena-badge"> Fight in '. $arena['name'] .' to get the <span>'. $arena['badge'] . '!</span></p>
                                ');
                            }
                        ?>
                        </section>
                    </div>
                </div>
                <div class="partition-h">
                    <div class="card">
                        <section class="card-header">
                            <div class="card-img-box">
                                <img src="img/icon_pokeball.png">
                            </div>
                            <p class="card-title">POKEDEX</p>
                        </section>
                        <section id="pokedex" class="card-content">
                        <?php
                        foreach ($pokedex as $dex) {
                            $dex['move1'] .= ", " . $dex['move2'] .= ", " . $dex['move3'];
                            if ($dex['type2'] != 'None') {
                                $dex['type1'] .= ", " . $dex['type2'];
                            }
                            new Card("icon_pokeball.png", $dex['name'], '
                                <p class="dex-types">Types: '. $dex['type1'] . '</p>
                                <p class="dex-moves">Moves: '. $dex['move1'] .'</p>
                                <p class="dex-desc">'. $dex['description'] .'</p>
                            ');
                        }
                        ?>
                        </section>
                    </div>
                    <div class="card">
                        <section class="card-header">
                            <div class="card-img-box">
                                <img src="img/icon_pokeball.png">
                            </div>
                            <p class="card-title">BATTLES</p>
                        </section>
                        <section id="pokedex" class="card-content">
                        <?php
                            foreach ($battles as $battle) {
                                $opp1Name;
                                $opp2Name;
                                foreach ($trainers as $trainer) {
                                    if ($battle['firstOpponent'] == $trainer['trainerAccountID']) {
                                        $opp1Name = $trainer['firstname'];
                                    }
                                    if ($battle['secondOpponent'] == $trainer['trainerAccountID']) {
                                        $opp2Name = $trainer['firstname'];
                                    }
                                }
                                $winner = ($battle['isFirstOpponentWinner']) ? $opp1Name : $opp2Name;
                                new Card("icon_pokeball.png", $opp1Name . " vs. " . $opp2Name, '
                                    <p class="dex-moves"> Battle date: '. $battle['battleDate'] .'</p>
                                    <p class="battle-winner">'. $winner . ' won this battle! </p>
                                ');
                            }
                        ?>
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