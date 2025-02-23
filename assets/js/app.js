document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("submit", function (event) {
        event.preventDefault(); // Sayfanın yeniden yüklenmesini engelle

        // Form verilerini al
        let formData = new FormData(this);

        // Form verilerini JSON formatına çevir
        let jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value.trim(); // Gereksiz boşlukları kaldır
        });

        // Giriş verilerini doğrula
        if (!jsonData.name || !jsonData.email || !jsonData.phone || !jsonData.message) {
            alert("Lütfen tüm alanları doldurun.");
            return;
        }

        if (!/^\S+@\S+\.\S+$/.test(jsonData.email)) {
            alert("Geçerli bir e-posta adresi girin.");
            return;
        }

        if (!/^[0-9]+$/.test(jsonData.phone)) {
            alert("Telefon numarası yalnızca rakamlardan oluşmalıdır.");
            return;
        }

        if (jsonData.message.length > 200) {
            alert("Mesaj en fazla 200 karakter olmalıdır.");
            return;
        }

        // Fetch API ile POST isteği gönder
        fetch("links/save.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Sunucu hatası: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data && data.message) {
                alert(escapeHTML(data.message)); // XSS önlemi
            } else {
                alert("Bilinmeyen bir hata oluştu.");
            }
            document.querySelector("form").reset(); // Formu temizle
        })
        .catch(error => {
            console.error("Hata:", error);
            alert("Bir hata oluştu, lütfen tekrar deneyin.");
        });
    });
    //boş
    // XSS saldırılarını önlemek için HTML özel karakterlerini kaçış karakterine çeviren fonksiyon
    function escapeHTML(str) {
        return str.replace(/[&<>"']/g, function (match) {
            return {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;"
            }[match];
        });
    }
});
function toggleMenu() {
    document.querySelector(".mobile-menu").classList.toggle("open");
}