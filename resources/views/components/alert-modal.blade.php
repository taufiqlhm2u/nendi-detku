<script type="module">
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: @json(session('success')),
    confirmButtonText: 'OK',
    confirmButtonColor: '#16a34a'
});
@endif

@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: @json(session('error')),
    confirmButtonText: 'OK',
    confirmButtonColor: '#dc2626'
});
@endif

@if(session('warning'))
Swal.fire({
    icon: 'warning',
    title: 'Perhatian',
    text: @json(session('warning')),
    confirmButtonText: 'OK',
    confirmButtonColor: '#d97706'
});
@endif

@if(session('info'))
Swal.fire({
    icon: 'info',
    title: 'Informasi',
    text: @json(session('info')),
    confirmButtonText: 'OK',
    confirmButtonColor: '#2563eb'
});
@endif
</script>

<script>
function confirmDelete(form) {

    Swal.fire({
        title: 'Hapus Data?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        reverseButtons: true
    }).then((result) => {

        if (result.isConfirmed) {
            form.submit();
        }

    });
}

function confirmLogout() {

    Swal.fire({
        title: 'Logout?',
        text: 'Anda akan keluar dari akun.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Logout',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626'
    }).then((result) => {

        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }

    });
}
</script>