<?php

require 'php/connect.php';

if (isset($_POST['remove_from_team'])) {
    $pokemonTeam = $db->select("tblPokemonTeam", "*", "trainerAccountID='$currentUser'")[0];

    for ($i = 1; $i <= 6; $i++) {
        $chosenPokemon = $pokemonTeam['chosenPokemon' . $i];
        if ($chosenPokemon == $_POST['spawnID']) {
            break;
        }
    }
    $updatedData = array(
        'trainerAccountID' => $currentUser,
        'chosenPokemon' . $i => null
    );
    $success = $db->update('tblPokemonTeam', $updatedData, "trainerAccountID='$currentUser'");

    if ($success) {
        header('Location: index.php');
        exit();
    } else {
        echo "Failed to remove Pokemon from the team.";
    }
}

?>
