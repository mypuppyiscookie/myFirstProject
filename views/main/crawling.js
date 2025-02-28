function crawling() {
  let fruit = document.getElementById("fruit").value.trim();
  let formData = new FormData();
  formData.append("action", "search");
  formData.append("fruit", fruit);

  if (fruit === "") {
    alert("과일 이름을 입력하세요!");
    return;
  }

  fetch("../../controllers/main/crawlingController.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `fruit=${encodeURIComponent(fruit)}`,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("크롤링 결과:", data);

      if (data.success) {
        document.getElementById("modalTitle").innerText = `${data.fruit}`;
        document.getElementById("modalMeaning").innerText = `${data.meaning}`;
        document.getElementById("modalImage1").src = data.image1;
        document.getElementById("modalImage2").src = data.image2;

        document.getElementById("crawlingModal").style.display = "flex";
      } else {
        alert("크롤링 실패: " + data.message);
      }
    })
    .catch((error) => console.error("크롤링 요청 실패:", error));
}

document.querySelector(".close").addEventListener("click", function () {
  document.getElementById("crawlingModal").style.display = "none";
});

document.querySelector(".close2").addEventListener("click", function () {
  document.getElementById("getModal").style.display = "none";
});

window.addEventListener("click", function (event) {
  if (event.target === document.getElementById("crawlingModal")) {
    document.getElementById("crawlingModal").style.display = "none";
  }
});

window.addEventListener("click", function (event) {
  if (event.target === document.getElementById("getModal")) {
    document.getElementById("getModal").style.display = "none";
  }
});

function saveFruit() {
  let modal = document.getElementById("crawlingModal");
  let fruit = document.getElementById("modalTitle").textContent.trim();
  let meaning = document.getElementById("modalMeaning").textContent.trim();
  let image1 = document.getElementById("modalImage1").src;
  let image2 = document.getElementById("modalImage2").src;

  if (!fruit || !meaning || !image1 || !image2) {
    alert("과일 정보를 찾을 수 없습니다.");
    return;
  }

  let formData = new FormData();
  formData.append("action", "save");
  formData.append("fruit", fruit);
  formData.append("meaning", meaning);
  formData.append("image1", image1);
  formData.append("image2", image2);

  fetch("../../controllers/main/crawlingController.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("저장 완료!");
      } else {
        alert("오류 발생: " + data.message);
      }
    })
    .catch((error) => {
      console.error("에러 발생:", error);
      alert("네트워크 오류 발생!");
    });
}

function getFruit() {
  let fruit = document.getElementById("fruitInput").value.trim();
  let formData = new FormData();
  formData.append("action", "get");
  formData.append("fruit", fruit);

  if (fruit === "") {
    alert("과일 이름을 입력하세요!");
    return;
  }

  fetch("../../controllers/main/crawlingController.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("서버 응답:", data);
      if (data.success) {
        document.getElementById("modalTitleGet").innerText = data.fruit;
        document.getElementById("modalMeaningGet").innerText = data.meaning;

        let decodedImage1 = decodeURIComponent(data.image1);
        let decodedImage2 = decodeURIComponent(data.image2);

        document.getElementById("modalImage1Get").src = decodedImage1;
        document.getElementById("modalImage2Get").src = decodedImage2;
        document.getElementById("getModal").style.display = "flex";
      } else {
        alert("데이터베이스에 존재하지 않는 과일입니다다");
      }
    })
    .catch((error) => {
      console.error("네트워크 오류:", error);
      alert("네트워크 오류 발생!");
    });
}
