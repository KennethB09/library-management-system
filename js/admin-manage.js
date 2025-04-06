const fileInputAdd = document.getElementById("fileInputAdd");
const availableOnAdd = document.getElementById("availableOnAdd");
const addFormCopiesInput = document.getElementById("addFormCopiesInput");

availableOnAdd.addEventListener("change", () => {

    availableOnAdd.value === "d" ? addFormCopiesInput.style.display = "none" : addFormCopiesInput.style.display = "block";
    
    if ((availableOnAdd.value === "d") || (availableOnAdd.value === "pd")) {
        fileInputAdd.disabled = false;
    } else {
        fileInputAdd.disabled = true;
        addFormCopiesInput.style.display = "block"
    }
})