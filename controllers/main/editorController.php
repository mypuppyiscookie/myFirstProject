<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

require_once __DIR__ . '/../../models/main/editorModel.php'; // 모델 파일 포함

ob_clean(); //불필요한 출력 제거
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "잘못된 요청"]);
    exit;
}

$action = $_GET["action"] ?? "";
$inputJSON = file_get_contents("php://input");
$inputData = json_decode($inputJSON, true);

$database = $inputData["database"] ?? "";
$table = $inputData["table"] ?? "";
$primaryKey = $inputData["primaryKey"] ?? "";
$primaryKeyData = $inputData["primaryKeyData"] ?? "";
$updates = $inputData["updates"] ?? [];
$changes = !empty($inputData["changes"]) ? $inputData["changes"] : [];
$newRows = $inputData["newRows"] ?? [];

if($action === "getPrimaryKey") { //기본키의 칼럼명 가져오기기
    if (!$table) {
        echo json_encode(["success" => false, "message" => "테이블이 지정되지 않았습니다."]);
        exit;
    }
    
    $primaryKey = getPrimaryKey($database, $table);
    
    if (!$primaryKey) {
        echo json_encode(["success" => false, "message" => "기본키가 존재하지 않습니다."]);
        exit;
    }
    
    echo json_encode(["success" => true, "primaryKey" => $primaryKey]); //기본키의의 컬럼명 출력
    exit;
} 

else if ($action === "updateRow") { //수정하기
    $postData = json_decode(file_get_contents("php://input"), true); //JSON을 연관 배열로 변환
    if (empty($database) || empty($table) || empty($primaryKey) || empty($updates)) {
        echo json_encode(["success" => false, "message" => "필수 데이터 누락락"]);
        exit;
    }

    $allSuccess = true;

    foreach ($updates as $update) {
        $primaryKeyData = $update["primaryKeyData"] ?? "";
        $updateData = $update["changes"] ?? [];

        $updateSuccess = updateTableRow($database, $table, $primaryKey, $updates);
        if (!$updateSuccess) {
            $allSuccess = false;
        }
    }

    if ($allSuccess) {
        echo json_encode(["success" => true, "message" => "모든 데이터가 성공적으로 수정되었습니다"]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "일부 데이터 수정 실패"]);
        exit;
    }
}

else if ($action === "addRow") {

    if (empty($database) || empty($table) || empty($newRows)) {
        echo json_encode(["success" => false, "message" => "필수 데이터가 누락되었습니다."]);
        exit;
    }

    $allSuccess = true;

    foreach ($newRows as $newData) {
        $insertSuccess = insertNewRow($database, $table, $newData);
        if (!$insertSuccess) {
            $allSuccess = false;
            echo json_encode(["success" => false, "message" => "일부 데이터 추가 실패"]);
            exit;
        }
    }

    if ($allSuccess) {
        echo json_encode(["success" => true, "message" => "데이터 추가 성공공"]);
    } else {
        echo json_encode(["success" => false, "message" => "일부 데이터 추가 실패"]);
    }
    exit;
}

else if ($action === "deleteRow") {

    if (empty($database) || empty($table) || empty($primaryKey)) {
        echo json_encode(["success" => false, "message" => "필수 데이터가 누락되었습니다."]);
        exit;
    }

    $deleteSuccess = deleteRowFromTable($database, $table, $primaryKey, $primaryKeyData);

    if ($deleteSuccess) {
        echo json_encode(["success" => true, "message" => "데이터 삭제 완료"]);
    } else {
        echo json_encode(["success" => false, "message" => "데이터 삭제 실패"]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => "잘못된 요청"]);
exit;
?>
