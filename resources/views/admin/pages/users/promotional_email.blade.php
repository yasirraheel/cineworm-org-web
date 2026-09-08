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
                    <a href="{{ URL::to('admin/users') }}">
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
                            <input type="email" name="test_email" placeholder="{{ trans('words.email') }}" class="form-control" id="test_email" value="{{ Auth::User()->email }}" autocomplete="off" required>
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

                <form action="{{ url('admin/users/promotional-email/send') }}" method="POST" class="form-horizontal" id="promoEmailForm">
                  @csrf

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Target Audience *</label>
                    <div class="col-sm-8">
                      <select name="audience" id="audience" class="form-control">
                        <option value="all">All Registered Users ({{ $total_users }} users)</option>
                        <option value="active_only">Active Users Only ({{ $active_users }} users)</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Subject Line *</label>
                    <div class="col-sm-8">
                      <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g. Special Offer on Cineworm" value="{{ old('subject') }}" required>
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
                      <button type="button" id="btnQueueBroadcast" class="btn btn-primary waves-effect waves-light" @if($total_users == 0) disabled @endif>
                        <i class="fa fa-send"></i> Queue & Send to Users
                      </button>
                    </div>
                  </div>

                </form>

              </div>
            </div>
          </div>

          <!-- Campaign History & Live Queue Table -->
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">
                <h4 class="header-title m-t-0 m-b-20 text-primary"><i class="fa fa-history"></i> Promotional Campaigns History & Queue Status</h4>
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th style="width: 50px;">#</th>
                        <th>Subject</th>
                        <th>Audience</th>
                        <th>Recipients</th>
                        <th>Sent</th>
                        <th>Failed</th>
                        <th>Status</th>
                        <th>Date Queued</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($campaigns as $i => $camp)
                        <tr>
                          <td>{{ $campaigns->firstItem() + $i }}</td>
                          <td><strong>{{ $camp->subject }}</strong></td>
                          <td>
                            @if($camp->audience === 'active_only')
                              <span class="badge badge-info">Active Users Only</span>
                            @else
                              <span class="badge badge-secondary">All Users</span>
                            @endif
                          </td>
                          <td><strong>{{ $camp->total_recipients }}</strong></td>
                          <td><span class="text-success font-weight-bold">{{ $camp->sent_count }}</span></td>
                          <td><span class="text-danger font-weight-bold">{{ $camp->failed_count }}</span></td>
                          <td>
                            @if($camp->status === 'completed')
                              <span class="badge badge-success"><i class="fa fa-check"></i> Completed</span>
                            @elseif($camp->status === 'processing')
                              <span class="badge badge-warning"><i class="fa fa-spinner fa-spin"></i> Processing ({{ $camp->sent_count }}/{{ $camp->total_recipients }})</span>
                            @else
                              <span class="badge badge-info"><i class="fa fa-clock-o"></i> Queued</span>
                            @endif
                          </td>
                          <td>{{ $camp->created_at ? $camp->created_at->format('M d, Y H:i') : '-' }}</td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="8" class="text-center py-4 text-muted">
                            No promotional campaigns queued yet.
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

                <nav class="paging_simple_numbers">
                  @include('admin.pagination', ['paginator' => $campaigns])
                </nav>

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

      // Send Test Email AJAX
      $('#test_email_sent_btn').click(function() {
        var testEmail = $('#test_email').val();
        var subject = $('#subject').val();
        var content = (typeof tinymce !== 'undefined' && tinymce.get('elm1'))
          ? tinymce.get('elm1').getContent()
          : $('#elm1').val();

        if (!subject) {
          alert('Please enter an email subject first.');
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
          url: "{{ url('admin/users/promotional-email/test') }}",
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

      // Queue & Send Confirmation
      $('#btnQueueBroadcast').click(function() {
        var subject = $('#subject').val();
        var content = (typeof tinymce !== 'undefined' && tinymce.get('elm1'))
          ? tinymce.get('elm1').getContent()
          : $('#elm1').val();

        if (!subject || !content) {
          alert('Please provide both a subject and message body.');
          return;
        }

        var audienceText = $('#audience option:selected').text();

        Swal.fire({
          title: 'Queue Promotional Email Campaign?',
          text: "This will queue emails for " + audienceText + ". Emails will be sent in batches automatically by your server cron.",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Queue Campaign Now!',
          cancelButtonText: "{{ trans('words.btn_cancel') }}",
          background: "#1a2234",
          color: "#fff"
        }).then((result) => {
          if (result.isConfirmed) {
            $('#promoEmailForm').submit();
          }
        });
      });
    });
  </script>

@endsection
