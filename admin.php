<?php
require 'php/connect.php';
require "php/html_generator.php";
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
</head>
<body>
<?php
echo section("#container",
    head("Dittobase"),
    section("#banner",
        img("img/banner_home.png", "#banner-img")
    ),
    nav(
        function() {
            global $currentUser;
            $html = new HTML();
            if (!$currentUser) {
                $html->add(
                    button(null, "#register", ".nav-button", "REGISTER", span("▶")),
                    button(null, "#login", ".nav-button", "LOGIN", span("▶")),
                );
            } else {
                $html->add(
                    button(null, "#login", ".nav-button", "LOGOUT", span("▶"))
                );
            }
            if ($currentUser == 45) {
                $html->add(
                    button(null, "#admin", ".nav-button", "ADMIN", span("▶"))
                );
            }
            return $html;
        },
        button(null, "#home", ".nav-button", "HOME", span("▶")),
        button(null, "#about-us", ".nav-button", "ABOUT US", span("▶")),
        button(null, "#contact-us", ".nav-button", "CONTACT US", span("▶"))
    ),
    main(
        vbox(
            card("icon_pokeball.png", "Region Report",
                form(null, ".flex-row",
                    div(".form-input",
                        label("region", "Region"),
                        select("region", "Kanto", "Johto", "Hoenn", "Sinnoh", "Unova", "Kalos", "Alola", "Galar")
                    ),
                    button(null, ".report-button", "GO!")
                ),
                function() {
                    if (isset($_POST['region'])) {
                        $html = new HTML();
                        $html->add(
                            card("icon_pokeball.png", "Trainers from " . $_POST['region'],
                                function() {
                                    global $db;
                                    $html = new HTML();
                                    $region = $_POST['region'];
                                    $columns = 't.trainerAccountID, t.firstname';
                                    $trainers = $db->select(
                                        'tblTrainerAccount t',
                                        $columns,
                                        "t.region='$region'",
                                        [
                                            ['tblTrainerPokemon tp', 't.trainerAccountID = tp.trainerAccountID'],
                                        ]
                                    );

                                    foreach ($trainers as $trainer) {
                                        $trainerHTML = new HTML();

                                        $trainerPokemon = $db->select_one(
                                            'tblTrainerPokemon',
                                            '*',
                                            "trainerAccountID='" . $trainer['trainerAccountID'] . "'"
                                        );

                                        for ($i = 1; $i <= 10; $i++) {
                                            $pokemonID = $trainerPokemon['pokemon' . $i];
                                            if ($pokemonID) {
                                                $pokemon = $db->select_one(
                                                    'tblPokemon p',
                                                    'p.pokedexID, pd.name',
                                                    "p.spawnID='$pokemonID'",
                                                    [['tblPokedex pd', 'p.pokedexID = pd.pokedexID']]
                                                );
                                                $trainerHTML->add(p($pokemon['name'], '.pink-text'));
                                            }
                                        }

                                        $html->add(card("icon_pokeball.png", $trainer['firstname'] . "'s Pokemon", $trainerHTML->toString()));
                                    }

                                    return $html;
                                }
                            )
                        );
                        return $html;
                    }
                }
            ),
            card("icon_pokeball.png", "Pokemon Report",
                form(null, ".flex-row",
                    div(".form-input",
                        label("type", "Pokemon Type"),
                        select("type", "Normal", "Fire", "Water", "Electric", "Grass", "Ice", "Fighting", "Poison", "Ground", "Flying", "Psychic", "Bug", "Rock", "Ghost", "Dragon", "Dark", "Steel", "Fairy")
                    ),
                    button(null, ".report-button", "GO!")
                ),
                function() {
                    if (isset($_POST['type'])) {
                        $html = new HTML();
                        $html->add(
                            card("icon_pokeball.png", "Trainers with " . $_POST['type'] . "-type Pokemon",
                                function() {
                                    global $db;
                                    $html = new HTML();
                                    $inputType = $_POST['type'];
                                    $trainers = getPokemonTypes($db);

                                    foreach ($trainers as $trainer) {
                                        $trainerHTML = new HTML();
                                        for ($i = 1; $i <= 10; $i++) {
                                            if ($trainer['type1' . $i] == $inputType) {
                                                $trainerHTML->add(p($trainer['name' . $i]));
                                            }
                                            if ($trainer['type2' . $i] == $inputType) {
                                                $trainerHTML->add(p($trainer['name' . $i]));
                                            }
                                        }
                                        
                                        if (!empty($trainerHTML->toString())) {
                                            $html->add(
                                                card("icon_pokeball.png", $trainer['trainerName'], $trainerHTML)
                                            );
                                        }
                                    }

                                    return $html;
                                }
                            )
                        );
                        return $html;
                    }
                }
            )
        ),
        card("icon_pokeball.png", "Arena Report",
            form(null, ".flex-row",
                div(".form-input",
                    label("arena", "Arena"),
                    select("arena", "Pewter City Gym", "Violet City Gym", "Rustboro City Gym", "Oreburgh City Gym", "Striaton City Gym", "Santalune City Gym", "Hau'oli City Gym", "Turffield Stadium")
                ),
                button(null, ".report-button", "GO!")
            ),
            function() {
                if (isset($_POST['arena'])) {
                    $html = new HTML();
                    $html->add(
                        card("icon_pokeball.png", "Battles in " . $_POST['arena'],
                            function() {
                                global $db;
                                $html = new HTML();
                                $arena = $db->select("tblArena", "arenaID", "name='". $_POST['arena'] ."'");
                                if ($arena) {
                                    $arena = $arena[0]['arenaID'];
                                } else {
                                    $arena = null;
                                }
                                $joins = [
                                    ['tblTrainerAccount t1', 'b.firstOpponent = t1.trainerAccountID'],
                                    ['tblTrainerAccount t2', 'b.secondOpponent = t2.trainerAccountID']
                                ];
                                $battles = $db->select('tblBattle b', 'b.*, t1.firstname AS firstOpponentName, t2.firstname AS secondOpponentName', "arenaID='$arena'", $joins);
                                foreach ($battles as $battle) {
                                    $html->add(
                                        div('.battle',
                                            div(".battle-header",
                                                ($battle['isFirstOpponentWinner']) ? 
                                                p(".battle-winner", "WINNER") . p(".battle-loser", "LOSER") : 
                                                p(".battle-loser", "LOSER") . p(".battle-winner", "WINNER")
                                            ),
                                            div(".battle-body",
                                                p($battle['firstOpponentName']),
                                                p("VS."),
                                                p($battle['secondOpponentName'])
                                            ),
                                            p(".battle-date", $battle['battleDate'])
                                        )
                                    );
                                }
                                return $html;
                            }
                        )
                    );
                    return $html;
                }
            }
        ),
    ),
    footer(
        p("Simon Escaño and Malt Solon"),
        p("BSCS-2")
    )
)
?>
    <script src="js/shared.js"></script>
</body>
</html>