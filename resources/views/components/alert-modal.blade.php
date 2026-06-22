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