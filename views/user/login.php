<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>dkbLogin</title>
    <link rel="stylesheet" href=" /css/style.css">
</head>

<body>
    <div>
        <p class="logo">DataKimboBase</p>
        <form id="loginForm" class="loginForm">
            <input type="text" name="id" class="inputBig" placeholder="아이디">
            <input type="password" name="password" class="inputBig" placeholder="비밀번호">
            <button type="submit" class="loginBtn">로그인</button>
            <div class="loginLink">
                <a href="/views/user/join/join.php" class="goto">회원가입</a>
                <a href="/views/user/find.php" class="goto">아이디/비밀번호 찾기</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function (event) {
            event.preventDefault(); // 기본 폼 제출 동작 방지

            let formData = new FormData(this);

            fetch("../../controllers/user/loginController.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.text()) // ✅ JSON 변환 전에 텍스트로 먼저 확인
                .then(text => {
                    console.log("서버 응답:", text); // ✅ 응답을 콘솔에서 확인

                    try {
                        const data = JSON.parse(text); // ✅ JSON 변환 시도
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            alert(data.message);
                        }
                    } catch (error) {
                        console.error("JSON 변환 오류:", error, "서버 응답:", text);
                        alert("서버에서 JSON 형식이 올바르게 반환되지 않았습니다.");
                    }
                })
                .catch(error => console.log("Error:" + error));
        })
    </script>
</body>

</html>