<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

require_once "../../models/main/queryModel.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data["query"]) || empty(trim($data["query"]))) {
        echo json_encode([]);
        exit;
    }

    $database = new Database();
    $result = $database->executeQuery($data["query"]);
    $database->close();

    echo json_encode($result);
}
?>
