<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>dkbsetup</title>
    <link rel="stylesheet" href=" /css/main.css">
</head>

<body>
    <div class="setUpPage">
        <div class="logo">마이 페이지</div>
        <div class="setUpBar">
            <button id="basicInfoBtn" class="setUpBtn" onclick="setUpBar('basicInfo')" >회원 정보</button>
            <button id="changePwdBtn" class="setUpBtn" onclick="setUpBar('changePwd')">비밀번호 변경</button>
            <button id="cancelBtn" class="setUpBtn" onclick="setUpBar('cancel')">회원 탈퇴</button>
        </div>

        <form id="basicInfo" class="findCont active">
            <input type="text" id="id" class="inputBig" disabled>
            <input type="text" id="name" class="inputBig" disabled>
            <input type="email" id="email" class="inputBig" disabled>
            <input type="text" id="birthday" class="inputBig" disabled>
            <input type="text" id="joinDate" class="inputBig" disabled>
            <button type="button" id="editBtn" onclick="toggleEdit()" class="findBtn">수정</button>
            <button type="button" id="saveBtn" onclick="updateUser()" class="findBtn" style="display: none;">저장</button>
        </form>

        <form id="changePwd" class="findCont active">
            <input type="password" id="nowPwd" class="inputBig" placeholder="현재 비밀번호">
            <input type="password" id="futurePwd" class="inputBig" placeholder="변경 비밀번호">
            <button type="button" onclick="changePwd()" class="findBtn">비밀번호 변경</button>
        </form>

        <form id="cancel" class="findCont active" style="text-align: center;">
            <span>탈퇴 후 복구가 불가능합니다<br>정말로 탈퇴하시겠습니까?</span>
            <input type="password" id="PwdForCancel" class="inputBig" placeholder="비밀번호">
            <button type="button" onclick="deleteAccount()" class="findBtn">탈퇴하기</button>
        </form>
    </div>

    <script>
        window.onload = function() {
            setUpBar('basicInfo');
        }

        function setUpBar(type) {
            document.getElementById("basicInfo").classList.remove("active");
            document.getElementById("changePwd").classList.remove("active");
            document.getElementById("cancel").classList.remove("active");

            if (type === 'basicInfo') {
                getUserInfo();
                document.getElementById("changePwd").classList.add("active");
                document.getElementById("cancel").classList.add("active");
                document.getElementById("basicInfoBtn").style.borderColor = "rgba(146,19,19,1) rgba(146,19,19,1) rgba(251, 251, 251, 1) rgba(146,19,19,1)";
                document.getElementById("changePwdBtn").style.borderColor = "";
                document.getElementById("cancelBtn").style.borderColor = "";
            } else if (type === 'changePwd') {
                document.getElementById("basicInfo").classList.add("active");
                document.getElementById("cancel").classList.add("active");
                document.getElementById("changePwdBtn").style.borderColor = "rgba(146,19,19,1) rgba(146,19,19,1) rgba(251, 251, 251, 1) rgba(146,19,19,1)";
                document.getElementById("basicInfoBtn").style.borderColor = "";
                document.getElementById("cancelBtn").style.borderColor = "";
            } else if (type === 'cancel') {
                document.getElementById("basicInfo").classList.add("active");
                document.getElementById("changePwd").classList.add("active");
                document.getElementById("cancelBtn").style.borderColor = "rgba(146,19,19,1) rgba(146,19,19,1) rgba(251, 251, 251, 1) rgba(146,19,19,1)";
                document.getElementById("basicInfoBtn").style.borderColor = "";
                document.getElementById("changePwdBtn").style.borderColor = "";
            }
        }


        function getUserInfo() {
            fetch("../../controllers/main/myPageController.php?action=getUserInfo")
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

            editBtn.style.display = "none"; 
            saveBtn.style.display = "block"; 
        }

        function updateUser() {

            let formData = new FormData();
            formData.append("action", "updateUser");
            formData.append("id", document.getElementById("id").value);
            formData.append("name", document.getElementById("name").value);
            formData.append("email", document.getElementById("email").value);
            formData.append("birthday", document.getElementById("birthday").value);
            formData.append("joinDate", document.getElementById("joinDate").value);

            fetch("../../controllers/main/myPageController.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message)
                    if(data.success) location.reload();
                })
                .catch(error => console.error("오류 발생:", error));
        }

        function changePwd() {
            let formData = new FormData();
            formData.append("action", "changePassword");
            formData.append("currentPassword", document.getElementById("nowPwd").value);
            formData.append("newPassword", document.getElementById("futurePwd").value);

            fetch("../../controllers/main/myPageController.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message)
                    if(data.success) location.reload();
                })
                .catch(error => console.error("오류 발생:", error));
        }

        function deleteAccount() {
            if (!confirm("정말로 탈퇴하시겠습니까?")) return;

            let formData = new FormData();
            formData.append("action", "deleteAccount");
            formData.append("deletePassword", document.getElementById("PwdForCancel").value);

            fetch("../../controllers/main/myPageController.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) window.location.href = "../../index.php"; 
                })
                .catch(error => console.error("오류 발생:", error));
        }
    </script>
</body>

</html>