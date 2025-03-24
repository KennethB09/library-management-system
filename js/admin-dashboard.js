
// Load active tab from local storage

const currentTab = localStorage.getItem("currentTab");
const activeTab = document.getElementById("defaultTab");

if (currentTab) {
    document.getElementById(currentTab).click();
} else {
    localStorage.setItem("currentTab", activeTab);
    document.getElementById("defaultTab").click();
}

function changeTab(evt, tabName) {
    let i = 0;
    const tabContent = document.getElementsByClassName("tab-content");
    const tabLinks = document.getElementsByClassName("tab-link");

    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    };

    for (i = 0; i < tabLinks.length; i++) {
        tabLinks[i].className = tabLinks[i].className.replace(" active", "");
    };

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    localStorage.setItem("currentTab", evt.currentTarget.id);
}