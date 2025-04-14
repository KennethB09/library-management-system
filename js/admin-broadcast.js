const form1 = document.querySelector(".broadcast-form-1");
const form2 = document.querySelector(".broadcast-form-2");
const formSelector = document.getElementById("broadcastFormSelector");

formSelector.addEventListener("change", () => {
    
    if (formSelector.value === "all") {
        form1.setAttribute("data-visible", "true");
        form2.setAttribute("data-visible", "false");
    } else {
        form1.setAttribute("data-visible", "false");
        form2.setAttribute("data-visible", "true");
    }
})