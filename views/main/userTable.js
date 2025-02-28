document.querySelector(".userBtn").addEventListener("click", function() {
    let userTable = document.getElementById("userTable");
    if (userTable.style.display === "none" || userTable.style.display === "") {
        userTable.style.display = "block";
    } else {
        userTable.style.display = "none";
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById("logoutBtn");
    const userBtn = document.querySelector(".userBtn");

    fetch("../../getUserInfo.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const nameFirstTwo = data.userName.substring(0, 2);
                userBtn.innerHTML = `<span class="userNameShort">${nameFirstTwo}</span>`;
            }
        })
        .catch(error => console.error("유저 정보 불러오기 오류:", error));

    if (logoutBtn) {
        logoutBtn.addEventListener("click", function () {
            fetch("../controllers/AuthController.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("로그아웃 되었습니다!");
                    window.location.href = "../views/login.php"; 
                }
            })
            .catch(error => console.error("로그아웃 요청 오류:", error));
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const logoutBtn = document.getElementById("logoutBtn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", function() {
            fetch("../../controllers/user/logoutController.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "../../index.php";
                    } else {
                        alert("로그아웃 실패: " + data.message);
                    }
                })
                .catch(error => console.error("로그아웃 요청 오류:", error));
        });
    }
});