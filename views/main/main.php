<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>dkbmain</title>
    <link rel="stylesheet" href=" /css/main.css">
    <script src="userTable.js" defer></script>
    <script src="crawling.js" defer></script>
    <script src="editor.js" defer></script>
    <!-- <script src="sideBar.js" defer></script> -->
</head>

<body>
    <div class="container">
        <aside class="sideBar">
            <span type="button" class="icon">📂</span>
            <div class="title">데이터 베이스 목록</div>
            <div id="databaseList" class="databaseListBtn"></div>
            <!-- <div class="resizer"></div> -->

            <div class="crawlingZone">
                <div class="title">네이버에서 과일 찾기</div>
                <div class="line1">
                    <input id="fruit" class="inputCrawling" placeholder="과일 이름을 입력하세요">
                    <button id="crawlingBtn" onclick="crawling()" class="crawlingBtn">검색</button>
                </div>
            </div>

            <div class="getZone">
                <div class="title">데이터베이스에서 과일 찾기</div>
                <div class="line1">
                    <input id="fruitInput" class="inputCrawling" placeholder="과일 이름을 입력하세요">
                    <button id="crawlingBtn" onclick="getFruit()" class="crawlingBtn">검색</button>
                </div>
            </div>
        </aside>

        <main class="mainContent">
            <div class="line1">
                <div class="title2">SQL 쿼리</div>
                <button id="sqlQueryBtn" class="sqlQueryBtn">실행</button>
            </div>
            <textarea id="sqlQuery" class="sqlQuery"></textarea>

            <div class="title2">테이블 목록</div>
            <div id="tableList" class="tableList"></div>
            <div id="result" class="tableHeight"></div>
        </main>

        <button class="userBtn">
            <span class="userNameShort"></span>
        </button>
        <div class="userTable" id="userTable">
            <a href="myPage.php"><button>마이페이지</button></a><br>
            <button id="logoutBtn">로그아웃</button>
        </div>
    </div>

    <div id="crawlingModal" class="modal">
        <div class="modalContent">
            <span class="close">&times;</span>
            <h2 id="modalTitle"></h2>
            <p id="modalMeaning"></p>
            <div class="modalImages">
                <img id="modalImage1" src="" alt="이미지1">
                <img id="modalImage2" src="" alt="이미지2">
            </div>
            <button id="downloadImage" onclick="saveFruit()">저장하기</button>
        </div>
    </div>

    <div id="getModal" class="modal">
        <div class="modalContent">
            <span class="close2">&times;</span>
            <h2 id="modalTitleGet"></h2>
            <p id="modalMeaningGet"></p>
            <div class="modalImages">
                <img id="modalImage1Get" src="" alt="이미지1">
                <img id="modalImage2Get" src="" alt="이미지2">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadDatabases();
        });

        function loadDatabases() { //데이터베이스 목록 불러오기 
            fetch("../../controllers/main/mainController.php?action=getDatabases")
                .then(response => response.json())
                .then(data => {
                    console.log("서버 응답:", data);

                    if (data.success) {
                        let dbList = document.getElementById("databaseList");
                        if (!dbList) {
                            console.error("'databaseList' 요소를 찾을 수 없습니다.");
                            return;
                        }

                        dbList.innerHTML = "";

                        data.databases.forEach(db => {
                            let btn = document.createElement("button");
                            btn.innerText = db;
                            btn.classList.add("databaseListBtn");
                            btn.onclick = () => loadTables(db);
                            dbList.appendChild(btn);
                        });
                    } else {
                        console.error("서버 오류:", data.message || "데이터를 가져오지 못했습니다.");
                    }
                })
                .catch(error => console.error("네트워크 또는 JSON 변환 오류:", error));
        }

        document.addEventListener("DOMContentLoaded", function() { //sql쿼리 실행하기 
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
                            resultDiv.innerHTML = data.success ? `${data.message}` : `${data.message}`;
                        }
                    })
                    .catch(error => {
                        resultDiv.innerHTML = `서버 오류 발생`;
                    });
            });
        });

        function loadTables(database) { //데이터베이스 선택했을 때 테이블 목록 불러오기 
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

                        tableList.innerHTML = "";

                        data.tables.forEach(table => {
                            let tableBtn = document.createElement("button");
                            tableBtn.innerText = table;
                            tableBtn.classList.add("tableBtn");
                            tableBtn.onclick = () => loadTableData(database, table);
                            tableList.appendChild(tableBtn);
                        });
                    }
                })
                .catch(error => console.error("테이블 목록 불러오기 오류:", error));
        }

        function loadTableData(database, table) { //테이블의 데이터 불러오기 
            console.log(typeof addInputEvents);
            fetch(`../../controllers/main/mainController.php?action=getTableData&database=${database}&table=${table}`)
                .then(response => response.json())
                .then(data => {
                    console.log("서버 응답:", data);

                    let resultDiv = document.getElementById("result");

                    if (!resultDiv) {
                        console.error("'result' 요소를 찾을 수 없습니다.");
                        return;
                    }

                    if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                        let primaryKey = data.primaryKey || null;
                        resultDiv.innerHTML = generateTable(data.data, data.primaryKey, database, table);
                    } else {
                        resultDiv.innerHTML = "<p>데이터가 없습니다람쥐</p>";
                    }
                })
                .catch(error => console.error("테이블 데이터 불러오기 오류:", error));
        }

        function generateTable(data, primaryKey, database, table) { //테이블 정렬
            if (!data || data.length === 0) {
                return "<p>데이터가 없습니다</p>";
            }

            let tableHTML = `
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>`;

            // ✅ 테이블 헤더
            Object.keys(data[0]).forEach(col => {
                let primaryIcon = col === primaryKey ? " 🔑" : "";
                tableHTML += `<th>${primaryIcon}${col}</th>`;
            });

            tableHTML += `</tr></thead><tbody>`;

            // ✅ 테이블 데이터 생성
            data.forEach(row => {
                tableHTML += "<tr>";
                Object.keys(row).forEach(col => {
                    // tableHTML += `<td><input class="table-input" data-col="${col}" data-id="${JSON.stringify(row)}" value="${row[col] || ''}"></td>`;
                    tableHTML += `<td><input id="input-${row[primaryKey]}" class="table-input" data-col="${col}" data-id="${row[primaryKey]}" value="${row[col] || ''}"></td>`;

                });
                tableHTML += "</tr>";
            });

            tableHTML += "</tbody></table></div>";

            // ✅ 버튼 그룹 추가
            tableHTML += `
    <div class="tableButtons">
        <button class="refreshDataBtn" onclick="refreshTable('${database}', '${table}')">새로고침</button>
        <button class="saveDataBtn" onclick="handleSaveButtonClick('${database}', '${table}', '${primaryKey}')" disabled>저장</button>
        <button class="cancelDataBtn" onclick="cancelRow('${database}', '${table}')" disabled>취소</button>
        <button class="addDataBtn" onclick="addRow('${database}', '${table}')">로우 추가</button>
        <button class="deleteDataBtn" onclick="deleteRow('${database}', '${table}', '${primaryKey}')">로우 삭제</button>
    </div>`;

            //테이블이 DOM에 추가된 후 이벤트 등록
            setTimeout(addInputEvents, 100);
            setTimeout(storeOriginalData, 100);

            return tableHTML;
        }
    </script>
</body>

</html>