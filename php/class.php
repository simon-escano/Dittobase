<?php

class Database {
    private $conn;

    public function __construct($hostname, $username, $password, $database) {
        $this->conn = mysqli_connect($hostname, $username, $password, $database);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    public function query($sql) {
        return mysqli_query($this->conn, $sql);
    }

    public function select($table, $columns = '*', $where = '', $joins = []) {
        $sql = "SELECT $columns FROM $table";
        foreach ($joins as $join) {
            $type = isset($join[2]) ? strtoupper($join[2]) : 'JOIN';
            $sql .= " $type $join[0] ON $join[1]";
        }
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $result = $this->query($sql);
        if (!$result) {
            return [];
        }
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);
        return $rows;
    }

    public function select_one($table, $columns = '*', $where = '', $joins = []) {
        $sql = "SELECT $columns FROM $table";
        foreach ($joins as $join) {
            $type = isset($join[2]) ? strtoupper($join[2]) : 'JOIN';
            $sql .= " $type $join[0] ON $join[1]";
        }
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        $result = $this->query($sql);
        if (!$result) {
            return [];
        }
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $row;
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);
    
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = mysqli_prepare($this->conn, $sql);
    
        if (!$stmt) {
            return false;
        }
    
        $types = str_repeat('s', count($values));
        mysqli_stmt_bind_param($stmt, $types, ...$values);
    
        $result = mysqli_stmt_execute($stmt);
    
        mysqli_stmt_close($stmt);
    
        return $result;
    }

    public function delete($table, $where = '') {
        $sql = "DELETE FROM $table";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        return $this->query($sql);
    }
    
    public function update($table, $data, $where = '') {
        $setClause = '';
        foreach ($data as $column => $value) {
            $setClause .= "$column = ?, ";
        }
        $setClause = rtrim($setClause, ', ');
    
        $sql = "UPDATE $table SET $setClause";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
    
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            return false;
        }
    
        $types = str_repeat('s', count($data));
        $values = array_values($data);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
    
        $result = mysqli_stmt_execute($stmt);
    
        mysqli_stmt_close($stmt);
    
        return $result;
    }    

    public function close() {
        mysqli_close($this->conn);
    }
}

function getPokemonTypes($db, $where = "") {
    $joins = [
        ["tblTrainerPokemon AS TP", "TA.trainerAccountID = TP.trainerAccountID"],
        ["tblPokemon AS P1", "TP.pokemon1 = P1.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD1", "P1.pokedexID = PD1.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P2", "TP.pokemon2 = P2.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD2", "P2.pokedexID = PD2.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P3", "TP.pokemon3 = P3.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD3", "P3.pokedexID = PD3.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P4", "TP.pokemon4 = P4.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD4", "P4.pokedexID = PD4.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P5", "TP.pokemon5 = P5.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD5", "P5.pokedexID = PD5.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P6", "TP.pokemon6 = P6.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD6", "P6.pokedexID = PD6.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P7", "TP.pokemon7 = P7.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD7", "P7.pokedexID = PD7.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P8", "TP.pokemon8 = P8.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD8", "P8.pokedexID = PD8.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P9", "TP.pokemon9 = P9.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD9", "P9.pokedexID = PD9.pokedexID", "LEFT JOIN"],
        ["tblPokemon AS P10", "TP.pokemon10 = P10.spawnID", "LEFT JOIN"],
        ["tblPokedex AS PD10", "P10.pokedexID = PD10.pokedexID", "LEFT JOIN"]
    ];    

    return $db->select("tblTrainerAccount TA",
        "TA.firstname AS trainerName,
        TP.pokemon1 AS spawnID1,
        PD1.name AS name1,
        PD1.type1 AS type11,
        PD1.type2 AS type21,
        TP.pokemon2 AS spawnID2,
        PD2.name AS name2,
        PD2.type1 AS type12,
        PD2.type2 AS type22,
        TP.pokemon3 AS spawnID3,
        PD3.name AS name3,
        PD3.type1 AS type13,
        PD3.type2 AS type23,
        TP.pokemon4 AS spawnID4,
        PD4.name AS name4,
        PD4.type1 AS type14,
        PD4.type2 AS type24,
        TP.pokemon5 AS spawnID5,
        PD5.name AS name5,
        PD5.type1 AS type15,
        PD5.type2 AS type25,
        TP.pokemon6 AS spawnID6,
        PD6.name AS name6,
        PD6.type1 AS type16,
        PD6.type2 AS type26,
        TP.pokemon7 AS spawnID7,
        PD7.name AS name7,
        PD7.type1 AS type17,
        PD7.type2 AS type27,
        TP.pokemon8 AS spawnID8,
        PD8.name AS name8,
        PD8.type1 AS type18,
        PD8.type2 AS type28,
        TP.pokemon9 AS spawnID9,
        PD9.name AS name9,
        PD9.type1 AS type19,
        PD9.type2 AS type29,
        TP.pokemon10 AS spawnID10,
        PD10.name AS name10,
        PD10.type1 AS type110,
        PD10.type2 AS type210", $where, $joins);
}

function getTypes() {
    $types = array(
        "Normal" => "#A8A77A", "Fire" => "#EE8130", "Water" => "#6390F0", "Electric" => "#F7D02C",
        "Grass" => "#7AC74C", "Ice" => "#96D9D6", "Fighting" => "#C22E28", "Poison" => "#A33EA1",
        "Ground" => "#E2BF65", "Flying" => "#A98FF3", "Psychic" => "#F95587", "Bug" => "#A6B91A",
        "Rock" => "#B6A136", "Ghost" => "#735797", "Dragon" => "#6F35FC", "Dark" => "#705746",
        "Steel" => "#B7B7CE", "Fairy" => "#D685AD"
    );
    
    return $types;
}

function typeCounter($types) {
    $typesCount = array(
        "Normal" => 0, "Fire" => 0, "Water" => 0, "Electric" => 0,
        "Grass" => 0, "Ice" => 0, "Fighting" => 0, "Poison" => 0,
        "Ground" => 0, "Flying" => 0, "Psychic" => 0, "Bug" => 0,
        "Rock" => 0, "Ghost" => 0, "Dragon" => 0, "Dark" => 0,
        "Steel" => 0, "Fairy" => 0, "total" => 0
    );

    for($i = 1; $i<=10; $i++){
        $typeColumn1 =  $types["type1" . $i];
        $typeColumn2 = $types["type2" . $i];

        switch (true) {
            case ($typeColumn1 == "Normal" || $typeColumn2 == "Normal" ):
                $typesCount["Normal"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Fire" || $typeColumn2 == "Fire" ):
                $typesCount["Fire"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Water" || $typeColumn2 == "Water" ):
                $typesCount["Water"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Electric" || $typeColumn2 == "Electric" ):
                $typesCount["Electric"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Grass" || $typeColumn2 == "Grass" ):
                $typesCount["Grass"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Ice" || $typeColumn2 == "Ice" ):
                $typesCount["Ice"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Fighting" || $typeColumn2 == "Fighting" ):
                $typesCount["Fighting"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Poison" || $typeColumn2 == "Poison" ):
                $typesCount["Poison"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Ground" || $typeColumn2 == "Ground" ):
                $typesCount["Ground"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Flying" || $typeColumn2 == "Flying" ):
                $typesCount["Flying"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Psychic" || $typeColumn2 == "Psychic" ):
                $typesCount["Psychic"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Bug" || $typeColumn2 == "Bug" ):
                $typesCount["Bug"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Rock" || $typeColumn2 == "Rock" ):
                $typesCount["Rock"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Ghost" || $typeColumn2 == "Ghost" ):
                $typesCount["Ghost"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Dragon" || $typeColumn2 == "Dragon" ):
                $typesCount["Dragon"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Dark" || $typeColumn2 == "Dark" ):
                $typesCount["Dark"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Steel" || $typeColumn2 == "Steel" ):
                $typesCount["Steel"]++;
                $typesCount["total"]++;
                break;
            case ($typeColumn1 == "Fairy" || $typeColumn2 == "Fairy" ):
                $typesCount["Fairy"]++;
                $typesCount["total"]++;
                break;
        }
    }

    return $typesCount;
}

function countPokemon($db, $trainerID) {
    $sql = "SELECT 
            SUM(count_pokemon1) AS total_count_pokemon1,
            SUM(count_pokemon2) AS total_count_pokemon2,
            SUM(count_pokemon3) AS total_count_pokemon3,
            SUM(count_pokemon4) AS total_count_pokemon4,
            SUM(count_pokemon5) AS total_count_pokemon5,
            SUM(count_pokemon6) AS total_count_pokemon6,
            SUM(count_pokemon7) AS total_count_pokemon7,
            SUM(count_pokemon8) AS total_count_pokemon8,
            SUM(count_pokemon9) AS total_count_pokemon9,
            SUM(count_pokemon10) AS total_count_pokemon10,
            SUM(count_pokemon1 + count_pokemon2 + count_pokemon3 + count_pokemon4 + count_pokemon5 + count_pokemon6 + count_pokemon7 + count_pokemon8 + count_pokemon9 + count_pokemon10) AS total_count
        FROM (
            SELECT 
                COUNT(CASE WHEN pokemon1 IS NOT NULL AND pokemon1 != 0 THEN 1 END) AS count_pokemon1,
                COUNT(CASE WHEN pokemon2 IS NOT NULL AND pokemon2 != 0 THEN 1 END) AS count_pokemon2,
                COUNT(CASE WHEN pokemon3 IS NOT NULL AND pokemon3 != 0 THEN 1 END) AS count_pokemon3,
                COUNT(CASE WHEN pokemon4 IS NOT NULL AND pokemon4 != 0 THEN 1 END) AS count_pokemon4,
                COUNT(CASE WHEN pokemon5 IS NOT NULL AND pokemon5 != 0 THEN 1 END) AS count_pokemon5,
                COUNT(CASE WHEN pokemon6 IS NOT NULL AND pokemon6 != 0 THEN 1 END) AS count_pokemon6,
                COUNT(CASE WHEN pokemon7 IS NOT NULL AND pokemon7 != 0 THEN 1 END) AS count_pokemon7,
                COUNT(CASE WHEN pokemon8 IS NOT NULL AND pokemon8 != 0 THEN 1 END) AS count_pokemon8,
                COUNT(CASE WHEN pokemon9 IS NOT NULL AND pokemon9 != 0 THEN 1 END) AS count_pokemon9,
                COUNT(CASE WHEN pokemon10 IS NOT NULL AND pokemon10 != 0 THEN 1 END) AS count_pokemon10
            FROM tblTrainerPokemon
            WHERE trainerAccountID ='$trainerID'
        ) AS counts;";

    $result = $db->query($sql);
    if (!$result) {
        return [];
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_free_result($result);
    return $rows[0]['total_count'];
}

?>