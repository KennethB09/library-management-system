const bookId = document.getElementById("bookId");
const bookTitle = document.getElementById("bookTitle");
const bookAuthor = document.getElementById("bookAuthor");
const bookAbout = document.getElementById("bookAbout");
const bookType = document.getElementById("bookType");
const bookGenre = document.getElementById("bookGenre");
const bookAvailable = document.getElementById("bookAvailable");
const bookTotal = document.getElementById("bookTotal");
const format = document.getElementById("format");

const requestBtn = document.querySelector(".search-book-sidebar-btn-request");
const waitListBtn = document.querySelector(".search-book-sidebar-btn-wait-list");

let selectedId = "";

function onClickBook(id, title, author, type, genre, description, available, total, format) {
    
    selectedId = id

    if (available === "0" && format === "physical") {
        requestBtn.disabled = true;
        waitListBtn.disabled = false;
    } else {
        requestBtn.disabled = false;
        waitListBtn.disabled = false;
        
        if (format === "physical") {
            format.innerText = "Physical";
        } else if (format === "both") {
            format.innerText = "Physical / Digital";
        } else {
            format.innerText = "Digital";
        }
    }


    bookId.value = id;
    bookTitle.innerText = title;
    bookAuthor.innerText = author;
    bookAbout.innerText = description;
    bookType.innerText = type;
    bookGenre.innerText = genre;
    bookAvailable.innerText = available;
    bookTotal.innerText = total;
}

function waitList(event, userId) {
    event.preventDefault();
    console.log("working")
    let formData = new FormData();
    formData.append("bookId", selectedId);
    formData.append("userId", userId);
    fetch("../utility/waitList.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload();
    })
    .catch(error => console.error(error));
}