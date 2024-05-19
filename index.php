<?php
require 'php/connect.php';
require "php/html_generator.php";

$pokedex = $db->select("tblPokedex", "name, type1, type2, move1, move2, move3, description, image");

if (!isset($_SESSION['pokedexIndex'])) {
    $_SESSION['pokedexIndex'] = 0;
}

if ($currentUser) {
    $battleDetails = $db->select("tblBattle", "isFirstOpponentWinner, battleDate", "firstOpponent='$currentUser' OR secondOpponent='$currentUser' ORDER BY battleDate");
    $dataPoints = array();
    foreach ($battleDetails as $bd) {
        $dataPoints[] = array(
            "y" => $bd['isFirstOpponentWinner'],
            "label" => $bd['battleDate']
        );
    }
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
        $currentUser ?
        vbox(
            hbox(
                vbox(
                    card("icon_pokeball.png", "HELLO, " . $db->select_one("tblTrainerAccount", "firstname", "trainerAccountID='$currentUser'")['firstname'] . "!",
                        p(".card-txt", "Welcome to Dittobase!")
                    ),
                    card("icon_pokeball.png", "YOUR POKEMON TEAM",
                        div("#your-pokemon-team", ".pokemons",
                            function() {
                                global $db, $currentUser;
                                $html = new HTML();
                                $joins = [
                                    ['tblPokemon p', 'p.spawnID = t.chosenPokemon1 OR p.spawnID = t.chosenPokemon2 OR p.spawnID = t.chosenPokemon3 OR p.spawnID = t.chosenPokemon4 OR p.spawnID = t.chosenPokemon5 OR p.spawnID = t.chosenPokemon6'],
                                    ['tblPokedex d', 'd.pokedexID = p.pokedexID']
                                ];
                            
                                $columns = 'd.name AS name, d.image AS image, spawnID';
                                $where = 't.trainerAccountID = "' . $currentUser . '"';
                                $pokemonData = $db->select('tblPokemonTeam t', $columns, $where, $joins);
                            
                                foreach ($pokemonData as $data) {
                                    $html->add(
                                        div(".pokemon",
                                            p(".pokemon-name", $data["name"]),
                                            div(".pokemon-content", img($data['image'], ".pokemon-image")),
                                            button("remove_from_team", ".pokemon-button red", "REMOVE FROM TEAM", ["spawnID" => $data["spawnID"]])
                                        )
                                    );
                                }
                                return $html;
                            }
                        )
                    )
                ),
                card("icon_pokeball.png", "YOUR POKEMON [" . countPokemon($db, $currentUser) . "]",
                    div("#your-pokemon", ".pokemons",
                        function() {
                            global $db, $currentUser;
                            $html = new HTML();
                            $joins = [
                                ['tblPokemon p', 'p.spawnID = t.pokemon1 OR p.spawnID = t.pokemon2 OR p.spawnID = t.pokemon3 OR p.spawnID = t.pokemon4 OR p.spawnID = t.pokemon5 OR p.spawnID = t.pokemon6 OR p.spawnID = t.pokemon7 OR p.spawnID = t.pokemon8 OR p.spawnID = t.pokemon9 OR p.spawnID = t.pokemon10'],
                                ['tblPokedex d', 'd.pokedexID = p.pokedexID']
                            ];
                            $columns = 'p.spawnID AS spawnID, d.name AS name, d.image AS image';
                            $where = 't.trainerAccountID = "' . $currentUser . '"';
                            $pokemonData = $db->select('tblTrainerPokemon t', $columns, $where, $joins);
                            foreach ($pokemonData as $data) {
                                $html->add(
                                    div(".pokemon",
                                        p(".pokemon-name", $data["name"]),
                                        div(".pokemon-content", img($data["image"], ".pokemon-image")),
                                        div(".pokemon-buttons",
                                            button("add_to_team", ".pokemon-button green", "ADD TO TEAM", ["spawnID" => $data["spawnID"]]),
                                            button("release", ".pokemon-button red", "RELEASE", ["spawnID" => $data["spawnID"]])
                                        )
                                    )
                                );
                            }
                            return $html;
                        }
                    )
                )
            ),
            card("icon_pokeball.png", "DASHBOARD",
                hbox(
                    function() {
                        global $db, $currentUser;
                        $data = $db->select_one("tblBattle", "COUNT(battleID) AS totalBattles, 
                        SUM(CASE WHEN (isFirstOpponentWinner=1 AND firstOpponent='$currentUser') OR 
                        (isFirstOpponentWinner=0 AND secondOpponent='$currentUser') THEN 1 ELSE 0 END) AS totalWins", 
                        "firstOpponent='$currentUser' OR secondOpponent='$currentUser'");

                        $numOfBattles = $data["totalBattles"];
                        $numOfWins = $data["totalWins"];

                        return
                        pie_chart(['Total battles' => $numOfBattles],  ['Wins' => $numOfWins], ['Losses' => $numOfBattles - $numOfWins]);
                    },
                    div(".bar-chart flex-1",
                        div(".bar-chart-header",
                            p(".bar-chart-title", "YOUR POKEMON TYPES"),
                            div(".bar-chart-legends",
                                function() {
                                    $html = new HTML();
                                    $types = getTypes();
                                    foreach ($types as $key => $value) {
                                        $html->add(
                                            div(".bar-chart-legend",
                                                div(".bar-chart-legend-color", "^background-color: " . $value),
                                                p(".bar-chart-legend-title", $key)
                                            )
                                        );
                                    }
                                    return $html;
                                }
                            )
                        ),
                        div(".bar-chart-elems",
                            function() {
                                global $db, $currentUser;
                                $html = new HTML();
                                $types = getPokemonTypes($db, "TA.trainerAccountID=$currentUser");
                                $typeCount = typeCounter($types[0]);
                                $typeColors = getTypes();
                                $total = $typeCount["total"];

                                foreach ($typeCount as $type => $value) {
                                    if (!$value || $type == "total") continue;
                                    $html->add(
                                        div(".bar-chart-elem", "^height: " . percent($total, $value, true) . ";background-color: " . $typeColors[$type],
                                            p(".bar-chart-elem-amount", $value)
                                        )
                                    );
                                }
                                return $html;
                            }
                        )
                    )
                ),
                div("#line-chart", "^height: 140px; width: 100%")
            )
        ) : "",
        vbox(
            card("icon_pokeball.png", "ARENAS",
                div(".arenas",
                    function() {
                        global $db;
                        $html = new HTML();
                        $arenas = $db->select("tblArena", "name, region, badge");
                        foreach ($arenas as $arena) {
                            $html->add(
                                div(".arena",
                                    p(".arena-name", $arena['name']),
                                    p(".arena-location", "Location: ", $arena['region']),
                                    div(".arena-description",
                                        p(".arena-description-text", "Fight in ", $arena['name'], " to get the ", span($arena['badge'], "!"))
                                    )
                                )
                            );
                        }
                        return $html;
                    }
                )
            ),
            hbox(
                generatePokedex($pokedex,
                    function() {
                        global $db, $pokedex;
                        $html = new HTML();
                        if (isset($_POST['search'])) {
                            $search = $_POST['search'];
                            $searched = "";
                            switch ($_POST['query']) {
                                case "name":
                                    $where = "LOWER(name) LIKE LOWER('%$search%')";
                                    $searched = $db->select("tblPokedex", "*", $where);
                                    break;
                                case "types":
                                    $where = "LOWER(type1) LIKE LOWER('%$search%') OR LOWER(type2) LIKE LOWER('%$search%')";
                                    $searched = $db->select("tblPokedex", "*", $where);
                                    break;
                                case "moves":
                                    $where = "LOWER(move1) LIKE LOWER('%$search%') OR LOWER(move2) LIKE LOWER('%$search%') OR LOWER(move3) LIKE LOWER('%$search%')";
                                    $searched = $db->select("tblPokedex", "*", $where);
                                    break;
                            }                                      
                            if (!$searched) {
                                return "No results for " . $search . " by " . $_POST['query'];
                            }
                            foreach ($searched as $res) {
                                $html->add(generatePokedexResult($res));
                            }
                            return $html;
                        } else {
                            return generatePokedexResult($pokedex[$_SESSION['pokedexIndex']]);
                        }
                    }
                ),
                card("icon_pokeball.png", "BATTLES",
                    div(".battles",
                        function() {
                            global $db;
                            $html = new HTML();
                            $joins = [
                                ['tblTrainerAccount t1', 'b.firstOpponent = t1.trainerAccountID'],
                                ['tblTrainerAccount t2', 'b.secondOpponent = t2.trainerAccountID']
                            ];
                            $battles = $db->select('tblBattle b', 'b.*, t1.firstname AS firstOpponentName, t2.firstname AS secondOpponentName', '', $joins);
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
                )
            )
        )
    ),
    footer(
        p("Simon Escaño and Malt Solon"),
        p("BSCS-2")
    )
)
?>
    <script src="js/shared.js"></script>
    <script>
        window.onload = function () {
        var chart = new CanvasJS.Chart("line-chart", {
            title: {
                text: "Win Trajectory",
                fontFamily: "Pixelify Sans",
                fontWeight: "bold",
                fontColor: "#39135c",
                fontSize: 24
            },
            axisY: {
                fontFamily: "Pixelify Sans",
                labelFontFamily: "Pixelify Sans",
                labelFontColor: "#39135c",
                fontColor: "#39135c",
                fontWeight: "bold",
                labelFontSize: 12,
                interval: 1,
                minimum: 0,
                maximum: 1,
                labelFormatter: function (e) {
                    return (e.value === 1) ? "Win" : "Loss";
                }
            },
            axisX: {
                labelFontFamily: "Pixelify Sans",
                labelFontColor: "#39135c",
                labelFontSize: 10,
                fontWeight: "bold"
            },
            data: [{
                type: "line",
                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>,
                lineColor: "#7d18de",
                markerColor: "#f856ab"
            }],
            backgroundColor: "#FFF",
            toolTip: {
                contentFormatter: function (e) {
                    var content = " ";
                    if (e.entries[0].dataPoint.y == 1) {
                        content += `<span style='color: #ffacd7; font-weight: bold;'>Won</span>`;
                    } else {
                        content += `<span style='color: #a977d8; font-weight: bold;'>Lost</span>`;
                    }
                    return content;
                }
            }
        });
        chart.render();
    }
    </script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>
</html>