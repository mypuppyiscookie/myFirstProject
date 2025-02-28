<?php

function saveFruit($fruit, $meaning, $image1, $image2) {
    global $conn;
    include_once __DIR__ . "/../db.php";
    if(!$conn){
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if(!$conn){
            die("DB 연결 실패: ". mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    // ✅ 중복 데이터 확인 (fruitName이 이미 있는지 체크)
    $checkQuery = "SELECT fruitNo FROM crawlingFruits WHERE fruitName = '$fruit'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // ✅ 이미 존재하면 `UPDATE`
        $row = mysqli_fetch_assoc($checkResult);
        $fruitNo = $row["fruitNo"];
        $query = "UPDATE crawlingFruits SET fruitMeaning='$meaning', image1='$image1', image2='$image2' WHERE fruitNo='$fruitNo'";
    } else {
        // ✅ 존재하지 않으면 `INSERT`
        $query = "INSERT INTO crawlingFruits(fruitName, fruitMeaning, image1, image2) VALUES ('$fruit', '$meaning', '$image1', '$image2')";
    }
    return mysqli_query($conn, $query);    
}

function getFruit($fruit) {
    global $conn;
    include_once __DIR__ . "/../db.php";
    if(!$conn){
        $conn = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);
        if(!$conn){
            die("DB 연결 실패: ". mysqli_connect_error());
        }
    }
    mysqli_set_charset($conn, "utf8");

    $query = "SELECT * FROM crawlingFruits where fruitName='$fruit' LIMIT 1";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}
?>