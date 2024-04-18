<?php
require 'php/connect.php';
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
                    <div class="partition-h">
                    <?php
                        if ($currentUser) {
                            $trainers = $db->select('tblTrainerAccount', '*', "trainerAccountID='". $currentUser ."'");
                            echo part('v', 
                                card("icon_pokeball.png", "Hello, " . $trainers['0']['firstname'] . "!", "Welcome to Dittobase!") .
                                card("icon_pokeball.png", "Your Pokemon Team", function() use($db, $currentUser) {
                                    $html = '';
                                    $pokemonTeam = $db->select("tblPokemonTeam", "*", "trainerAccountID='". $currentUser ."'")['0'];
                                    for ($i = 1; $i <= 6; $i++) {
                                        $chosenPokemon = $pokemonTeam['chosenPokemon' . $i];
                                        if (!$chosenPokemon) continue;
                                        $chosenPokemon = $db->select("tblPokedex", "*", "pokedexID='". $chosenPokemon ."'")['0'];
                                        $html .= '<div class="card-pokemon"><p>'. $chosenPokemon['name'] .'</p></div>';
                                    }
                                    return $html;
                                })
                            );
                            
                            echo card("icon_pokeball.png", "Your Pokemon", function() use($db, $currentUser) {
                                $trainerPokemon = $db->select('tblTrainerPokemon', '*', "trainerAccountID='" . $currentUser . "'")['0'];
                                $html = '';
                                for ($i = 1; $i <= 10; $i++) {
                                    $currentPokemon = $trainerPokemon['pokemon' . $i];
                                    if (!$currentPokemon) continue;

                                    $pokemon = $db->select('tblPokemon', '*', "spawnID='" . $currentPokemon . "'")['0'];
                                    $dex = $db->select('tblPokedex', '*', "pokedexID='" . $pokemon['pokedexID'] . "'")['0'];

                                    $html .= 
                                        '<div class="card-pokemon">' . 
                                        '<p>' . $dex['name'] . '</p>' .
                                        '<form method="post" action="add_to_team.php">' .
                                        '<input type="hidden" name="pokemonID" value="' . $pokemon['pokedexID'] . '">' .
                                        '<input type="hidden" name="trainerID" value="' . $currentUser . '">' .
                                        '<input id="add-to-team-button" type="submit" name="add_to_team" value="ADD TO TEAM">' .
                                        '</form>' . 
                                        '<form method="post" action="free_pokemon.php">' . // Change action to the PHP script that handles the free action
                                        '<input type="hidden" name="trainerID" value="' . $currentUser . '">' . // Include a hidden input to pass the Pokemon ID
                                        '<input type="hidden" name="pokemonPos" value="pokemon' . $i . '">' . // Include a hidden input to pass the Pokemon ID
                                        '<input type="hidden" name="pokemonID" value="' . $pokemon['pokedexID'] . '">' . // Include a hidden input to pass the Pokemon ID
                                        '<input id="free-button" type="submit" name="free_pokemon" value="FREE">' .  // Name the submit button for handling in PHP
                                        '</form>' .
                                        '</div>';
                                }
                                return $html;
                            });
                        }
                    ?>
                    </div>
                    <div class="card">
                        <section class="card-header">
                            <div class="card-img-box">
                                <img src="img/icon_pokeball.png">
                            </div>
                            <p class="card-title">ARENAS</p>
                        </section>
                        <section id="pokedex" class="card-content">
                        <?php
                            $arenas = $db->select('tblArena', '*');
                            foreach ($arenas as $arena) {
                                echo card("icon_pokeball.png", $arena['name'], '
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
                        $pokedex = $db->select("tblPokedex", "*");
                        foreach ($pokedex as $dex) {
                            $dex['move1'] .= ", " . $dex['move2'] .= ", " . $dex['move3'];
                            if ($dex['type2'] != 'None') {
                                $dex['type1'] .= ", " . $dex['type2'];
                            }
                            echo card("icon_pokeball.png", $dex['name'], '
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
                            $battles = $db->select('tblBattle', '*');
                            foreach ($battles as $battle) {
                                $opp1Name = $db->select('tblTrainerAccount', '*', "trainerAccountID='". $battle['firstOpponent'] ."'")['0']['firstname'];
                                $opp2Name = $db->select('tblTrainerAccount', '*', "trainerAccountID='". $battle['secondOpponent'] ."'")['0']['firstname'];
                                $winner = ($battle['isFirstOpponentWinner']) ? $opp1Name : $opp2Name;
                                echo card("icon_pokeball.png", $opp1Name . " vs. " . $opp2Name, '
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