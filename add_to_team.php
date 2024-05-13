<?php

require 'php/connect.php';

if (isset($_POST['add_to_team'])) {
    $pokemonTeam = $db->select("tblPokemonTeam", "*", "trainerAccountID='$currentUser'")[0];
    $alreadyAdded = false;
    $position = null;
    for ($i = 1; $i <= 6; $i++) {
        $chosenPokemon = $pokemonTeam['chosenPokemon' . $i];
        if ($chosenPokemon == $_POST['spawnID']) {
            $alreadyAdded = true;
        }
        if (!$chosenPokemon) {
            if (!$position) {
                $position = $i;
            }
        }
    }
    $updatedData = array(
        'trainerAccountID' => $currentUser,
        'chosenPokemon' . $position => $_POST['spawnID']
    );
    $success = ($alreadyAdded || !$position) ? true : $db->update('tblPokemonTeam', $updatedData, "trainerAccountID='$currentUser'");
    if ($success) {
        header('Location: index.php');
        exit();
    } else {
        echo "Failed to add Pokemon to the team.";
    }
}

?>
