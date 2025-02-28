<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header("Content-Type: application/json; charset=UTF-8");

//중복 출력 방지
if (ob_get_contents()) {
    ob_clean();
}
// 필요한 파일 포함
require_once __DIR__ . '/../../models/main/crawlingModel.php';
require_once __DIR__ . '/../../services/imageDownload.php';
require_once __DIR__ . '/../../services/crawlingService.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "잘못된 요청"]);
    exit;
}

$action = $_POST["action"] ?? "search"; //요청 타입 구분 기본값은 search

$fruit = $_POST["fruit"] ?? "";

$fruitData = crawlFruit($fruit);

$meaning = $fruitData["meaning"] ?? "";
$image1 = $fruitData["image1"] ?? "";
$image2 = $fruitData["image2"] ?? "";

if ($action === "search") {
    echo json_encode([
        "success" => true,
        "fruit" => $fruit,
        "meaning" => $meaning,
        "image1" => $image1,
        "image2" => $image2
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === "get") {
    $existingData = getFruit($fruit);
    if ($existingData) {
        $base_url = "http://192.168.1.139/"; //자신의 도메인 주소로 변경

        //기존 상대 경로를 절대 경로로 변환 
        $image1_url = $base_url . ltrim($existingData["image1"], "/");
        $image2_url = $base_url . ltrim($existingData["image2"], "/");

        echo json_encode([
            "success" => true,
            "fruit" => $existingData["fruitName"],
            "meaning" => $existingData["fruitMeaning"],
            "image1" => $image1_url,
            "image2" => $image2_url
        ], JSON_UNESCAPED_UNICODE);
        die();
    } else {
        //데이터베이스에 존재하지 않는 경우 "존재하지 않는 데이터" 반환
        echo json_encode([
            "success" => false,
            "message" => "존재하지 않는 데이터입니다."
        ], JSON_UNESCAPED_UNICODE);
        die();
    }
}

//이미지 저장 경로 설정
$save_directory = __DIR__ . '/../../images/fruits/';

//저장할 파일 경로로
$image1_local = rtrim($save_directory, '/') . '/' . trim($fruit) . "1.jpg";
$image2_local = rtrim($save_directory, '/') . '/' . trim($fruit) . "2.jpg";

//이미지 다운로드 실행
$download1 = ImageDownloader::download($image1, $image1_local);
$download2 = ImageDownloader::download($image2, $image2_local);

if (!$download1 || !$download2) {
    echo json_encode(["success" => false, "message" => "이미지 다운로드 실패"]);
    exit;
}

saveFruit($fruit, $meaning, str_replace(__DIR__ . '/../../', '', $image1_local), str_replace(__DIR__ . '/../../', '', $image2_local));

echo json_encode([
    "success" => true,
    "message" => "저장 성공",
    "fruit" => $fruit,
    "meaning" => $meaning,
    "image1" => str_replace(__DIR__ . '/../../', '', $image1_local),
    "image2" => str_replace(__DIR__ . '/../../', '', $image2_local)
], JSON_UNESCAPED_UNICODE);
exit;
