@extends("admin.admin_app")

@section("content")

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box">

                <div class="row m-b-20">
                  <div class="col-md-8">
                    <h4 class="m-t-0 header-title"><i class="fa fa-paper-plane text-primary m-r-5"></i> Compose & Send Newsletter</h4>
                    <p class="text-muted font-13 m-b-0">
                      Broadcast news, releases, or announcements to all active subscribers. Every email includes a secure unsubscribe link.
                    </p>
                  </div>
                  <div class="col-md-4 text-md-right">
                    <a href="{{ url('admin/newsletter/subscribers') }}" class="btn btn-secondary waves-effect">
                      <i class="fa fa-arrow-left m-r-5"></i> Back to Subscribers
                    </a>
                  </div>
                </div>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      {{ Session::get('flash_message') }}
                    </div>
                @endif

                @if($errors->any())
                  <div class="alert alert-danger">
                    <ul class="m-b-0">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <div class="row">
                  <!-- Main Compose Form -->
                  <div class="col-lg-8">
                    <form action="{{ url('admin/newsletter/send') }}" method="POST" id="newsletterForm">
                      @csrf

                      <div class="form-group">
                        <label class="font-weight-bold">Audience Target</label>
                        <div class="p-2" style="background: #25252e; border: 1px solid #33333d; border-radius: 4px; color: #d0d0d8;">
                          <i class="fa fa-users text-success m-r-5"></i>
                          Sending to <strong>{{ $active_count }}</strong> Active Subscribers
                          @if($active_count == 0)
                            <span class="text-danger ml-2 font-weight-bold">(No active subscribers to send to)</span>
                          @endif
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="subject" class="font-weight-bold">Subject Line <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g. New Releases on Cineworm this weekend!" value="{{ old('subject') }}" required>
                      </div>

                      <div class="form-group">
                        <label for="content" class="font-weight-bold">Message Content (HTML or formatted text) <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" rows="12" class="form-control" placeholder="Write your announcement or newsletter body here..." required>{{ old('content') }}</textarea>
                        <small class="form-text text-muted">You can write plain text or HTML markup (headings, paragraphs, links, images, etc.).</small>
                      </div>

                      <div class="form-group m-t-20">
                        <button type="button" id="btnBroadcastConfirm" class="btn btn-primary btn-lg waves-effect waves-light" @if($active_count == 0) disabled @endif>
                          <i class="fa fa-send m-r-5"></i> Send Newsletter to {{ $active_count }} Subscribers
                        </button>
                      </div>
                    </form>
                  </div>

                  <!-- Test Send Card -->
                  <div class="col-lg-4">
                    <div class="card-box" style="background: #1a1a21; border: 1px solid #2d2d38;">
                      <h5 class="header-title m-t-0" style="color: #fff;"><i class="fa fa-flask text-warning m-r-5"></i> Test Delivery First</h5>
                      <p class="text-muted font-13">
                        Always preview the newsletter in your own inbox to verify how it displays before sending it to your entire subscriber list.
                      </p>

                      <div class="form-group">
                        <label>Test Recipient Email</label>
                        <input type="email" id="test_email" class="form-control" value="{{ $admin_email }}" placeholder="your-email@example.com">
                      </div>

                      <button type="button" id="btnSendTest" class="btn btn-warning btn-block waves-effect waves-light">
                        <i class="fa fa-envelope-o m-r-5"></i> Send Test Preview
                      </button>

                      <div id="testResultAlert" class="m-t-15" style="display: none;"></div>
                    </div>

                    <div class="card-box" style="background: #1a1a21; border: 1px solid #2d2d38;">
                      <h5 class="header-title m-t-0" style="color: #fff;"><i class="fa fa-info-circle text-info m-r-5"></i> Tips</h5>
                      <ul class="text-muted font-13 pl-3 mb-0" style="line-height: 1.8;">
                        <li>Keep your subject line punchy and descriptive.</li>
                        <li>Include call-to-action buttons or links back to your movies and shows.</li>
                        <li>The system automatically includes your logo and an unsubscribe footer for compliance.</li>
                      </ul>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright") 
  </div>

  <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      // Send Test Email AJAX
      $('#btnSendTest').click(function() {
        var testEmail = $('#test_email').val();
        var subject = $('#subject').val();
        var content = $('#content').val();
        var resultDiv = $('#testResultAlert');

        if (!subject) {
          alert('Please enter a subject line first.');
          $('#subject').focus();
          return;
        }

        if (!content) {
          alert('Please enter message content first.');
          $('#content').focus();
          return;
        }

        if (!testEmail) {
          alert('Please enter a test email address.');
          $('#test_email').focus();
          return;
        }

        var origBtnText = $(this).html();
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin m-r-5"></i> Sending...');
        resultDiv.hide();

        $.ajax({
          type: 'POST',
          url: "{{ url('admin/newsletter/send-test') }}",
          data: {
            _token: "{{ csrf_token() }}",
            test_email: testEmail,
            subject: subject,
            content: content
          },
          success: function(res) {
            $('#btnSendTest').prop('disabled', false).html(origBtnText);
            if (res.status === 'success') {
              resultDiv.removeClass('alert-danger').addClass('alert alert-success').html('<i class="fa fa-check m-r-5"></i> ' + res.message).slideDown();
            } else {
              resultDiv.removeClass('alert-success').addClass('alert alert-danger').html('<i class="fa fa-times m-r-5"></i> ' + res.message).slideDown();
            }
          },
          error: function(xhr) {
            $('#btnSendTest').prop('disabled', false).html(origBtnText);
            var errMsg = 'Failed to send test email. Please check your SMTP settings.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errMsg = xhr.responseJSON.message;
            }
            resultDiv.removeClass('alert-success').addClass('alert alert-danger').html('<i class="fa fa-times m-r-5"></i> ' + errMsg).slideDown();
          }
        });
      });

      // Broadcast Confirmation
      $('#btnBroadcastConfirm').click(function() {
        var subject = $('#subject').val();
        var content = $('#content').val();

        if (!subject || !content) {
          alert('Please provide both a subject line and message content.');
          return;
        }

        Swal.fire({
          title: 'Broadcast Newsletter?',
          text: "This will send the newsletter to {{ $active_count }} active subscriber(s). Are you sure?",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, broadcast now!',
          cancelButtonText: 'Cancel',
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
