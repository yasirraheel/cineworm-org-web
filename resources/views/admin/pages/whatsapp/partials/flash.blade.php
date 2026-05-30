<script type="text/javascript">
    @if (Session::has('flash_message'))
        Swal.fire({
            icon: 'success',
            title: '{{ Session::get('flash_message') }}',
            showConfirmButton: false,
            timer: 3000,
            background: "#1a2234",
            color: "#fff"
        });
    @endif

    @if (Session::has('error_flash_message'))
        Swal.fire({
            icon: 'error',
            title: '{{ Session::get('error_flash_message') }}',
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background: "#1a2234",
            color: "#fff"
        });
    @endif

    @if (count($errors) > 0)
        Swal.fire({
            icon: 'error',
            title: 'Please check the form',
            html: '<p>@foreach ($errors->all() as $error) {{ $error }}<br/> @endforeach</p>',
            showConfirmButton: true,
            confirmButtonColor: '#10c469',
            background: "#1a2234",
            color: "#fff"
        });
    @endif
</script>
