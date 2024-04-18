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
        return $this->query($sql);
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

        $this->html();
    }

    public function html() {
        echo '
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

?>