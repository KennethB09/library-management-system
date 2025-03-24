const bookId = document.getElementById("bookId");
const bookTitle = document.getElementById("bookTitle");
const bookAuthor = document.getElementById("bookAuthor");
const bookAbout = document.getElementById("bookAbout");
const bookType = document.getElementById("bookType");
const bookGenre = document.getElementById("bookGenre");
const bookAvailable = document.getElementById("bookAvailable");
const bookTotal = document.getElementById("bookTotal");
const requestBtn = document.querySelector(".search-book-sidebar-btn-request");

function onClickBook(id, title, author, type, genre, description, available, total) {
    
    requestBtn.disabled = false;
    bookId.value = id;
    bookTitle.innerText = title;
    bookAuthor.innerText = author;
    bookAbout.innerText = description;
    bookType.innerText = type;
    bookGenre.innerText = genre;
    bookAvailable.innerText = available;
    bookTotal.innerText = total;

}