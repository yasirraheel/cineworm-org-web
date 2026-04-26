<script type="text/javascript">
  @if ($errors->any())
  Swal.fire({
    icon: 'error',
    title: 'Please check the form',
    html: '@foreach ($errors->all() as $error)<p style="margin:0;">{{ $error }}</p>@endforeach',
    confirmButtonColor: '#ff0015',
    background: '#1a1a1d',
    color: '#fff'
  });
  @endif

  @if(Session::has('flash_message'))
  Swal.fire({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    icon: 'success',
    title: '{{ Session::get('flash_message') }}'
  });
  @endif

  @if(Session::has('error_flash_message'))
  Swal.fire({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    icon: 'error',
    title: '{{ Session::get('error_flash_message') }}'
  });
  @endif
</script>
