document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("submit", function (event) {
        event.preventDefault(); // Sayfanın yeniden yüklenmesini engelle

        // Form verilerini al
        let formData = new FormData(this);

        // Form verilerini JSON formatına çevir
        let jsonData = {};
        formData.forEach((value, key) => { jsonData[key] = value });

        // Fetch API ile POST isteği gönder
        fetch("links/save.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json()) // JSON olarak yanıtı al
        .then(data => {
            alert(data.message); // Kullanıcıya geri bildirim ver
            document.querySelector("form").reset(); // Formu temizle
        })
        .catch(error => console.error("Hata:", error));
    });
});