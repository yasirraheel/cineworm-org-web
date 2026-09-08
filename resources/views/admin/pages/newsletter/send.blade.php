@extends("admin.admin_app")

@section("content")

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div class="card-box">

                <div class="row">
                  <div class="col-sm-6">
                    <a href="{{ URL::to('admin/newsletter/subscribers') }}">
                      <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;">
                        <i class="fa fa-arrow-left"></i> {{ trans('words.back') }}
                      </h4>
                    </a>
                  </div>
                  <div class="col-sm-6">
                    <a href="#" class="btn btn-info btn-md waves-effect waves-light m-b-20 mt-2 pull-right" title="Test Email" data-toggle="modal" data-target="#smtp_test_model">
                      <i class="fa fa-send"></i> Test Email
                    </a>
                  </div>
                </div>

                <!-- Test Email Modal -->
                <div id="smtp_test_model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Send Test Email</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group row">
                          <label class="col-sm-3 col-form-label">{{ trans('words.test_email') }}</label>
                          <div class="col-sm-9">
                            <input type="email" name="test_email" placeholder="{{ trans('words.email') }}" class="form-control" id="test_email" value="{{ $admin_email }}" autocomplete="off" required>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" id="test_email_sent_btn" class="btn btn-primary waves-effect waves-light">
                          {{ trans('words.send') }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      {{ Session::get('flash_message') }}
                    </div>
                @endif

                @if($errors->any())
                  <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    <ul class="m-b-0 pl-3">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ url('admin/newsletter/send') }}" method="POST" class="form-horizontal" id="newsletterForm">
                  @csrf

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Audience Target</label>
                    <div class="col-sm-8">
                      <input type="text" class="form-control" value="{{ $active_count }} Active Subscribers" readonly>
                      @if($active_count == 0)
                        <small class="text-danger">No active subscribers found in database.</small>
                      @endif
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Subject Line *</label>
                    <div class="col-sm-8">
                      <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g. New Releases on Cineworm" value="{{ old('subject') }}" required>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Message Content *</label>
                    <div class="col-sm-8">
                      <textarea id="elm1" name="content" class="form-control">{{ old('content') }}</textarea>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="offset-sm-3 col-sm-9 pl-1">
                      <button type="button" id="btnSubmitNewsletter" class="btn btn-primary waves-effect waves-light" @if($active_count == 0) disabled @endif>
                        <i class="fa fa-send"></i> Send Newsletter
                      </button>
                    </div>
                  </div>

                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright") 
  </div>

  <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
      // Ensure TinyMCE initializes if not already auto-initialized
      if (typeof tinymce !== 'undefined' && !tinymce.get('elm1')) {
        tinymce.init({
          selector: "textarea#elm1",
          height: 350,
          plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen link template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars emoticons',
          toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor"
        });
      }

      // Test Email Send
      $('#test_email_sent_btn').click(function() {
        var testEmail = $('#test_email').val();
        var subject = $('#subject').val();
        var content = (typeof tinymce !== 'undefined' && tinymce.get('elm1'))
          ? tinymce.get('elm1').getContent()
          : $('#elm1').val();

        if (!subject) {
          alert('Please enter a subject line first.');
          $('#subject').focus();
          return;
        }

        if (!content) {
          alert('Please enter message content first.');
          return;
        }

        if (!testEmail) {
          alert('Please enter a test email address.');
          $('#test_email').focus();
          return;
        }

        var btn = $(this);
        btn.html('sending...').prop('disabled', true);

        $.ajax({
          type: 'POST',
          url: "{{ url('admin/newsletter/send-test') }}",
          data: {
            _token: "{{ csrf_token() }}",
            test_email: testEmail,
            subject: subject,
            content: content
          },
          dataType: 'json',
          success: function(response) {
            btn.html("{{ trans('words.send') }}").prop('disabled', false);
            $('#smtp_test_model').modal('hide');

            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: false
            });

            if (response.status === 'success') {
              Toast.fire({ icon: 'success', title: response.message });
            } else {
              Toast.fire({ icon: 'error', title: response.message });
            }
          },
          error: function(xhr) {
            btn.html("{{ trans('words.send') }}").prop('disabled', false);
            var errMsg = 'Failed to send test email.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errMsg = xhr.responseJSON.message;
            }
            alert(errMsg);
          }
        });
      });

      // Confirmation
      $('#btnSubmitNewsletter').click(function() {
        var subject = $('#subject').val();
        var content = (typeof tinymce !== 'undefined' && tinymce.get('elm1'))
          ? tinymce.get('elm1').getContent()
          : $('#elm1').val();

        if (!subject || !content) {
          alert('Please provide both a Subject Line and Message Content.');
          return;
        }

        Swal.fire({
          title: '{{ trans("words.dlt_warning") }}',
          text: "Send newsletter to {{ $active_count }} active subscriber(s)?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Send Now!',
          cancelButtonText: "{{ trans('words.btn_cancel') }}",
          background: "#1a2234",
          color: "#fff"
        }).then((result) => {
          if (result.isConfirmed) {
            $('#newsletterForm').submit();
          }
        });
      });
    });
  </script>

@endsection
