const sidebar = document.querySelector(".sideBar");
const resizer = document.querySelector(".resizer");
const title = document.querySelector(".title");
const databaseList = document.querySelector("#databaseList");
const icon = document.querySelector(".icon");

let isResizing = false;

resizer.addEventListener("mousedown", (event) => {
  isResizing = true;
  document.addEventListener("mousemove", resizeSidebar);
  document.addEventListener("mouseup", stopResizing);
  event.preventDefault(); // ✅ 드래그 시 불필요한 선택 방지
});

function resizeSidebar(event) {
  if (isResizing) {
    let newWidth = event.clientX;

    if (newWidth > 1000) {
      newWidth = 1000;
    } else if (newWidth < 120) {
      newWidth = 50; // 120px 이하로 내려가면 자동으로 50px로 변함
    }

    sideBar.style.width = `${newWidth}px`;
    mainContent.style.width = `calc(100% - ${newWidth}px)`;

    // 너비가 50px이면 텍스트 숨기고 아이콘 표시
    if (newWidth === 50) {
      title.style.opacity = "0";
      databaseList.style.opacity = "0";
      icon.style.display = "block";
    } else {
      title.style.opacity = "1";
      databaseList.style.opacity = "1";
      icon.style.display = "none";
    }
  }
}

function stopResizing() {
  isResizing = false;
  document.removeEventListener("mousemove", resizeSidebar);
  document.removeEventListener("mouseup", stopResizing);
}
