// Validasi form kontak
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const message = document.getElementById('message').value.trim();
    
    if (!name || !email || !message) {
        e.preventDefault();
        alert('Harap isi semua field!');
    }
});

// Hitung total harga di halaman pemesanan
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const quantityInput = this.parentElement.querySelector('input[type="number"]');
        if (this.checked) {
            quantityInput.disabled = false;
        } else {
            quantityInput.disabled = true;
            quantityInput.value = 1;
        }
    });
});