function switchTable(table) {
    const borrowTable = document.getElementById("borrowTable");
    const requestTable = document.getElementById("requestTable");
    const waitListTable = document.getElementById("waitListTable");

    const borrowTableTitle = document.getElementsByClassName("borrow-table-title");
    const requestTableTitle = document.getElementsByClassName("request-table-title");
    const waitListTableTitle = document.getElementsByClassName("waitList-table-title");

    if(table === "request") {
        requestTable.setAttribute("data-visible", "true");
        borrowTable.setAttribute("data-visible", "false");
        waitListTable.setAttribute("data-visible", "false");
        requestTableTitle[0].classList.add("title-clicked");
        borrowTableTitle[0].classList.remove("title-clicked");
        waitListTableTitle[0].classList.remove("title-clicked");
    } else if (table === "borrow") {
        borrowTable.setAttribute("data-visible", "true");
        requestTable.setAttribute("data-visible", "false");
        waitListTable.setAttribute("data-visible", "false");
        borrowTableTitle[0].classList.add("title-clicked");
        requestTableTitle[0].classList.remove("title-clicked");
        waitListTableTitle[0].classList.remove("title-clicked");
    } else {
        waitListTable.setAttribute("data-visible", "true");
        borrowTable.setAttribute("data-visible", "false");
        requestTable.setAttribute("data-visible", "false");
        waitListTableTitle[0].classList.add("title-clicked");
        borrowTableTitle[0].classList.remove("title-clicked");
        requestTableTitle[0].classList.remove("title-clicked");
    }
};

function clickProfile() {
    const modal = document.querySelector(".user-profile-modal-container");
    const modalVisible = modal.getAttribute("data-visible");

    if (modalVisible === "true") {
        modal.setAttribute("data-visible", "false");
    } else {
        modal.setAttribute("data-visible", "true");
    }
}

function editProfile() {
    const containerInfo = document.querySelector(".user-profile-modal-info-container");
    const containerForm = document.querySelector(".user-profile-modal-form-container");
    const infoAttr = containerInfo.getAttribute("data-visible");

    if (infoAttr === "true") {
        containerInfo.setAttribute("data-visible", "false");
        containerForm.setAttribute("data-visible", "true");
    } else {
        containerInfo.setAttribute("data-visible", "true");
        containerForm.setAttribute("data-visible", "false");
    }
}

function toggleMenu() {
    const menu = document.querySelector(".user-profile-dialogue");
    const menuAttr = menu.getAttribute("data-visible");

    if (menuAttr === "true") {
        menu.setAttribute("data-visible", "false");
    } else {
        menu.setAttribute("data-visible", "true");
    }
}

function waitListRemove(event, id) {
    event.preventDefault();

    let formData = new FormData();
    formData.append("id", id);
    fetch("../controllers/wait-list-remove.php", {
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