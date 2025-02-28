//새로고침 
function refreshTable(database, table) {
    loadTableData(database, table);
}

//원본 데이터 저장 객체
let originalData = {};
//변경된 데이터 저장
let modifiedData = {}; 
//추가된 데이터 저장
let newRowData = {};
//선택된 데이터 저장
let selectedData = {};

//원본 데이터를 저장하는 함수
function storeOriginalData() {
    let inputs = document.querySelectorAll(".table-input");

    originalData = {}; // 기존 데이터가 남아있는 것을 방지하기 위해 초기화!!
    modifiedData = {};

    inputs.forEach(input => {
        let rowId = input.dataset.id;
        let col = input.dataset.col;

        if (!originalData[rowId]) originalData[rowId] = {};
        originalData[rowId][col] = input.value.trim();
    });
}

//input 요소에 이벤트를 추가하는 함수
function addInputEvents() {
    let inputs = document.querySelectorAll(".table-input");

    inputs.forEach(input => {
        input.addEventListener("focus", function () { //클릭 시 배경색 변경
            this.style.backgroundColor = "rgba(146, 19, 19, 1)";
            this.style.color = "rgba(251, 251, 251, 1)";

            let rowId = this.dataset.id;
            let col = this.dataset.col;
            let value = this.value.trim();

            // if (!selectedData[rowId]) selectedData[rowId] = {};
            // selectedData[rowId][col] = value;
            // 새로운 데이터 선택 시 기존 데이터 초기화 (항상 한 개만 유지)
            selectedData = {}; 
            selectedData[rowId] = {}; 
            selectedData[rowId][col] = value;

            console.log("선택된 데이터:", selectedData); //디버깅용 로그
        });

        input.addEventListener("blur", function () { //클릭을 해제했을 때 값이 변하지 않았다면 색상 복구
            let rowId = this.dataset.id;
            let col = this.dataset.col;
            let currentValue = this.value.trim();
            let originalValue = originalData[rowId]?.[col] || "";

            if (currentValue === originalValue) { 
                this.style.backgroundColor = "white";
                this.style.color = "black";
            }
        });

        input.addEventListener("input", function () { //값이 변경되었다면 checkChange() 함수 불러오기기
            checkChanges(this); // 변경된 input만 검사하도록
        });
    });
}

//변경 사항 감지 및 버튼 활성화
function checkChanges() {
    let inputs = document.querySelectorAll(".table-input");
    let isChanged = false;

    inputs.forEach(input => {
        let rowId = input.dataset.id;
        let col = input.dataset.col;
        let currentValue = input.value.trim();
        let originalValue = originalData[rowId]?.[col] || "";

        if (currentValue !== originalValue) {
            isChanged = true;
            input.style.backgroundColor = "rgba(146, 19, 19, 1)"; // 변경 감지된 input만 색상 유지
            input.style.color = "rgba(251, 251, 251, 1)";

            if (!modifiedData[rowId]) modifiedData[rowId] = {};
            modifiedData[rowId][col] = currentValue;
            input.classList.add("edited-row"); //값이 수정된 경우 edited-row 클래스 추가 
        } else {
            //값이 원래대로 돌아오면 제거
            if (modifiedData[rowId]) {
                delete modifiedData[rowId][col];
                if (Object.keys(modifiedData[rowId]).length === 0) {
                    delete modifiedData[rowId];
                }
                input.classList.remove("edited-row"); //값이 원래대로 돌아오면 클래스 제거 
            }
        }
    });

    console.log("변경된 값",modifiedData);

    let saveDataBtn = document.querySelector(".saveDataBtn");
    let cancelDataBtn = document.querySelector(".cancelDataBtn");

    if (saveDataBtn && cancelDataBtn) {
        saveDataBtn.disabled = !isChanged;
        cancelDataBtn.disabled = !isChanged;
    }
}

async function saveUpdatedRow(database, table, primaryKey) {
    if (!database || !table || !primaryKey) {
        console.error("필수 데이터가 누락되었습니다람쥐");
        return;
    }

    if (Object.keys(modifiedData).length === 0) {
        alert("변경된 데이터가 없습니다.");
        return;
    }

    let updates = [];

    // 여러 개의 수정된 행을 업데이트 리스트로 변환
    Object.entries(modifiedData).forEach(([rowId, changes]) => {
        updates.push({
            primaryKeyData: rowId,  // 각 행의 기본키 값
            changes: changes        // 변경된 데이터
        });
    });

    let updatePayload = { database, table, primaryKey, updates };

    try {
        let response = await fetch(`../../controllers/main/editorController.php?action=updateRow`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(updatePayload) //여러 개의 변경된 데이터 전송
        });

        let data = await response.json(); //response.json()을 await으로 직접 처리

        if (data.success) { 
            alert("수정되었습니다!");
            modifiedData = {}; //저장 후 변경 데이터 초기화
            checkChanges(); //버튼 상태 초기화
        } else {
            throw new Error("수정 실패: " + data.message);
        }
    } catch (error) {
        console.error("서버 요청 오류:", error);
        alert("네트워크 오류 발생!");
    }
}

function cancelRow(database, table) {
    let inputs = document.querySelectorAll(".table-input");

    inputs.forEach(input => {
        input.value = input.defaultValue; // 원래 값으로 되돌리기
    });

    checkChanges(); // 버튼 상태 다시 확인
}

function addRow() {
    let tableBody = document.querySelector("table tbody");
    if (!tableBody) {
        console.error("테이블을 찾을 수 없습니다.");
        return;
    }

    //새로운 행 추가하기
    let newRow = document.createElement("tr");
    let columns = document.querySelectorAll("table thead th"); // 테이블의 컬럼 가져오기
    let rowId = "new_" + Date.now(); // 고유한 임시 ID 생성

    newRow.setAttribute("data-id", rowId); //새 행에 ID 부여

    columns.forEach((col) => {
        let colName = col.textContent.replace(" 🔑", "").trim(); //🔑 아이콘 제거

        //`AUTO_INCREMENT` 또는 `createdDate` 같은 필드는 입력 방지
        if (colName.toLowerCase() === "id" || colName.toLowerCase() === "createddate") {
            newRow.innerHTML += `<td>-</td>`; // 사용자 입력 방지 (자동 생성됨)
        } else {
            let inputField = `<input class="table-input new-row" data-col="${colName}" data-id="${rowId}" value="">`; //new-row 클래스 추가 
            newRow.innerHTML += `<td>${inputField}</td>`;
        }
    });

    tableBody.appendChild(newRow); //테이블에 행 추가
    addInputEvents(); //새로운 인풋 이벤트 바인딩

    //저장, 취소 버튼 활성화화
    document.querySelector(".saveDataBtn").disabled = false;
    document.querySelector(".cancelDataBtn").disabled = false;
}

async function saveNewRow(database, table) {
    if (!database || !table) {
        console.error("필수 데이터가 누락되었습니다.");
        return;
    }

    let newRows = document.querySelectorAll(".table-input.new-row"); //추가된 행 찾기
    if (newRows.length === 0) { 
        alert("추가된 데이터가 없습니다.");
        return;
    }

    let tableColumns = Array.from(document.querySelectorAll("table thead th")).map(th => 
        th.textContent.replace(" 🔑", "").trim()
    ); //테이블 컬럼명 가져오기 (🔑 아이콘 제거)

    let newRowData = {}; //새로운 행 데이터를 저장할 객체

    newRows.forEach(input => { //각 입력 필드를 순회하며 데이터 저장
        let rowId = input.dataset.id; 
        let col = input.dataset.col;
        let value = (typeof input.value === "string" && input.value.trim() !== "") ? input.value.trim() : null;

        if (!newRowData[rowId]) {
            newRowData[rowId] = {}; //해당 행 데이터가 없으면 초기화

            //입력되지 않은 컬럼은 null로 초기화
            tableColumns.forEach(column => {
                if (column.toLowerCase() !== "id" && column.toLowerCase() !== "createddate") {
                    newRowData[rowId][column] = null; 
                }
            });
        }

        newRowData[rowId][col] = value; //입력된 값 저장
    });

    let insertPayload = { database, table, newRows: Object.values(newRowData) }; //전송할 데이터 객체

    try {
        let response = await fetch(`../../controllers/main/editorController.php?action=addRow`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(insertPayload) //JSON 데이터로 변환하여 전송
        });

        let data = await response.json(); //응답을 JSON으로 변환

        if (!data.success) {
            throw new Error("추가 실패: " + data.message);
        }

        alert("새로운 행이 추가되었습니다!");
        refreshTable(database, table); //테이블 새로고침

    } catch (error) {
        console.error("추가 요청 오류:", error);
        alert("네트워크 오류 발생!");
    }
}

async function handleSaveButtonClick(database, table, primaryKey) {
    let hasNewRow = document.querySelector(".new-row") !== null;
    let hasEditedRow = document.querySelector(".edited-row") !== null;

    if (!hasNewRow && !hasEditedRow) {
        alert("변경 사항이 없습니다!");
        return;
    }

    let saveButton = document.querySelector(".saveDataBtn");
    saveButton.disabled = true; //저장 중 버튼 비활성화 (중복 저장 방지)

    try {
        if (hasEditedRow) {
            await saveUpdatedRow(database, table, primaryKey);
        }

        if (hasNewRow) {
            await saveNewRow(database, table);
        }

        alert("변경 사항이 저장되었습니다!");
        refreshTable(database, table); //테이블 새로고침
    } catch (error) {
        console.error("저장 중 오류 발생:", error);
        alert("저장 중 오류가 발생했습니다! " + error.message);
    } finally {
        saveButton.disabled = false; //저장 완료 후 버튼 다시 활성화
    }
}

function deleteRow(database, table, primaryKey) {
    let rowIds = Object.keys(selectedData); //선택된 행의 ID 목록

    if (rowIds.length === 0) {
        alert("삭제할 데이터를 선택하세요.");
        return;
    }

    if (!confirm("정말 삭제하시겠습니까?")) return;

    fetch(`../../controllers/main/editorController.php?action=deleteRow`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            database,
            table,
            primaryKey,
            primaryKeyData: rowIds //선택된 행의 ID들
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            refreshTable(database, table); //테이블 새로고침
            selectedData = {}; //선택된 데이터 초기화
        } else {
            alert("삭제 실패: " + data.message);
        }
    })
    .catch(error => {
        console.error("서버 요청 오류:", error);
        alert("네트워크 오류 발생!");
    });
}



