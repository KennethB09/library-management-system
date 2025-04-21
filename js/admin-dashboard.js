
// Load active tab from local storage

const currentTab = localStorage.getItem("currentTab");
const activeTab = document.getElementById("defaultTab");

if (currentTab) {
    document.getElementById(currentTab).click();
} else {
    localStorage.setItem("currentTab", activeTab);
    document.getElementById("defaultTab").click();
}

const selectedScheme = localStorage.getItem("scheme");
const root = document.getElementById("root");

if (selectedScheme === "light") {
    root.setAttribute("data-scheme", "light");
} else {
    root.setAttribute("data-scheme", "dark");
}

function changeTheme() {
    const getScheme = root.getAttribute("data-scheme");
    
    if (getScheme === "light") {
        root.setAttribute("data-scheme", "dark");
        localStorage.setItem("scheme", "dark");
    } else {
        root.setAttribute("data-scheme", "light");
        localStorage.setItem("scheme", "light");
    }
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

function toggleModal(container) {
    const modal = document.getElementById(container);
    const modalVisible = modal.getAttribute("data-visible");

    if (modalVisible === "true") {
        modal.setAttribute("data-visible", "false");
    } else {
        modal.setAttribute("data-visible", "true");
    }
}

function showForm(container1, container2, container3) {
    const modal1 = document.getElementById(container1);
    const modal2 = document.getElementById(container2);
    const modal3 = document.getElementById(container3);
    const modal1Visible = modal1.getAttribute("data-visible");

    if (modal1Visible === "true") {
        modal1.setAttribute("data-visible", "false");
        modal2.setAttribute("data-visible", "true");
        modal3.setAttribute("data-visible", "false");
    } else {
        modal2.setAttribute("data-visible", "false");
        modal3.setAttribute("data-visible", "false");
        modal1.setAttribute("data-visible", "true");
    }
}

function alertDismiss() {
    const alert = document.getElementById("alert");
    alert.style.display = "none";    
}

// Function to validate form fields and display errors
function validateAdminForm() {
    // Get form elements
    const firstName = document.getElementById('edit-first-name');
    const lastName = document.getElementById('edit-last-name');
    const newPass = document.getElementById('edit-new-pass');
    const reNewPass = document.getElementById('edit-re-new-pass');
    const oldPass = document.getElementById('edit-old-pass');

    // Reset previous errors
    const errorElements = document.querySelectorAll('.error');
    errorElements.forEach(el => el.textContent = '');

    let isValid = true;

    // Validate first name
    if (firstName.value.trim() === '') {
        showError(firstName, 'First Name is required.');
        isValid = false;
    } else if (firstName.value.trim().length < 2) {
        showError(firstName, 'First Name must be at least 2 characters.');
        isValid = false;
    }

    // Validate last name
    if (lastName.value.trim() === '') {
        showError(lastName, 'Last Name is required.');
        isValid = false;
    } else if (lastName.value.trim().length < 2) {
        showError(lastName, 'Last Name must be at least 2 characters.');
        isValid = false;
    }

    // Validate password fields if any of them is filled
    if (newPass.value || reNewPass.value || oldPass.value) {
        // Check if new password meets requirements
        if (newPass.value.trim() === '') {
            showError(newPass, 'New password is required.');
            isValid = false;
        } else if (newPass.value.length < 8) {
            showError(newPass, 'Password must be at least 8 characters.');
            isValid = false;
        }

        // Check if passwords match
        if (reNewPass.value.trim() === '') {
            showError(reNewPass, 'Please re-type your new password.');
            isValid = false;
        } else if (newPass.value !== reNewPass.value) {
            showError(reNewPass, 'New passwords do not match.');
            isValid = false;
        }

        // Check if old password is provided
        if (oldPass.value.trim() === '') {
            showError(oldPass, 'Old password is required to change password.');
            isValid = false;
        }
    }

    return isValid;
}

// Helper function to show validation errors
function showError(inputElement, message) {
    const errorSpan = inputElement.nextElementSibling;
    errorSpan.textContent = message;
}

// Your existing form submission code with validation added

const updateUserInfo = document.getElementById('adminUpdateInfo');
updateUserInfo.addEventListener('submit', (event) => {
    event.preventDefault();

    // Validate form before submission
    if (validateAdminForm()) {
        const formData = new FormData(updateUserInfo);

        fetch("../controllers/admin-update-info.php", {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then((data) => {

                location.reload();

            })
            .catch(error => console.error(error));
    }
});