let selectedBook = null;
let selectedBookAvailableOn = null;
const editBtn = document.querySelector(".crud-btn-edit");
const deleteBtn = document.querySelector(".crud-btn-delete");
const tableBody = document.getElementById("table-body");
const tableRows = document.getElementsByClassName("table-row");
const textArea = document.getElementById("edit-form-des");

function setInputValue(bookId, title, author, type, genre, copies, des, availableOn) {

    const availableOnEdit = document.getElementById("availableOnEdit");
    const uploadInputContainer = document.getElementById("uploadInputContainer");
    const available = availableOn === undefined || availableOn === "" ? "p" : availableOn;

    selectedBook = bookId;
    selectedBookAvailableOn = availableOn;

    document.getElementById("edit-form-title").value = title;
    document.getElementById("edit-form-author").value = author;
    document.getElementById("edit-form-type").value = type;
    document.getElementById("edit-form-genre").value = genre;
    document.getElementById("edit-form-copies").value = copies;
    textArea.value = des;
    document.getElementById("edit-form-bookId").value = bookId;

    if (available === "p") {
        uploadInputContainer.style.display = "none";
        availableOnEdit.value = "Physical";
    } else if (available === "pd") {
        uploadInputContainer.style.display = "block";
        availableOnEdit.value = "Physical / Digital";
    } else {
        uploadInputContainer.style.display = "block";
        availableOnEdit.value = "Digital";
    }
}

function activeBtn(isActive) {

    if (isActive) {
        editBtn.disabled = false;
        deleteBtn.disabled = false;
        editBtn.setAttribute("data-active", "true");
        deleteBtn.setAttribute("data-active", "true");
    } else {
        editBtn.disabled = true;
        deleteBtn.disabled = true;
        editBtn.setAttribute("data-active", "false");
        deleteBtn.setAttribute("data-active", "false");
    }
}

for (let i = 0; i < tableRows.length; i++) {
    tableRows[i].addEventListener("click", () => {
        let clicked = document.getElementsByClassName("active");

        if (tableRows[i].className === "table-row active" || clicked.length > 0) {
            clicked[0].className = clicked[0].className.replace(" active", "");
            setInputValue("", "", "", "", "", "", "");
            activeBtn(false)
        } else {
            tableRows[i].className += " active";
        }
    })
}

function onClickBook(bookId, title, author, type, genre, copies, des, availableOn) {

    activeBtn(true);

    setInputValue(bookId, title, author, type, genre, copies, des, availableOn);
};

function openAddModal(isOpen) {
    const modal = document.getElementById("add-modal");

    if (isOpen) {
        modal.className += " active-modal";
    } else {
        modal.className = modal.className.replace(" active-modal", "");
    }
};

function openEditModal(isOpen) {
    const modal = document.getElementById("edit-modal");

    if (isOpen) {
        modal.className += " active-modal";
    } else {
        modal.className = modal.className.replace(" active-modal", "");
    }
};

function openDeleteModal(isOpen) {
    const modal = document.getElementById("delete-modal");

    if (isOpen) {
        modal.className += " active-modal";
    } else {
        modal.className = modal.className.replace(" active-modal", "");
    }
};

function updateBook(event) {
    event.preventDefault();

    let formData = new FormData(document.getElementById("edit-form"));

    fetch("../utility/admin-update-book.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            alert(data);
            openEditModal(false)
            location.reload();
        })
        .catch(error => console.error(error));
};

function deleteBook(event) {
    event.preventDefault();

    let formData = new FormData();
    formData.append("bookId", selectedBook);
    formData.append("availableOn", selectedBookAvailableOn);
    fetch("../utility/admin-delete-book.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            alert(data);
            openDeleteModal(false)
            location.reload();
        })
        .catch(error => console.error(error));
}