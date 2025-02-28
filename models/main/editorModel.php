<?php
function getPrimaryKey($table)
{
    global $conn;
    include_once __DIR__ . "/../db.php";
    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");
    mysqli_select_db($conn, $database);

    //기본키 조회 쿼리 
    $query = "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'";
    $result = mysqli_query($conn, $query);

    //오류 발생 시 false 반환
    if (!$result) {
        error_log("기본키 조회 실패: " . mysqli_error($conn));
        return false;
    }

    //기본키 존재 여부 확인
    $row = mysqli_fetch_assoc($result);
    $primaryKey = $row ? $row["Column_name"] : false; // 기본키 컬럼명 반환, 없다면 false
    return $primaryKey;
}

function updateTableRow($database, $table, $primaryKey, $updates)
{
    global $conn;
    include_once __DIR__ . "/../db.php";
    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");
    mysqli_select_db($conn, $database);

    if (empty($updates)) {
        error_log("업데이트할 데이터가 없음");
        return false;
    }

    //테이블의 컬럼 정보 가져오기
    $columnsInfo = [];
    $query = "SHOW COLUMNS FROM `$table`";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        //배열로 변환하여 저장 (이전에는 단순 문자열 저장이었음)
        $columnsInfo[$row["Field"]] = [
            "type" => strtolower($row["Type"]),
            "not_null" => ($row["Null"] === "NO"),
        ];
    }

    // 여러 개의 업데이트 수행
    foreach ($updates as $update) {
        $primaryKeyData = $update["primaryKeyData"];
        $changes = $update["changes"];

        if (empty($changes)) {
            continue; // 변경 사항이 없는 경우 스킵
        }

        $columns = [];

        foreach ($changes as $col => $value) {
            // $columns[] = "`$col` = '" . mysqli_real_escape_string($conn, $value) . "'";
            $validationResult = validateColumnData($col, $value, $columnsInfo);
            if (!$validationResult["success"]) {
                return ["success" => false, "message" => $validationResult["message"]];
            }

            //검증된 값 업데이트
            $validatedValue = $validationResult["value"];
            $columns[] = "`$col` = '" . mysqli_real_escape_string($conn, $validatedValue) . "'";
        }

        //SQL 업데이트 쿼리 생성
        $query = "UPDATE `$table` SET " . implode(", ", $columns) . " WHERE `$primaryKey` = '$primaryKeyData'";

        //SQL 실행
        $result = mysqli_query($conn, $query);

        if (!$result) {
            error_log("SQL 실행 실패: " . mysqli_error($conn));
            echo json_encode(["success" => false, "message" => "SQL 실행 실패: " . mysqli_error($conn)]);
            exit;
        }
    }

    return true;
}

function insertNewRow($database, $table, $newData)
{
    global $conn;
    include_once __DIR__ . "/../db.php";

    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");
    mysqli_select_db($conn, $database);

    if (empty($newData) || !is_array($newData)) { //배열이 아닌 경우
        return ["success" => false, "message" => "추가할 데이터가 없습니다."];
    }

    //테이블 컬럼 정보 가져오기
    $columnsInfo = []; //테이블에 컬럼 정보 저장(컬럼명 -> 데이터 타입)
    $autoIncrementColumn = null; //자동 증가 컬럼 확인 
    $query = "SHOW COLUMNS FROM `$table`";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $columnsInfo[$row["Field"]] = [ //컬럼 정보 배열에 저장 
            "type" => strtolower($row["Type"]), //컬럼의 데이터 타입 소문자로 변경 
            "not_null" => ($row["Null"] === "NO"), // NOT NULL이면 true
        ];
        if (strpos(strtolower($row["Extra"]), "auto_increment") !== false) {
            $autoIncrementColumn = $row["Field"]; //자동 증가 컬럼이면 저장 
        }
    }

    //입력값 검증 및 타입 변환
    foreach ($newData as $col => $value) {
        if (!isset($columnsInfo[$col])) {
            return ["success" => false, "message" => "'$col' 컬럼은 존재하지 않습니다!"];
        }
        // `AUTO_INCREMENT` 컬럼 및 `createdDate` 같은 TIMESTAMP 필드 제거
        if ($col === $autoIncrementColumn || preg_match("/datetime|timestamp/", $columnsInfo[$col]["type"])) {
            unset($newData[$col]);
            continue;
        }
        //데이터 검증 함수 수행
        $validationResult = validateColumnData($col, $value, $columnsInfo);
        if (!$validationResult["success"]) {
            return $validationResult; // 검증 실패 시 즉시 반환
        }
        //검증 통과한 값 적용 (형태 변환된 값 저장)
        $newData[$col] = $validationResult["value"];
    }

    //SQL 쿼리 생성 및 실행
    $columns = array_keys($newData); //컬럼명 리스트 가져오기 
    $values = array_map(function ($value) {
        return ($value === null) ? "NULL" : "'$value'"; // 값이 NULL이면 "NULL"을 저장, 아니면 문자열로 반환
    }, array_values($newData));

    //최종 SQL 쿼리: INSERT INTO 테이블명 (컬럼1, 컬럼2) VALUES ('값1', '값2')
    $query = "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $values) . ")";
    $result = mysqli_query($conn, $query);

    //SQL 실행 결과 확인
    if (!$result) {
        $errorMsg = mysqli_error($conn);
        return ["success" => false, "message" => "SQL 실행 실패: " . $errorMsg];
    }

    return ["success" => true, "message" => "새로운 행이 추가되었습니다!"];
}


function deleteRowFromTable($database, $table, $primaryKey, $primaryKeyData)
{
    global $conn;
    include_once __DIR__ . "/../db.php";

    if (!$conn) {
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if (!$conn) {
            die("DB 연결 실패: " . mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");
    mysqli_select_db($conn, $database);

    // 🔥 primaryKeyData가 배열인지 확인 후 문자열 변환
    if (is_array($primaryKeyData)) {
        $primaryKeyData = implode("','", $primaryKeyData); // '1','2','3' 형태로 변환
        $primaryKeyData = "'$primaryKeyData'"; // 최종적으로 '1','2','3' 형태 유지
    } else {
        $primaryKeyData = "'$primaryKeyData'"; // 단일 값일 경우 그대로 사용
    }

    // ✅ 여러 개의 행을 삭제할 수 있도록 IN (...) 사용
    $query = "DELETE FROM `$table` WHERE `$primaryKey` IN ($primaryKeyData)";
    error_log("📝 실행할 SQL: " . $query);

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("SQL 실행 실패: " . mysqli_error($conn));
        return false;
    }

    // ✅ 삭제된 행이 없으면 false 반환
    if (mysqli_affected_rows($conn) === 0) {
        error_log("⚠ 삭제된 행이 없음.");
        return false;
    }

    return true;
}


function validateColumnData($col, $value, $columnsInfo)
{
    if (!isset($columnsInfo[$col])) {
        return ["success" => false, "message" => "'$col' 컬럼은 존재하지 않습니다"];
    }

    $columnType = strtolower($columnsInfo[$col]["type"]); // 컬럼 타입 정보
    $isNotNull = $columnsInfo[$col]["not_null"]; // NOT NULL 여부

    //`NOT NULL` 컬럼인데 값이 비어 있으면 즉시 오류 반환
    if ($isNotNull && (trim($value) === "" || $value === null)) {
        return ["success" => false, "message" => "'$col' 필드는 필수 입력값입니다!"];
    }

    // DATETIME, DATE, TIMESTAMP 필드 검증
    if (preg_match("/datetime|timestamp|date/i", $columnType)) {
        if (trim($value) === "") {
            return ["success" => true, "value" => null]; // 빈 값이면 NULL 저장
        } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/", $value)) {
            return ["success" => false, "message" => "'$col' 필드는 YYYY-MM-DD 또는 YYYY-MM-DD HH:MM:SS 형식이어야 합니다!"];
        }
    }

    // 숫자 타입 검사 (INT, FLOAT, DOUBLE, DECIMAL) → 오직 숫자만
    if (preg_match("/int|float|double|decimal/", $columnType)) {
        if (!preg_match('/^-?\d+(\.\d+)?$/', trim($value)) && trim($value) !== "") {
            return ["success" => false, "message" => "'$col' 필드는 숫자만 입력 가능합니다!"];
        }
        $value = $value * 1; // 숫자로 변환
    }

    // 날짜 타입 검사
    if (preg_match("/date|datetime|timestamp/i", $columnType)) {
        if (strtotime($value) === false) {
            return ["success" => false, "message" => "'$col' 필드는 올바른 날짜 형식이어야 합니다!"];
        }
        return ["success" => true, "value" => $value]; // 날짜 검증 통과 후 즉시 반환
    }

    // 검증 성공 시 원래 값 반환
    return ["success" => true, "value" => $value];
}
