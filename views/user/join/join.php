<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DKBRegister</title>
    <link rel="stylesheet" href=" /css/style.css">
</head>

<body>
    <div>
        <div class="logo">회원가입</div>

        <form id="joinForm">
            <div class="line1">
                <input type="text" id="id" name="id" class="inputSmall" placeholder="아이디: 영문, 숫자 포함 5~13자리">
                <button type="button" id="checkId" name="checkId" class="duplicateBtn" id="checkIdBtn">중복 확인</button>
            </div>

            <input type="password" name="password" class="inputBig" placeholder="비밀번호: 영문, 숫자, 특수문자 포함 8자리 이상">

            <div class="line3">
                <input type="text" name="name" class="inputSmall" placeholder="이름">
                <div class="genderContainer">
                    <button id="maleBtn" class="maleBtn" type="button" onclick="selectGender('남')">남</button>
                    <button id="femaleBtn" class="femaleBtn" type="button" onclick="selectGender('여')">여</button>
                    <!-- 선택한 성별 저장 -->
                    <input type="hidden" id="genderInput" name="gender">
                </div>
            </div>

            <input type="email" name="email" class="inputBig" placeholder="이메일">

            <input type="text" name="birthday" class="inputBig" placeholder="생년월일 8자리 ex)19990902">

            <input type="submit" name="register" id="submitBtn" value="회원가입" class="loginBtn">
        </form>
    </div>
    <script>
        function selectGender(gender) {
            document.getElementById("genderInput").value = gender;

            let maleBtn = document.getElementById("maleBtn");
            let femaleBtn = document.getElementById("femaleBtn");

            maleBtn.style.backgroundColor = "";
            maleBtn.style.borderColor = "";
            femaleBtn.style.backgroundColor = "";
            femaleBtn.style.borderColor = "";

            if (gender === "남") {
                maleBtn.style.backgroundColor = "rgba(146,19,19,1)";
                maleBtn.style.borderColor = "rgba(146,19,19,1)";
                maleBtn.style.color = "rgba(251, 251, 251, 1)";
            } else if (gender === "여") {
                femaleBtn.style.backgroundColor = "rgba(146,19,19,1)";
                femaleBtn.style.borderColor = "rgba(146,19,19,1)";
                femaleBtn.style.color = "rgba(251, 251, 251, 1)";
            }
        }

        let isIdChecked = false;
        document.getElementById("checkId").addEventListener("click", function () {
            let id = document.getElementById("id").value;
            let submitBtn = document.getElementById("submitBtn");

            fetch("../../../controllers/user/joinController.php", {
                method: "POST",
                body: new URLSearchParams({ action: "checkId", id: id })
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                    isIdChecked = true;
                    document.getElementById("submitBtn").disabled = false;
                }})
                .catch(error => console.error("오류 발생:", error));
        });


        document.getElementById("joinForm").addEventListener("submit", function (event) {
            event.preventDefault();

            let formData = new FormData(this);
            formData.append("action","join");
            fetch("../../../controllers/user/joinController.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else{
                    alert(data.message);
                }
            })
            .catch(error => console.log("Error:" + error));
        })

    </script>

</body>

</html>