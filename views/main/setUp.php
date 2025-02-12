<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>dkbsetup</title>
    <link rel="stylesheet" href=" /css/main.css">
</head>

<body>
    <div class="setUpPage">
        <div class="setUpBar">
            <button id="basicInfoBtn" onclick="setUpBar('basicInfo')">기본 정보</button>
            <button id="changePwdBtn" onclick="setUpBar('changePwd')">비밀번호 변경</button>
            <button id="cancelBtn" onclick="setUpBar('cancel')">회원 탈퇴</button>
        </div>

        <form id="basicInfo" class="active">
            <h2>기본 정보</h2>
            <input type="text" id="id" disabled>
            <input type="text" id="name" disabled>
            <input type="email" id="email" disabled>
            <input type="text" id="birthday" disabled>
            <input type="text" id="joinDate" disabled>
            <button type="button" id="editBtn" onclick="toggleEdit()">정보 수정</button>
            <button type="button" id="saveBtn" onclick="updateUser()" style="display: none;">저장</button>
        </form>

        <form id="changePwd" class="active">
            <h2>비밀번호 변경</h2>
            <input type="password" id="nowPwd" placeholder="현재 비밀번호">
            <input type="password" id="futurePwd" placeholder="변경 비밀번호">
            <button type="button" onclick="changePwd()">비밀번호 변경</button>
        </form>

        <form id="cancel" class="active">
            <h2>회원 탈퇴</h2>
            <p>탈퇴 후 복구가 불가능합니다</p>
            <p>정말로 탈퇴하시겠습니까?</p>
            <input type="password" id="PwdForCancel" placeholder="비밀번호">
            <button type="button" onclick="deleteAccount()">탈퇴하기</button>
        </form>
    </div>

    <script>
        window.onload = function() {
            setUpBar('basicInfo');
        }

        function setUpBar(type) {
            // document.getElementById("basicInfoBtn").classList.remove("active");
            // document.getElementById("changePwdBtn").classList.remove("active");
            // document.getElementById("cancelBtn").classList.remove("active");
            document.getElementById("basicInfo").classList.remove("active");
            document.getElementById("changePwd").classList.remove("active");
            document.getElementById("cancel").classList.remove("active");

            if (type === 'basicInfo') {
                document.getElementById("changePwd").classList.add("active");
                document.getElementById("cancel").classList.add("active");

                getUserInfo();

            } else if (type === 'changePwd') {
                document.getElementById("basicInfo").classList.add("active");
                document.getElementById("cancel").classList.add("active");
            } else if (type === 'cancel') {
                document.getElementById("basicInfo").classList.add("active");
                document.getElementById("changePwd").classList.add("active");
            }
        }


        function getUserInfo() {
            fetch("../../controllers/main/setUpController.php?action=getUserInfo")
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        document.getElementById("id").value = data.data.userId
                        document.getElementById("name").value = data.data.userName;
                        document.getElementById("email").value = data.data.email;
                        document.getElementById("birthday").value = data.data.birthday;
                        document.getElementById("joinDate").value = data.data.joinDate;
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error("오류 발생:", error));
        }

        function toggleEdit() {
            let inputs = document.querySelectorAll("#name, #email, #birthday");
            let editBtn = document.getElementById("editBtn");
            let saveBtn = document.getElementById("saveBtn");

            inputs.forEach(input => input.disabled = false);

            // 버튼 변경
            editBtn.style.display = "none"; // "정보 수정" 버튼 숨김
            saveBtn.style.display = "block"; // "저장" 버튼 표시
        }

        function updateUser() {

            let formData = new FormData();
            formData.append("action", "updateUser");
            formData.append("id", document.getElementById("id").value);
            formData.append("name", document.getElementById("name").value);
            formData.append("email", document.getElementById("email").value);
            formData.append("birthday", document.getElementById("birthday").value);
            formData.append("joinDate", document.getElementById("joinDate").value);

            fetch("../../controllers/main/setUpController.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => alert(data.message))
                .catch(error => console.error("오류 발생:", error));
        }

        // ✅ 비밀번호 변경
        function changePwd() {
            let formData = new FormData();
            formData.append("action", "changePassword");
            formData.append("current_password", document.getElementById("nowPwd").value);
            formData.append("new_password", document.getElementById("futurePwd").value);

            fetch("../../controllers/main/setUpController.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => alert(data.message))
                .catch(error => console.error("오류 발생:", error));
        }

        // ✅ 회원 탈퇴
        function deleteAccount() {
            if (!confirm("정말로 탈퇴하시겠습니까?")) return;

            let formData = new FormData();
            formData.append("action", "deleteAccount");
            formData.append("password", document.getElementById("PwdForCancel").value);

            fetch("../../controllers/main/setUpController.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) window.location.href = "/"; // 탈퇴 후 홈으로 이동
                })
                .catch(error => console.error("오류 발생:", error));
        }
    </script>
</body>

</html>