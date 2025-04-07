const borrowTable = document.getElementById("borrowTable");
const requestTable = document.getElementById("requestTable");

function switchTable(table) {
    const borrowTableTitle = document.getElementsByClassName("borrow-table-title");
    const requestTableTitle = document.getElementsByClassName("request-table-title");

    if(table === "request") {
        requestTable.setAttribute("data-visible", "true");
        borrowTable.setAttribute("data-visible", "false");
        borrowTableTitle[0].classList.remove("title-clicked");
        requestTableTitle[0].classList.add("title-clicked");
    } else {
        borrowTable.setAttribute("data-visible", "true");
        requestTable.setAttribute("data-visible", "false");
        requestTableTitle[0].classList.remove("title-clicked");
        borrowTableTitle[0].classList.add("title-clicked");
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

