<?php
class ImageDownloader {
    public static function download($url, $savePath) {
        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // $data = curl_exec($ch);
        // curl_close($ch);

        // return file_put_contents($savePath, $data) !== false;
        // ✅ URL이 올바른지 확인
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            error_log("🚨 잘못된 URL: " . $url);
            return false;
        }

        // ✅ cURL로 이미지 다운로드
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // ✅ HTTP 상태 코드 확인 (200이 아니면 다운로드 실패)
        if ($httpCode !== 200 || $imageData === false) {
            error_log("🚨 이미지 다운로드 실패 (HTTP 코드: " . $httpCode . ")");
            return false;
        }

        // ✅ 저장할 디렉토리가 존재하는지 확인하고 없으면 생성
        $saveDir = dirname($savePath);
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0777, true);
        }

        // ✅ 파일 저장
        if (file_put_contents($savePath, $imageData) === false) {
            error_log("🚨 파일 저장 실패: " . $savePath);
            return false;
        }

        // ✅ 저장된 파일 크기 확인
        if (filesize($savePath) < 100) { // 100바이트 미만이면 손상된 파일로 간주
            error_log("🚨 파일 크기가 너무 작음, 다운로드 실패: " . $savePath);
            unlink($savePath); // 손상된 파일 삭제
            return false;
        }

        return true;
    }
}
?>
