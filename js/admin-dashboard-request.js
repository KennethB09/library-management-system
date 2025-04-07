function onApprove(event, requestId, bookRef, dueDate, borrower, format) {
    event.preventDefault();
    console.log(format)

    let formData = new FormData();

    formData.append("requestId", requestId);
    formData.append("bookRef", bookRef);
    formData.append("dueDate", dueDate);
    formData.append("borrower", borrower);
    formData.append("format", format);
    fetch("../controllers/approve.php", {
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

function onDecline(event, requestId) {
    event.preventDefault();
    let formData = new FormData();
    formData.append("requestId", requestId);
    fetch("../controllers/decline.php", {
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