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
            $sql .= " JOIN $join[0] ON $join[1]";
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
    

    public function select2($selectQuery, $where = "") {
        $sql = $selectQuery;
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

class Card {
    public $img;
    public $title;
    public $content;
    public $id;
    public function __construct($img, $title, $content, $id) {
        $this->img = $img;
        $this->title = $title;
        $this->content = $content;
        $this->id = $id;
    }

    public function html() {
        return '
        <div id="'. $this->id .'" class="card">
            <section class="card-header">
                <div class="card-img-box">
                    <img src="img/'. $this->img .'">
                </div>
                <p class="card-title">'. $this->title .'</p>
            </section>
            <section class="card-content">
            '. $this->content .'
            </section>
        </div>
        ';
    }
}

function card($img, $title) {
    $args = func_get_args();
    $contents = "";
    $id = "";
    array_shift($args);
    array_shift($args);
    foreach ($args as $content) {
        if (is_callable($content)) {
            $content = $content();
        }
        if (str_starts_with($content, "#")) {
            $content = substr($content, 1);
            $id = $content;
            continue;
        }
        $contents .= $content;
    }
    return (new Card($img, $title, $contents, $id))->html();
}

function part($orientation) {
    $args = func_get_args();
    $contents = "";
    array_shift($args);
    foreach ($args as $content) {
        if (is_callable($content)) {
            $content = $content();
        }
        $contents .= $content;
    }
    return '<div class="partition-'. $orientation .'">' . $contents .'</div>';
}

function div($class) {
    $args = func_get_args();
    $contents = "";
    $id = "";
    array_shift($args);
    foreach ($args as $content) {
        if (is_callable($content)) {
            $content = $content();
        }
        if (str_starts_with($content, "#")) {
            $content = substr($content, 1);
            $id = $content;
            continue;
        }
        $contents .= $content;
    }
    return '<div id="'. $id .'" class="' . $class . '">'. $contents .'</div>';
}

function p($class) {
    $args = func_get_args();
    $contents = "";
    array_shift($args);
    foreach ($args as $content) {
        if (is_callable($content)) {
            $content = $content();
        }
        $contents .= $content;
    }
    return '<p class="' . $class . '">'. $contents .'</p>';
}

function img($src) {
    $args = func_get_args();
    $classes = "";
    array_shift($args);
    foreach ($args as $arg) {
        $classes .= $arg;
    }
    return '<img class="'. $classes .'" src="'. $src .'">';
}

function button($action, $class, $content) {
    $args = func_get_args();
    $values = "";
    array_shift($args);
    array_shift($args);
    array_shift($args);
    foreach ($args as $arg) {
        if (is_single_assoc_array($arg)) {
            $values .= '<input type="hidden" name="'. key($arg) .'" value="'. reset($arg) .'">';
        }
    }
    return '
    <form class="'. $class .'-form" action="'. $action .'.php" method="post">
        '. $values .'
        <button class="'. $class .'" name="'. $action .'" type="submit">'. $content .'</button>
    </form>
    ';
}

function pieChart($total) {
    $args = func_get_args();
    array_shift($args);
    $headers = "";
    $values = "";
    $colors = ["var(--secondary)", "var(--secondary-verylight)", "var(--primary)"];
    $totalColor = "var(--primary-dark) ";

    $headers .= div("pie-chart-header",
        '<div class="pie-chart-header-percent" style="background: '. $totalColor .'">'. key($total) . ': ' . reset($total) . '</div>'
    );

    $sum = 0;
    for ($i = 0; $i < count($args); $i++) {
        $arg = $args[$i];
        if (is_single_assoc_array($arg)) {
            $total_num = reset($total);
            $name = key($arg);
            $value = reset($arg);
            $prev = $i != 0 ? reset($args[$i - 1]) : 0;
            $color = $colors[$i % count($colors)] . " ";
            $headers .= 
            div("pie-chart-header",
                '<div class="pie-chart-header-percent" style="background: '. $color .'">'. percent($total_num, $value, true) .'</div>',
                p("pie-chart-header-text", $name . ": " . $value)
            );
            $values .= $color . $sum . "% " . $sum + percent($total_num, $value) . "%, ";
            $sum += percent($total_num, $value);
            if ($i == count($args) - 1) {
                $values .= $totalColor . $sum . "% ";
            }
        }
    }

    
    return 
    div("pie-chart-container",
        div("pie-chart-headers", $headers),
        '<div class="pie-chart" style="background: conic-gradient('. $values .');"></div>'
    );
}

function is_single_assoc_array($array) {
    return is_array($array) && count($array) == 1 && is_string(key($array));
}

function percent($total, $value, $string = false) {
    $percent = null;
    if (!$value) {
        $percent = 0;
    } else {
        $percent = ($value / $total) * 100;
    }
    if ($string) {
        $percent = intval($percent) . "%";
    }
    return $percent;
}

function getPokemonTypes($db, $where = "") {
    return $db->select2("SELECT 
        TA.firstname AS trainerName,
        TP.pokemon1 AS spawnID1,
        PD1.type1 AS type11,
        PD1.type2 AS type21,
        TP.pokemon2 AS spawnID2,
        PD2.type1 AS type12,
        PD2.type2 AS type22,
        TP.pokemon3 AS spawnID3,
        PD3.type1 AS type13,
        PD3.type2 AS type23,
        TP.pokemon4 AS spawnID4,
        PD4.type1 AS type14,
        PD4.type2 AS type24,
        TP.pokemon5 AS spawnID5,
        PD5.type1 AS type15,
        PD5.type2 AS type25,
        TP.pokemon6 AS spawnID6,
        PD6.type1 AS type16,
        PD6.type2 AS type26,
        TP.pokemon7 AS spawnID7,
        PD7.type1 AS type17,
        PD7.type2 AS type27,
        TP.pokemon8 AS spawnID8,
        PD8.type1 AS type18,
        PD8.type2 AS type28,
        TP.pokemon9 AS spawnID9,
        PD9.type1 AS type19,
        PD9.type2 AS type29,
        TP.pokemon10 AS spawnID10,
        PD10.type1 AS type110,
        PD10.type2 AS type210
    FROM 
        tblTrainerAccount AS TA
    JOIN 
        tblTrainerPokemon AS TP ON TA.trainerAccountID = TP.trainerAccountID
    LEFT JOIN 
        tblPokemon AS P1 ON TP.pokemon1 = P1.spawnID
    LEFT JOIN 
        tblPokedex AS PD1 ON P1.pokedexID = PD1.pokedexID
    LEFT JOIN 
        tblPokemon AS P2 ON TP.pokemon2 = P2.spawnID
    LEFT JOIN 
        tblPokedex AS PD2 ON P2.pokedexID = PD2.pokedexID
    LEFT JOIN 
        tblPokemon AS P3 ON TP.pokemon3 = P3.spawnID
    LEFT JOIN 
        tblPokedex AS PD3 ON P3.pokedexID = PD3.pokedexID
    LEFT JOIN 
        tblPokemon AS P4 ON TP.pokemon4 = P4.spawnID
    LEFT JOIN 
        tblPokedex AS PD4 ON P4.pokedexID = PD4.pokedexID
    LEFT JOIN 
        tblPokemon AS P5 ON TP.pokemon5 = P5.spawnID
    LEFT JOIN 
        tblPokedex AS PD5 ON P5.pokedexID = PD5.pokedexID
    LEFT JOIN 
        tblPokemon AS P6 ON TP.pokemon6 = P6.spawnID
    LEFT JOIN 
        tblPokedex AS PD6 ON P6.pokedexID = PD6.pokedexID
    LEFT JOIN 
        tblPokemon AS P7 ON TP.pokemon7 = P7.spawnID
    LEFT JOIN 
        tblPokedex AS PD7 ON P7.pokedexID = PD7.pokedexID
    LEFT JOIN 
        tblPokemon AS P8 ON TP.pokemon8 = P8.spawnID
    LEFT JOIN 
        tblPokedex AS PD8 ON P8.pokedexID = PD8.pokedexID
    LEFT JOIN 
        tblPokemon AS P9 ON TP.pokemon9 = P9.spawnID
    LEFT JOIN 
        tblPokedex AS PD9 ON P9.pokedexID = PD9.pokedexID
    LEFT JOIN 
        tblPokemon AS P10 ON TP.pokemon10 = P10.spawnID
    LEFT JOIN 
        tblPokedex AS PD10 ON P10.pokedexID = PD10.pokedexID;", $where);
}

?>