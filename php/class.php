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

    public function select($table, $columns = '*', $where = '') {
        $sql = "SELECT $columns FROM $table";
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

    public function select2($selectQuery) {
        $sql = $selectQuery;
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
    public function __construct($img, $title, $content) {
        $this->img = $img;
        $this->title = $title;
        $this->content = $content;
    }

    public function html() {
        return '
        <div class="card">
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
    array_shift($args);
    array_shift($args);
    foreach ($args as $content) {
        if (is_callable($content)) {
            $content = $content();
        }
        $contents .= $content;
    }
    return (new Card($img, $title, $contents))->html();
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
    array_shift($args);
    foreach ($args as $content) {
        if (is_callable($content)) {
            $content = $content();
        }
        $contents .= $content;
    }
    return '<div class="' . $class . '">'. $contents .'</div>';
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

?>