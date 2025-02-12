<?php
class Database {
    private $conn;
    public function executeQuery($sql) {
        include_once __DIR__ . "/../db.php";

        $this->conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);

        if (!$this->conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }

        mysqli_set_charset($this->conn, "utf8");

        $result=$this->conn->query($sql);
        if ($result instanceof mysqli_result) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } else if($this->conn->query($sql) === TRUE) {
            return ["success" => true, "message" => "success!"];
        } else {
            return [
                "success" => false,
                "message" => "Error: " . ($this->conn->errno ? $this->conn->errno . " - " . $this->conn->error : "쿼리가 실행되지 않음"),
                "query" => $sql
            ];
        }
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
