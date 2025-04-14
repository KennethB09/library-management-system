function sendUserNotification(event) {
    event.preventDefault();

    let formData = new FormData();
    formData.append("title", selectedBook);
    formData.append("body", selectedBookAvailableOn);
    fetch("../utility/admin-delete-book.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            alert(data);
            openDeleteModal(false)
            location.reload();
        })
        .catch(error => console.error(error));
}