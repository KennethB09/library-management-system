function onReturn(event, borrowId, bookRef, borrower) {
    event.preventDefault();

    let formData = new FormData();

    formData.append("borrowId", borrowId);
    formData.append("bookRef", bookRef);
    formData.append("borrower", borrower);

    fetch("../controllers/return.php", {
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