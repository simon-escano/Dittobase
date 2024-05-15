<?php
require 'php/connect.php';
global $currentUser;
global $db;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DittoBase | Admin</title>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/shared.css">

    <style>
        main {
            flex-direction: column;
        }
        form {
            display: flex;
            flex-direction: row;

            > input {
                overflow: initial;
                text-overflow: initial;
                max-width: initial;
            }
        }
    </style>
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

                if ($currentUser == 45) {
                    echo '<button id="admin" class="nav-button">ADMIN <span>▶</span></button>';
                }
            ?>
            <button id="home" class="nav-button">HOME <span>▶</span></button>
            <button id="about-us" class="nav-button">ABOUT US <span>▶</span></button>
            <button id="contact-us" class="nav-button">CONTACT US <span>▶</span></button>
        </nav>

        <main>
            <form action="" method="post">
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
                <input class="submit-button" id="register-button" name="" type="submit" value="Display Trainers">
            </form>
            <?php
            if (isset($_POST['region'])) {
                echo part('h', card('icon_pokeball.png', "Trainers from " . $_POST['region'], function() use($db) {
                    $html = '';
                    $kantoTrainers = $db->select('tblUser', 'username', 'region="' . $_POST['region'] . '"');
                    //SELECT tblUser username where region = '[user input]'
                    foreach ($kantoTrainers as $kantoTrainer) {
                        $html .= '<p>' . $kantoTrainer['username'] . '</p>';
                    }
                    return $html;
                }));
            }
            ?>
            <form action="" method="post">
                <div class="form-input">
                    <label for="type">Pokemon Type</label>
                    <select id="type" name="type">
                        <option value="Normal">Normal</option>
                        <option value="Fire">Fire</option>
                        <option value="Water">Water</option>
                        <option value="Electric">Electric</option>
                        <option value="Grass">Grass</option>
                        <option value="Ice">Ice</option>
                        <option value="Fighting">Fighting</option>
                        <option value="Poison">Poison</option>
                        <option value="Ground">Ground</option>
                        <option value="Flying">Flying</option>
                        <option value="Psychic">Psychic</option>
                        <option value="Bug">Bug</option>
                        <option value="Rock">Rock</option>
                        <option value="Ghost">Ghost</option>
                        <option value="Dragon">Dragon</option>
                        <option value="Dark">Dark</option>
                        <option value="Steel">Steel</option>
                        <option value="Fairy">Fairy</option>
                    </select>
                </div>
                <input class="submit-button" id="register-button" name="" type="submit" value="Display Trainer Types">
            </form>
            <?php
            if (isset($_POST['type'])) {
                echo part('h', card('icon_pokeball.png', $_POST['type'] . "-type Trainers", 
                    function() use($db) {
                        $html = '';
                        $trainerTypeInput = $_POST['type'];
                        $trainers = getPokemonTypes($db);
                        print_r($trainers);

                        foreach ($trainers as $trainer) {
                            if($trainer['type11'] == $trainerTypeInput || $trainer['type21'] == $trainerTypeInput || $trainer['type12'] == $trainerTypeInput ||
                                $trainer['type22'] == $trainerTypeInput || $trainer['type13'] == $trainerTypeInput || $trainer['type23'] == $trainerTypeInput ||
                                $trainer['type14'] == $trainerTypeInput || $trainer['type24'] == $trainerTypeInput || $trainer['type15'] == $trainerTypeInput ||
                                $trainer['type25'] == $trainerTypeInput || $trainer['type16'] == $trainerTypeInput || $trainer['type26'] == $trainerTypeInput ||
                                $trainer['type17'] == $trainerTypeInput || $trainer['type27'] == $trainerTypeInput || $trainer['type18'] == $trainerTypeInput ||
                                $trainer['type28'] == $trainerTypeInput || $trainer['type19'] == $trainerTypeInput || $trainer['type29'] == $trainerTypeInput ||
                                $trainer['type110'] == $trainerTypeInput || $trainer['type210'] == $trainerTypeInput)
                            $html .= '<p>' . $trainer['trainerName'] . '</p>';
                        }
                        return $html;
                    }
                ));
            }
            ?>
            <form action="" method="post">
                <input type="hidden" name="check-arena-status" value="check-arena-status">
                <input class="submit-button" id="check-arena-status-button" name="check-arena-status-button" type="submit" value="Check Arena Status">
            </form>
            <?php
            if (isset($_POST['check-arena-status'])) {
                echo card('icon_pokeball.png', 'Arena Statuses', function() use($db) {
                    $html = '';
                    $arenas = $db->select('tblBattle GROUP BY arenaID ', 'arenaID, count(battleID) AS numOfBattles');
                    $max = 0;
                    foreach ($arenas as $arena) {
                        if($arena['numOfBattles'] > $max){
                            $max = $arena['numOfBattles'];
                        }
                    }
                    foreach ($arenas as $arena) {
                        if ($arenaName = $db->select('tblArena', 'name', "arenaID='" . $arena['arenaID'] . "'")) {
                            $arenaName = $arenaName['0'];
                            if($max == $arena['numOfBattles']){
                                $html .= card('icon_pokeball.png', $arenaName['name']. '<span> [Most Popular Arena!] </span>' , $arena['numOfBattles']. " battles");
                            }else{
                                $html .= card('icon_pokeball.png', $arenaName['name'], $arena['numOfBattles']. " battles");
                            }
                        }
                    }
                    return $html;
                    //<span>'. $arena['badge'] . '!</span>
                });
            }
            ?>
        </main>
        
        <footer>
            <p>Simon Escaño and Malt Solon</p>
            <p>BSCS-2</p>
        </footer>
    </section>

    <script src="js/shared.js"></script>
</body>
</html>