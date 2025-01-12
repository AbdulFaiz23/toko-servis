// Tambahkan animasi smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Tambahkan konfirmasi sebelum menghapus
function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus item ini?');
}

// Tambahkan validasi form
function validateForm() {
    let isValid = true;
    document.querySelectorAll('[required]').forEach(input => {
        if (!input.value) {
            isValid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    return isValid;
} 