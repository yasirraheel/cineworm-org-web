@extends("admin.admin_app")

@section("content")

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">

          <div class="row">
            <div class="col-12">
              <div class="card-box">

                <div class="row m-b-20 align-items-center">
                  <div class="col-md-8">
                    <h4 class="m-t-0 header-title"><i class="fa fa-envelope-o text-primary m-r-5"></i> Send Promotional Email to Users</h4>
                    <p class="text-muted font-13 m-b-0">
                      Compose and broadcast promotional emails to registered users. All emails are processed in rate-limited batches via the cron queue to ensure high deliverability and avoid server timeouts.
                    </p>
                  </div>
                  <div class="col-md-4 text-md-right">
                    <a href="{{ url('admin/users') }}" class="btn btn-secondary waves-effect">
                      <i class="fa fa-arrow-left m-r-5"></i> Back to Users
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
                    <form action="{{ url('admin/users/promotional-email/send') }}" method="POST" id="promoEmailForm">
                      @csrf

                      <div class="form-group">
                        <label class="font-weight-bold">Target Audience <span class="text-danger">*</span></label>
                        <select name="audience" id="audience" class="form-control" style="background: #23232a; color: #fff; border: 1px solid #363642;">
                          <option value="all">All Registered Users ({{ $total_users }} users)</option>
                          <option value="active_only">Active Users Only ({{ $active_users }} users)</option>
                        </select>
                      </div>

                      <div class="form-group">
                        <label for="subject" class="font-weight-bold">Email Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g. Special Offer: Enjoy 50% off on Cineworm Premium this month!" value="{{ old('subject') }}" required>
                      </div>

                      <div class="form-group">
                        <label for="content" class="font-weight-bold">Message Body (HTML or Plain Text) <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" rows="12" class="form-control" placeholder="Write your promotional announcement here. You can use standard HTML (paragraphs, bold text, links, buttons, images)..." required>{{ old('content') }}</textarea>
                        <small class="form-text text-muted">HTML tags such as &lt;h2&gt;, &lt;p&gt;, &lt;a&gt;, &lt;img&gt; are supported. Each email automatically includes your site logo, greeting, and site branding.</small>
                      </div>

                      <div class="form-group m-t-20">
                        <button type="button" id="btnQueueBroadcast" class="btn btn-primary btn-lg waves-effect waves-light" @if($total_users == 0) disabled @endif>
                          <i class="fa fa-send m-r-5"></i> Queue & Send to Users (1-Click)
                        </button>
                      </div>
                    </form>
                  </div>

                  <!-- Test Delivery Panel & Cron Info -->
                  <div class="col-lg-4">
                    <div class="card-box" style="background: #1a1a21; border: 1px solid #2d2d38;">
                      <h5 class="header-title m-t-0" style="color: #fff;"><i class="fa fa-flask text-warning m-r-5"></i> Send Test Preview</h5>
                      <p class="text-muted font-13">
                        Deliver a test preview to your own inbox first to check layout and formatting before queueing.
                      </p>

                      <div class="form-group">
                        <label>Test Recipient</label>
                        <input type="email" id="test_email" class="form-control" value="{{ Auth::User()->email }}" placeholder="admin@example.com">
                      </div>

                      <button type="button" id="btnSendTest" class="btn btn-warning btn-block waves-effect waves-light">
                        <i class="fa fa-envelope-o m-r-5"></i> Send Test Preview
                      </button>

                      <div id="testResultAlert" class="m-t-15" style="display: none;"></div>
                    </div>

                    <div class="card-box" style="background: #1a1a21; border: 1px solid #2d2d38;">
                      <h5 class="header-title m-t-0" style="color: #fff;"><i class="fa fa-clock-o text-info m-r-5"></i> Batch Queue Delivery</h5>
                      <p class="text-muted font-13" style="line-height: 1.6;">
                        <i class="fa fa-check text-success m-r-5"></i> Emails are <strong>automatically batch-processed</strong> via your existing server cron (<code>task:cron</code>) every minute.
                        <br><br>
                        <i class="fa fa-check text-success m-r-5"></i> Rate-limited to <strong>30 emails/min</strong> to prevent SMTP blocks, spam flags, or server timeouts.
                      </p>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- Campaign History & Live Queue Table -->
          <div class="row m-t-20">
            <div class="col-12">
              <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title"><i class="fa fa-history text-success m-r-5"></i> Promotional Campaigns History & Queue Status</h4>
                <p class="text-muted font-13 m-b-20">
                  Track the progress of all queued and completed promotional email campaigns.
                </p>

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
          alert('Please enter an email subject first.');
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

        var origText = $(this).html();
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin m-r-5"></i> Sending Test...');
        resultDiv.hide();

        $.ajax({
          type: 'POST',
          url: "{{ url('admin/users/promotional-email/test') }}",
          data: {
            _token: "{{ csrf_token() }}",
            test_email: testEmail,
            subject: subject,
            content: content
          },
          success: function(res) {
            $('#btnSendTest').prop('disabled', false).html(origText);
            if (res.status === 'success') {
              resultDiv.removeClass('alert-danger').addClass('alert alert-success').html('<i class="fa fa-check m-r-5"></i> ' + res.message).slideDown();
            } else {
              resultDiv.removeClass('alert-success').addClass('alert alert-danger').html('<i class="fa fa-times m-r-5"></i> ' + res.message).slideDown();
            }
          },
          error: function(xhr) {
            $('#btnSendTest').prop('disabled', false).html(origText);
            var errMsg = 'Failed to send test email.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errMsg = xhr.responseJSON.message;
            }
            resultDiv.removeClass('alert-success').addClass('alert alert-danger').html('<i class="fa fa-times m-r-5"></i> ' + errMsg).slideDown();
          }
        });
      });

      // Queue & Send Confirmation
      $('#btnQueueBroadcast').click(function() {
        var subject = $('#subject').val();
        var content = $('#content').val();

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
          cancelButtonText: 'Cancel',
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
