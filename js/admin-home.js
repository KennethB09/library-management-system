
function onToggleAdminInfo() {
    const adminInfoMain = document.querySelector(".admin-info-main-container");
    const visibility = adminInfoMain.getAttribute("data-visible");
 
    if (visibility !== "true") {
        adminInfoMain.setAttribute("data-visible", "true");
    } else {
        adminInfoMain.setAttribute("data-visible", "false");
    }
}

function onToggleLogout() {
    const logoutMain = document.querySelector(".admin-logout-modal-container");
    const visibility = logoutMain.getAttribute("data-visible");

    if (visibility!== "true") {
        logoutMain.setAttribute("data-visible", "true");
    } else {
        logoutMain.setAttribute("data-visible", "false");
    }
}