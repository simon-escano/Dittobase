<?php

require 'php/connect.php';

if (isset($_POST['add_to_team'])) {
    $trainerID = $_POST['trainerID'];
    $updatedData = array(
        'trainerAccountID' => $trainerID,
        'chosenPokemon1' => $_POST['pokemonID']
    );
    $success = $db->update('tblPokemonTeam', $updatedData, "trainerAccountID='" . $trainerID . "'");

    if ($success) {
        header('Location: index.php');
        exit();
    } else {
        echo "Failed to add Pokemon to the team.";
    }
}

?>
