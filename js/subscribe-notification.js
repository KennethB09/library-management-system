const notificationIcon = document.getElementById("notificationIcon");
navigator.serviceWorker.register("../sw.js");

if (Notification.permission === "granted") {

    notificationIcon.src = "../assets/notifications-outline.svg";
} else {
    notificationIcon.src = "../assets/notifications-off-outline.svg";
}

function enableNotification() {
    console.log("hi")
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            // get service worker
            navigator.serviceWorker.ready.then((sw) => {
                console.log("lo")
                // subscribe
                sw.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: "BM2ApnJjDV_efsvitcM3c_Ylu8tzraD_Zpo4OcyUDVR61ObJbY95g1tHVu_U7oNSeYcOJ5zLS73VaFhEzRKtofg"
                }).then((subscription) => {
                    console.log(JSON.stringify(subscription));

                    let formData = new FormData();
                    formData.append("credential", JSON.stringify(subscription));
                    fetch("../utility/upload-credential.php", {
                        method: "POST",
                        body: formData
                    })
                        .then(response => response.text())
                        .then(data => {
                            console.log(data);
                        })
                        .catch(error => console.error(error));
                });
            });
        }
        console.log("hello")
    });
}