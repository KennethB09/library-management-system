const fileInputAdd = document.getElementById("fileInputAdd");
const formatAdd = document.getElementById("formatAdd");
const addFormCopiesInput = document.getElementById("addFormCopiesInput");

formatAdd.addEventListener("change", () => {

    formatAdd.value === "digital" ? addFormCopiesInput.style.display = "none" : addFormCopiesInput.style.display = "block";
    
    if ((formatAdd.value === "digital") || (formatAdd.value === "both")) {
        fileInputAdd.disabled = false;
    } else {
        fileInputAdd.disabled = true;
        addFormCopiesInput.style.display = "block"
    }
})