const bookId = document.getElementById("bookId");
const bookTitle = document.getElementById("bookTitle");
const bookAuthor = document.getElementById("bookAuthor");
const bookAbout = document.getElementById("bookAbout");
const bookType = document.getElementById("bookType");
const bookGenre = document.getElementById("bookGenre");
const bookAvailable = document.getElementById("bookAvailable");
const bookTotal = document.getElementById("bookTotal");
const bookFormat = document.getElementById("bookFormat");

const requestBtn = document.querySelector(".search-book-sidebar-btn-request");
const waitListBtn = document.querySelector(".search-book-sidebar-btn-wait-list");

const selectedScheme = localStorage.getItem("scheme");
const root = document.getElementById("root");

if (selectedScheme === "light") {
    root.setAttribute("data-scheme", "light");
} else {
    root.setAttribute("data-scheme", "dark");
}

let selectedId = "";

function onClickBook(id, title, author, type, genre, description, available, total, formatType) {
    
    selectedId = id;
    
    if (available === "0" && formatType === "physical") {
        requestBtn.disabled = true;
        waitListBtn.disabled = false;
    } else {
        requestBtn.disabled = false;
        waitListBtn.disabled = false;
    }
    
    bookId.value = id;
    bookTitle.innerText = title;
    bookAuthor.innerText = author;
    bookAbout.innerText = description;
    bookType.innerText = type;
    bookGenre.innerText = genre;
    bookAvailable.innerText = available;
    bookTotal.innerText = total;
    bookFormat.innerText = formatType;

    showBookSideFormOnMobile();
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

function showBookSideFormOnMobile() {
    const sidebarForm = document.getElementById("searchBookSidebarForm");
    const sidebarFormVisibility = sidebarForm.getAttribute("data-visible");
    const formBackdrop = document.getElementById("searchBookSidebarFormBackdrop");

    if (sidebarFormVisibility === "false") {
        sidebarForm.setAttribute("data-visible", "true");
        formBackdrop.setAttribute("data-visible", "true");
    } else {
        sidebarForm.setAttribute("data-visible", "false");
        formBackdrop.setAttribute("data-visible", "false");
    }
}