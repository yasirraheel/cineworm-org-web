@extends("admin.admin_app")

@section("content")

  <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row m-b-20">
                  <div class="col-md-3 col-sm-6 mb-2">
                    <select class="form-control" name="status_select" id="status_select">
                      <option value="{{ url('admin/newsletter/subscribers') }}">All Subscribers ({{ $total_count }})</option>
                      <option value="{{ url('admin/newsletter/subscribers?status=1') }}" @if(request('status') === '1') selected @endif>Active ({{ $active_count }})</option>
                      <option value="{{ url('admin/newsletter/subscribers?status=0') }}" @if(request('status') === '0') selected @endif>Unsubscribed ({{ $unsubscribed_count }})</option>
                    </select>
                  </div>

                  <div class="col-md-3 col-sm-6 mb-2">
                    <form action="{{ url('admin/newsletter/subscribers') }}" method="get" class="app-search" id="search" role="form">
                      @if(request()->has('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                      @endif
                      <input type="text" name="s" value="{{ request('s') }}" placeholder="Search by email or name..." class="form-control">
                      <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                  </div>

                  <div class="col-md-6 col-sm-12 text-md-right mb-2">
                    <button type="button" class="btn btn-success btn-md waves-effect waves-light m-r-5" data-toggle="modal" data-target="#addSubscriberModal" title="Add Subscriber">
                      <i class="fa fa-plus"></i> Add Subscriber
                    </button>
                    <a href="{{ url('admin/newsletter/send') }}" class="btn btn-primary btn-md waves-effect waves-light m-r-5" title="Compose & Send Newsletter">
                      <i class="fa fa-paper-plane"></i> Send Newsletter
                    </a>
                    <a href="{{ url('admin/newsletter/export' . (request()->has('status') ? '?status='.request('status') : '')) }}" class="btn btn-info btn-md waves-effect waves-light" title="Export CSV">
                      <i class="fa fa-file-excel-o"></i> Export CSV
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

                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th style="width: 50px;">#</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>IP Address</th>
                        <th>Subscribed Date</th>
                        <th style="width: 140px; text-align: center;">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($subscribers as $i => $item)
                        <tr id="subscriber_row_{{ $item->id }}">
                          <td>{{ $subscribers->firstItem() + $i }}</td>
                          <td><strong>{{ $item->email }}</strong></td>
                          <td>{{ $item->name ?: '-' }}</td>
                          <td id="status_badge_{{ $item->id }}">
                            @if($item->status == 1)
                              <span class="badge badge-success">Active</span>
                            @else
                              <span class="badge badge-secondary">Unsubscribed</span>
                            @endif
                          </td>
                          <td>{{ $item->ip_address ?: '-' }}</td>
                          <td>{{ $item->created_at ? $item->created_at->format('M d, Y H:i') : '-' }}</td>
                          <td style="text-align: center;">
                            <!-- Toggle Status -->
                            <button type="button" class="btn btn-icon waves-effect waves-light btn-warning m-b-5 m-r-5 toggle_status_btn" data-id="{{ $item->id }}" data-toggle="tooltip" title="Toggle Active/Unsubscribed">
                              <i class="fa fa-refresh"></i>
                            </button>

                            <!-- Delete Subscriber -->
                            <button type="button" class="btn btn-icon waves-effect waves-light btn-danger m-b-5 delete_subscriber_btn" data-id="{{ $item->id }}" data-toggle="tooltip" title="Delete Subscriber">
                              <i class="fa fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="7" class="text-center py-4" style="color: #8c8c9e;">
                            <i class="fa fa-envelope-open-o m-b-10" style="font-size: 32px; display: block;"></i>
                            No newsletter subscribers found.
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

                <nav class="paging_simple_numbers">
                  @include('admin.pagination', ['paginator' => $subscribers])
                </nav>

              </div>
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright") 
  </div>

  <!-- Add Subscriber Modal -->
  <div id="addSubscriberModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addSubscriberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background: #1e1e24; color: #fff; border: 1px solid #32323c;">
        <div class="modal-header" style="border-bottom: 1px solid #32323c;">
          <h5 class="modal-title" id="addSubscriberModalLabel" style="color: #fff;"><i class="fa fa-user-plus m-r-5"></i> Add Newsletter Subscriber</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="color: #fff;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ url('admin/newsletter/add') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label>Email Address <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" placeholder="subscriber@example.com" required style="background: #272730; color: #fff; border: 1px solid #3a3a46;">
            </div>
            <div class="form-group">
              <label>Full Name (Optional)</label>
              <input type="text" name="name" class="form-control" placeholder="John Doe" style="background: #272730; color: #fff; border: 1px solid #3a3a46;">
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid #32323c;">
            <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fa fa-check"></i> Add Subscriber</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ URL::asset('admin_assets/js/jquery.min.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      // Filter status change
      $('#status_select').change(function() {
        window.location.href = $(this).val();
      });

      // Toggle status AJAX
      $('.toggle_status_btn').click(function() {
        var subscriberId = $(this).data('id');
        var btn = $(this);

        $.ajax({
          type: 'POST',
          url: "{{ url('admin/newsletter/toggle-status') }}/" + subscriberId,
          data: {
            _token: "{{ csrf_token() }}"
          },
          success: function(response) {
            if (response.status === 'success') {
              var badgeHtml = response.new_status == 1
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Unsubscribed</span>';
              $('#status_badge_' + subscriberId).html(badgeHtml);
            }
          },
          error: function() {
            alert('Could not update status. Please try again.');
          }
        });
      });

      // SweetAlert Delete Subscriber
      $('.delete_subscriber_btn').click(function() {
        var subscriberId = $(this).data('id');

        Swal.fire({
          title: '{{ trans("words.dlt_warning") }}',
          text: "{{ trans('words.dlt_warning_text') }}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: '{{ trans("words.dlt_confirm") }}',
          cancelButtonText: "{{ trans('words.btn_cancel') }}",
          background: "#1a2234",
          color: "#fff"
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              type: 'POST',
              url: "{{ url('admin/newsletter/delete') }}/" + subscriberId,
              data: {
                _token: "{{ csrf_token() }}"
              },
              success: function(response) {
                if (response.status == 1) {
                  $('#subscriber_row_' + subscriberId).fadeOut(400, function() {
                    $(this).remove();
                  });
                  Swal.fire({
                    title: 'Deleted!',
                    text: 'Subscriber has been removed.',
                    icon: 'success',
                    background: "#1a2234",
                    color: "#fff",
                    timer: 1500,
                    showConfirmButton: false
                  });
                }
              },
              error: function() {
                Swal.fire({
                  title: 'Error!',
                  text: 'Failed to delete subscriber.',
                  icon: 'error',
                  background: "#1a2234",
                  color: "#fff"
                });
              }
            });
          }
        });
      });
    });
  </script>

@endsection
