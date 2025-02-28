<?php

//요청 방식이 POST가 아닐 경우, 잘못된 요청으로 처리하고 종료료
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "잘못된 요청"]);
    exit;
}

//POST 데이터에서 'fruit' 값을 가져오고 값이 없다면 빈 문자열 할당당
$fruit = $_POST["fruit"] ?? "";

//'fruit' 값이 비어있으면 오류 메세지를 반환하고 종료료
if (empty($fruit)) {
    echo json_encode(["success" => false, "message" => "과일 이름을 입력하세요"]);
    exit;
}

function crawlFruit($fruit)
{
    //네이버 검색 차단을 우회하기 위해 User-Agent와 Referer 헤더 설정, 봇이 아닌 실제 브라우저에서 요청한 것처럼 인식하도록
    $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Referer: https://www.naver.com/'
    ]; 

    //네이버 검색에서 해당 과일의 뜻을 검색하는 URL 생성
    $search_url = "https://search.naver.com/search.naver?query=" . urlencode($fruit . " 뜻");
    //검색 결과 페이지 html 가져오기
    $search_html = fetchPage($search_url, $headers);

    //기본적으로 "사전 정보를 가져올 수 없음"으로 성정
    $meaning = "사전 정보를 가져올 수 없음";

    if ($search_html) {
        $dom = new DOMDocument(); //DOM 사용용
        @$dom->loadHTML($search_html); // HTML 파싱 중 오류 메시지를 억제하기 위해 @ 사용
        $xpath = new DOMXPath($dom); //DOMXPath 객체를 생성하여 DOMDocument 객체와 연결 DOMXPath를 사용하면 HTML 문서에서 특정 태그나 속성 값을 간단한 쿼리로 찾을 수 있다.

        $xpaths = [
            '//p[contains(@class, "api_txt_lines")]',       // 일반적인 뜻
            '//p[contains(@class, "mean")]',                // 추가적인 뜻
            '//ul[contains(@class, "addition_info")]//li',  // 추가 정보
            '//span[contains(@class, "text")]',             // 추가적인 설명
            '//span[contains(@class, "word")]',             // 단어 자체 설명
            '//div[contains(@class, "data_txt")]',          // 특정 페이지의 뜻
            '//dd[contains(@class, "desc")]',               // 네이버 사전 스타일
            '//div[contains(@class, "short_def")]',         // 짧은 정의
            '//div[contains(@class, "auto_complete")]//span', // 자동 완성 뜻
            '//div[contains(@class, "txt_inline")]',        // 설명 줄
            '//div[contains(@class, "card_section")]//p',   // 검색 카드 내용
            '//div[contains(@class, "k_dic_section")]//p',  // 네이버 국어사전
            '//div[contains(@class, "dic_txt")]//p',        // 네이버 백과사전
            '//div[contains(@class, "meaning")]//p',        // 네이버 지식백과
            '//div[contains(@class, "api_subject_bx")]//strong', // 검색 주제 강조
            '//div[contains(@class, "api_cs_wrap")]//p',    // 네이버 검색 카드 요약
            '//div[contains(@class, "cm_content_wrap")]//p', // 네이버 검색 카드 요약 내용
            '//div[contains(@class, "summary_box")]//p',    // 요약 내용 (긴 뜻일 경우)
            '//div[contains(@class, "sentence")]//p',       // 예문과 함께 제공되는 뜻
            '//div[contains(@class, "section_area")]//p',   // 네이버 특정 사전에서 검색된 뜻
            '//ul[contains(@class, "list")]//li',           // 리스트로 제공되는 뜻
            '//div[contains(@class, "word_dfn")]//p',       // 영어 단어 뜻
            '//div[contains(@class, "synonym")]//p',        // 유의어 및 동의어
            '//p'                                           // 마지막 예비 옵션 (모든 p 태그)
        ];
        

        foreach ($xpaths as $path) {
            $mean_element = $xpath->query($path)->item(0);
            if ($mean_element) {
                $meaning = trim($mean_element->textContent);
                break;
            }
        } // 여러 XPath 경로 중에서 첫 번째로 찾은 내용을 의미로 설정
    }

    // 네이버에서 이미지 검색하는 URL 생성
    $image_search_url = "https://search.naver.com/search.naver?query=" . urlencode($fruit);
    $image_html = fetchPage($image_search_url, $headers); // 이미지 검색 결과 페이지 HTML 가져오기

    // 기본 이미지 값 설정
    $image1 = "";
    $image2 = "";

    if ($image_html) {
        $dom = new DOMDocument();
        @$dom->loadHTML($image_html);
        $xpath = new DOMXPath($dom);

        $xpaths = [
            '//div[contains(@class, "thumb")]//img',  // 일반적인 썸네일
            '//img[contains(@class, "_image")]',      // 네이버 이미지 검색
            '//img[contains(@class, "api_get")]',     // 네이버에서 제공하는 썸네일
            '//img[contains(@src, "https")]',         // HTTPS 링크가 있는 모든 이미지
            '//div[contains(@class, "img_area")]//img', // 이미지 영역
            '//ul[contains(@class, "photo_grid")]//img', // 이미지 그리드 내부
            '//a[contains(@class, "img_wrap")]//img',  // 이미지가 링크 내부에 있는 경우
            '//div[contains(@class, "image")]//img',  // 일반적인 이미지 영역
            '//div[contains(@class, "photo_bx")]//img', // 포토 박스 내 이미지
            '//span[contains(@class, "thumb")]//img', // 썸네일 내 이미지
            '//div[contains(@class, "image_area")]//img', // 이미지 콘텐츠 영역
            '//ul[contains(@class, "photo_list")]//img', // 네이버 이미지 리스트
            '//a[contains(@class, "image_link")]//img', // 링크 내부 이미지
            '//div[contains(@class, "photo_detail")]//img', // 상세 이미지
            '//div[contains(@class, "img_wrap")]//img', // 이미지 래핑된 곳
            '//div[contains(@class, "media_thumb")]//img' // 동영상/이미지 썸네일
        ];

        $found_images = [];

        // XPath 경로 중에서 유효한 이미지 URL을 찾으면 저장
        foreach ($xpaths as $path) {
            $img_elements = $xpath->query($path);
            foreach ($img_elements as $img) {
                $src = $img->getAttribute("src");
                if (!in_array($src, $found_images) && filter_var($src, FILTER_VALIDATE_URL)) { // 유효한 URL인지 확인 후 중복 방지
                    $found_images[] = $src;
                }
                if (count($found_images) >= 2) {
                    break 2;
                }
            }
        }

        if (count($found_images) == 0) {
            $image1 = "이미지를 찾을 수 없음";
            $image2 = "이미지를 찾을 수 없음";
        } else {
            $image1 = $found_images[0] ?? "이미지를 찾을 수 없음";
            $image2 = $found_images[1] ?? $found_images[0] ?? "이미지를 찾을 수 없음"; // 한 개만 있으면 같은 걸 복사
        }
    }

    return ["meaning" => $meaning, "image1" => $image1, "image2" => $image2];
}

//네이버 차단을 우회하기 위해 cURL 사용 (User-Agent 및 Referer 추가)
function fetchPage($url, $headers = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate'); // 압축된 페이지도 받아옴
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// $fruit = isset($_GET['fruit']) ? $_GET['fruit'] : '';

$fruitData = crawlFruit($fruit);

// echo json_encode([
//     "success" => true,
//     "fruit" => $fruit,
//     "meaning" => $fruitData["meaning"],
//     "image1" => $fruitData["image1"],
//     "image2" => $fruitData["image2"]
// ], JSON_UNESCAPED_UNICODE);

?>