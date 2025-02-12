<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>아이디/비밀번호 찾기</title>
    <link rel="stylesheet" href=" /css/style.css">
</head>

<body>
    <div class="findCont">
        <div class="line1">
            <button id="findIdToggle" class="findIdToggle" onclick="findToggle('id')">아이디 찾기</button>
            <button id="findPwdToggle" class="findPwdToggle" onclick="findToggle('pwd')">비밀번호 찾기</button>
        </div>

        <form id="findIdForm" class="active">
            <input type="text" id="name" class="inputBig" placeholder="이름">
            <input type="email" id="email" class="inputBig" placeholder="이메일">
            <button class="findBtn" onclick="findId()">아이디 찾기</button>
        </form>

        <form id="findPwdForm" class="active">
            <input type="text" id="id" class="inputBig" placeholder="아이디">
            <button class="findBtn" onclick="findPwd()">비밀번호 찾기</button>
        </form>
    </div>

    <script>
        window.onload = function() {
            findToggle('id');
        }

        function findToggle(type) {
            document.getElementById("findIdForm").classList.remove("active");
            document.getElementById("findPwdForm").classList.remove("active");
            document.getElementById("findIdToggle").classList.remove("active");
            document.getElementById("findPwdToggle").classList.remove("active");
            document.getElementById("findIdToggle").style.borderColor ="rgba(146,19,19,1) rgba(146,19,19,1) rgba(251, 251, 251, 1) rgba(146,19,19,1)";

            if (type === 'id') {
                document.getElementById("findPwdForm").classList.add("active");
                document.getElementById("findIdToggle").style.borderColor ="rgba(146,19,19,1) rgba(146,19,19,1) rgba(251, 251, 251, 1) rgba(146,19,19,1)";
                document.getElementById("findPwdToggle").style.borderColor = "";
            } else {
                document.getElementById("findIdForm").classList.add("active");
                document.getElementById("findPwdToggle").style.borderColor ="rgba(146,19,19,1) rgba(146,19,19,1) rgba(251, 251, 251, 1) rgba(146,19,19,1)";
                document.getElementById("findIdToggle").style.borderColor = "";
            }
        }
        
        function findId() {
            let name = document.getElementById("name").value;
            let email = document.getElementById("email").value;

            fetch("../../controllers/user/findController.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `action=findId&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`
                })
                .then(response => response.json())
                .then(data => alert(data.message || "아이디: " + data.userId));
        }

        function findPwd() {
            let id = document.getElementById("id").value;

            fetch("../../controllers/user/findController.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `action=findPwd&id=${encodeURIComponent(id)}`
                })
                .then(response => response.json())
                .then(data => alert(data.message));
        }

    </script>
</body>

</html>