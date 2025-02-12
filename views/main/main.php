<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>dkbmain</title>
    <link rel="stylesheet" href=" /css/main.css">
    <script src="userTable.js" defer></script>
    <!-- <script src="sideBar.js" defer></script> -->
</head>

<body>
    <div class="sideBar">
        <span class="icon">📂</span>
        <div class="title">데이터 베이스 목록</div>
        <div id="databaseList" class="databaseListBtn"></div>

        <!-- <div class="resizer"></div> -->
    </div>

    <div class="mainContent">
        <div class="line1">
            <div class="title2">SQL 쿼리</div>
            <button id="sqlQueryBtn" class="sqlQueryBtn">실행</button>
        </div>
        <textarea id="sqlQuery" class="sqlQuery"></textarea>

        <div class="title2">테이블 목록</div>
        <div id="tableList" class="tableList"></div>
        <div id="result"></div>
    </div>

    <button class="userBtn">
        <span class="userNameShort"></span>
    </button>
    <div class="userTable" id="userTable">
        <a href="setUp.php"><button>마이페이지</button></a><br>
        <button id="logoutBtn">로그아웃</button>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadDatabases();
        });

        function loadDatabases() {
            fetch("../../controllers/main/mainController.php?action=getDatabases")
                .then(response => response.json())
                .then(data => {
                    console.log("서버 응답:", data); // ✅ 응답 확인

                    if (data.success) {
                        let dbList = document.getElementById("databaseList");
                        if (!dbList) {
                            console.error("❌ 'databaseList' 요소를 찾을 수 없습니다.");
                            return;
                        }

                        dbList.innerHTML = ""; // 기존 목록 초기화

                        data.databases.forEach(db => {
                            let btn = document.createElement("button");
                            btn.innerText = db;
                            btn.classList.add("databaseListBtn");
                            btn.onclick = () => loadTables(db); //클릭하면 테이블 목록 불러오기
                            dbList.appendChild(btn);
                        });
                    } else {
                        console.error("❌ 서버 오류:", data.message || "데이터를 가져오지 못했습니다.");
                    }
                })
                .catch(error => console.error("❌ 네트워크 또는 JSON 변환 오류:", error));
        }

        document.addEventListener("DOMContentLoaded", function() {
            const sqlQueryBtn = document.getElementById("sqlQueryBtn");
            const sqlQuery = document.getElementById("sqlQuery");
            const resultDiv = document.getElementById("result");

            sqlQueryBtn.addEventListener("click", function() {
                const query = sqlQuery.value.trim();

                if (!query) {
                    resultDiv.innerHTML = "SQL 쿼리를 입력하세요";
                    return;
                }

                fetch("../../controllers/main/queryController.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            query: query
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {

                            resultDiv.innerHTML = generateTable(data);
                        } else {
                            resultDiv.innerHTML = data.success ? `✅ ${data.message}` : `❌ ${data.message}`;
                        }
                    })
                    .catch(error => {
                        resultDiv.innerHTML = `서버 오류 발생`;
                    });
            });
        });

        function generateTable(data) {
            let table = "<table border='1' style='border-collapse: collapse; width: 100%; text-align: left;'>";
            table += "<tr>";

            // 테이블 헤더 생성 (컬럼명 가져오기)
            Object.keys(data[0]).forEach(col => {
                table += `<th style='padding: 8px; background-color: #f2f2f2;'>${col}</th>`;
            });
            table += "</tr>";

            // 테이블 데이터 생성
            data.forEach(row => {
                table += "<tr>";
                Object.values(row).forEach(value => {
                    table += `<td style='padding: 8px;'>${value}</td>`;
                });
                table += "</tr>";
            });

            table += "</table>";
            return table;
        }


        function loadTables(database) {
            fetch(`../../controllers/main/mainController.php?action=getTables&database=${database}`)
                .then(response => response.json())
                .then(data => {
                    console.log(`'${database}'의 테이블 목록:`, data);

                    if (data.success) {
                        let tableList = document.getElementById("tableList");

                        if (!tableList) {
                            console.error("'tableList' 요소를 찾을 수 없습니다.");
                            return;
                        }

                        tableList.innerHTML = ""; // ✅ 기존 목록 초기화

                        data.tables.forEach(table => {
                            let tableBtn = document.createElement("button");
                            tableBtn.innerText = table;
                            tableBtn.classList.add("tableBtn");
                            tableBtn.onclick = () => loadTableData(database, table); // ✅ 클릭하면 테이블 데이터 불러오기
                            tableList.appendChild(tableBtn);
                        });
                    }
                })
                .catch(error => console.error("테이블 목록 불러오기 오류:", error));
        }

        function loadTableData(database, table) {
            fetch(`../../controllers/main/mainController.php?action=getTableData&database=${database}&table=${table}`)
                .then(response => response.json())
                .then(data => {
                    console.log(`'${table}' 테이블 데이터:`, data);

                    let resultDiv = document.getElementById("result");

                    if (!resultDiv) {
                        console.error("'result' 요소를 찾을 수 없습니다.");
                        return;
                    }

                    if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                        resultDiv.innerHTML = generateTable(data.data);
                    } else {
                        resultDiv.innerHTML = "<p>데이터가 없습니다</p>";
                    }
                })
                .catch(error => console.error("테이블 데이터 불러오기 오류:", error));
        }
    </script>




</body>

</html>