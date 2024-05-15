<?php
require 'php/connect.php';

$pokedex = $db->select("tblPokedex", "name, type1, type2, move1, move2, move3, description, image");

if (!isset($_SESSION['pokedexIndex'])) {
    $_SESSION['pokedexIndex'] = 0;
}

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
                if ($currentUser == 45) {
                    echo '<button id="admin" class="nav-button">ADMIN <span>▶</span></button>';
                }
            ?>
            <button id="home" class="nav-button">HOME <span>▶</span></button>
            <button id="about-us" class="nav-button">ABOUT US <span>▶</span></button>
            <button id="contact-us" class="nav-button">CONTACT US <span>▶</span></button>
        </nav>

        <main>
            <?php
                echo 
                part('v',
                    ($currentUser) ? part('v',
                        part('h',
                            part('v',
                                card("icon_pokeball.png", "HELLO, " . $db->select("tblTrainerAccount", "firstname", "trainerAccountID='". $currentUser ."'")[0]["firstname"] . "!",
                                    "Welcome to Dittobase!",
                                    "#welcome-card",
                                ),
                                card("icon_pokeball.png", "YOUR POKEMON TEAM",
                                    div("pokemons", "#your-pokemon-team",
                                        function() use($db, $currentUser) {
                                            $html = '';
                                            $joins = [
                                                ['tblPokemon p', 'p.spawnID = t.chosenPokemon1 OR p.spawnID = t.chosenPokemon2 OR p.spawnID = t.chosenPokemon3 OR p.spawnID = t.chosenPokemon4 OR p.spawnID = t.chosenPokemon5 OR p.spawnID = t.chosenPokemon6'],
                                                ['tblPokedex d', 'd.pokedexID = p.pokedexID']
                                            ];
                                        
                                            $columns = 'd.name AS name, d.image AS image, spawnID';
                                            $where = 't.trainerAccountID = "' . $currentUser . '"';
                                            $pokemonData = $db->select('tblPokemonTeam t', $columns, $where, $joins);
                                        
                                            foreach ($pokemonData as $data) {
                                                $html .=
                                                div("pokemon",
                                                    p("pokemon-name",
                                                        $data['name']
                                                    ),
                                                    div("pokemon-content",
                                                        img($data['image'], "pokemon-image")
                                                    ),
                                                    button("remove_from_team", "remove-from-team-button", "REMOVE FROM TEAM", ["spawnID" => $data['spawnID']])
                                                );
                                            }
                                        
                                            return $html;
                                        }
                                    )
                                                                    
                                )
                            ),
                            card("icon_pokeball.png", "YOUR POKEMON",
                                div("pokemons", "#your-pokemon",
                                    function() use($db, $currentUser) {
                                        $html = '';
                                        $trainerPokemon = $db->select('tblTrainerPokemon', '*', "trainerAccountID='" . $currentUser . "'");
                                        if ($trainerPokemon) {
                                            $trainerPokemon = $trainerPokemon[0];

                                            $joins = [
                                                ['tblPokemon p', 'p.spawnID = t.pokemon1 OR p.spawnID = t.pokemon2 OR p.spawnID = t.pokemon3 OR p.spawnID = t.pokemon4 OR p.spawnID = t.pokemon5 OR p.spawnID = t.pokemon6 OR p.spawnID = t.pokemon7 OR p.spawnID = t.pokemon8 OR p.spawnID = t.pokemon9 OR p.spawnID = t.pokemon10'],
                                                ['tblPokedex d', 'd.pokedexID = p.pokedexID']
                                            ];

                                            $columns = 'p.spawnID AS spawnID, d.name AS name, d.image AS image';
                                            $where = 't.trainerAccountID = "' . $currentUser . '"';
                                            $pokemonData = $db->select('tblTrainerPokemon t', $columns, $where, $joins);

                                            $i = 1;
                                            foreach ($pokemonData as $data) {
                                                $html .=
                                                div("pokemon",
                                                    p("pokemon-name",
                                                        $data['name']
                                                    ),
                                                    div("pokemon-content",
                                                        img($data['image'], "pokemon-image")
                                                    ),
                                                    div("pokemon-buttons",
                                                        button("add_to_team", "add-to-team-button", "ADD TO TEAM",
                                                            ["spawnID" => $data['spawnID']],
                                                        ),
                                                        button("free_pokemon", "free-button", "RELEASE",
                                                            ["spawnID" => $data['spawnID']],
                                                        ),
                                                    )
                                                );
                                                $i++;
                                            }
                                        }
                                        return $html;
                                    }
                                )
                            )
                        ),
                        card("icon_pokeball.png", "DASHBOARD",
                            function() use($db, $currentUser) {
                                $data = $db->select("tblBattle", "COUNT(battleID) AS totalBattles, 
                                SUM(CASE WHEN (isFirstOpponentWinner=1 AND firstOpponent='$currentUser') OR 
                                (isFirstOpponentWinner=0 AND secondOpponent='$currentUser') THEN 1 ELSE 0 END) AS totalWins", 
                                "firstOpponent='$currentUser' OR secondOpponent='$currentUser'")[0];

                                $numOfBattles = $data["totalBattles"];
                                $numOfWins = $data["totalWins"];

                                return
                                pieChart(['Total battles' => $numOfBattles],  ['Wins' => $numOfWins], ['Losses' => $numOfBattles - $numOfWins]);
                            },  
                            "#dashboard",
                            div("bar-chart flex-1",
                                div("bar-chart-header",
                                    p("bar-chart-title", "Your pokemon types")
                                ),
                                function() use($db, $currentUser) {
                                    $types = getPokemonTypes($db, "trainerAccountID=$currentUser");
                                }
                            )
                        )
                    ) : "",
                    part('v',
                        card("icon_pokeball.png", "ARENAS",
                            div("arenas",
                                function() use($db) {
                                    $html = "";
                                    $arenas = $db->select('tblArena', 'name, region, badge');
                                    foreach ($arenas as $arena) {
                                        $html .=
                                        div("arena",
                                            p("arena-name", $arena['name']),
                                            p("arena-location", "Location: ", $arena['region']),
                                            div("arena-description",
                                                p("arena-description-text", "Fight in ", $arena['name'], " to get <span>", $arena['badge'], "!</span>")
                                            )
                                        );
                                    }
                                    return $html;
                                }
                            )
                        ),
                        part("h",
                            div("pokedex", "#pokedex",
                                div("pokedex-top",
                                    div("pokedex-top-left",
                                        div("pokedex-lens",
                                            div("pokedex-lens-glass")
                                        ),
                                        div("pokedex-top-button red"),
                                        div("pokedex-top-button yellow"),
                                        div("pokedex-top-button green")
                                    ),
                                    div("pokedex-top-right",
                                        div("pokedex-top-right-box",
                                            p("", "POKEDEX")
                                        )
                                    )
                                ),
                                div("pokedex-bottom",
                                    div("pokedex-screen",
                                        div("pokedex-screen-content",
                                            div("pokedex-result",
                                                div("pokedex-header", 
                                                    p("pokedex-name", $pokedex[$_SESSION['pokedexIndex']]['name']),
                                                    part("h non-flex",
                                                        p("pokedex-type", $pokedex[$_SESSION['pokedexIndex']]['type1']),
                                                        ($pokedex[$_SESSION['pokedexIndex']]['type2'] != "None") ? p("pokedex-type", $pokedex[$_SESSION['pokedexIndex']]['type2']) : ""
                                                    )
                                                ),
                                                div("pokedex-body",
                                                    img($pokedex[$_SESSION['pokedexIndex']]['image'], "pokedex-image"),
                                                    p("pokedex-description", $pokedex[$_SESSION['pokedexIndex']]['description'])
                                                ),
                                                div("pokedex-moves",
                                                    $pokedex[$_SESSION['pokedexIndex']]['name'], "'s moves",
                                                    part("h",
                                                        p("pokedex-type", $pokedex[$_SESSION['pokedexIndex']]['move1']),
                                                        p("pokedex-type", $pokedex[$_SESSION['pokedexIndex']]['move2']),
                                                        p("pokedex-type", $pokedex[$_SESSION['pokedexIndex']]['move3']),
                                                    )
                                                )
                                            )
                                        ),
                                        div("pokedex-screen-buttons",
                                            div("pokedex-screen-button"),
                                            div("pokedex-speaker",
                                                "<hr>", "<hr>", "<hr>", "<hr>",
                                            )
                                        )
                                    ),
                                    div("pokedex-buttons",
                                        button("decrement_pokedex", "pokedex-button", "<"),
                                        div("pokedex-button-group",
                                            div("pokedex-line-buttons",
                                                div("pokedex-line-button red"),
                                                div("pokedex-line-button blue"),
                                            ),
                                            div("pokedex-trackpad")
                                        ),
                                        button("increment_pokedex", "pokedex-button", ">", ["limit" => count($pokedex) - 1])
                                    )
                                )
                            ),
                            card("icon_pokeball.png", "BATTLES",
                                div("battles",
                                    function() use($db) {
                                        $html = '';
                                        $joins = [
                                            ['tblTrainerAccount t1', 'b.firstOpponent = t1.trainerAccountID'],
                                            ['tblTrainerAccount t2', 'b.secondOpponent = t2.trainerAccountID']
                                        ];
                                        $battles = $db->select('tblBattle b', 'b.*, t1.firstname AS firstOpponentName, t2.firstname AS secondOpponentName', '', $joins);
                                        foreach ($battles as $battle) {
                                            $html .=
                                            div("battle",
                                                div("battle-header",
                                                    ($battle['isFirstOpponentWinner']) ? 
                                                    p("battle-winner", "WINNER") . p("battle-loser", "LOSER") : 
                                                    p("battle-loser", "LOSER") . p("battle-winner", "WINNER")
                                                ),
                                                div("battle-body",
                                                    p("", $battle['firstOpponentName']),
                                                    p("vs", "VS."),
                                                    p("", $battle['secondOpponentName'])
                                                ),
                                                p("battle-date", $battle['battleDate'])
                                            );
                                        }
                                        return $html;
                                    }
                                )
                            )
                        )
                    )
                )
            ?>
        </main>
        
        <footer>
            <p>Simon Escaño and Malt Solon</p>
            <p>BSCS-2</p>
        </footer>
    </section>

    <script src="js/shared.js">

    </script>
</body>
</html>