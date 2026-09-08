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

                @if(isset($errors) && $errors->any())
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
                        <option value="all" @if($selectedUsers->isEmpty()) selected @endif>All Registered Users ({{ $total_users }} users)</option>
                        <option value="active_only">Active Users Only ({{ $active_users }} users)</option>
                        <option value="specific_users" @if($selectedUsers->isNotEmpty()) selected @endif>Specific User(s) (<span id="specific_count">{{ $selectedUsers->count() }}</span> selected)</option>
                      </select>
                    </div>
                  </div>

                  <!-- Specific Users Search and Selection Section -->
                  <div class="form-group row" id="specific_users_section" style="@if($selectedUsers->isEmpty()) display: none; @endif">
                    <label class="col-sm-3 col-form-label">Search & Select Users *</label>
                    <div class="col-sm-8">
                      
                      <!-- User Search Autocomplete Input -->
                      <div class="position-relative m-b-15">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text bg-dark border-secondary text-muted" style="border-color: #384561 !important;"><i class="fa fa-search"></i></span>
                          </div>
                          <input type="text" id="user_search_input" class="form-control" placeholder="Search user by name, email, or phone (e.g. John or user@example.com)..." autocomplete="off">
                          <div class="input-group-append" id="search_spinner" style="display: none;">
                            <span class="input-group-text bg-dark border-secondary" style="border-color: #384561 !important;"><i class="fa fa-spinner fa-spin text-primary"></i></span>
                          </div>
                        </div>

                        <!-- Search Results Dropdown -->
                        <div id="user_search_dropdown" class="list-group position-absolute w-100 shadow-lg" style="display: none; z-index: 1050; max-height: 280px; overflow-y: auto; background: #1c273c; border: 1px solid #323f5d; border-radius: 6px; top: 42px;"></div>
                      </div>

                      <!-- Selected Users Container -->
                      <div id="selected_users_container" class="p-3" style="background: rgba(255,255,255,0.03); border: 1px dashed #3a4763; border-radius: 6px; min-height: 70px;">
                        
                        <div id="no_users_selected_notice" style="@if($selectedUsers->isNotEmpty()) display: none; @endif text-align: center; color: #8a96a8; padding: 12px 0;">
                          <i class="fa fa-user-plus fa-2x m-b-5" style="opacity: 0.5;"></i><br>
                          No specific users selected yet. Search above by name or email to add recipients.
                        </div>

                        <!-- Pre-loaded or Dynamically Added Users Chips -->
                        <div id="users_chips_wrapper" class="d-flex flex-wrap">
                          @foreach($selectedUsers as $u)
                            <div class="user-chip-item d-flex align-items-center m-1 p-2" style="background: #1e283d; border: 1px solid #334366; border-radius: 6px; color: #e2e8f0; font-size: 13px;" id="user_chip_{{ $u['id'] }}">
                              <div class="avatar-mini mr-2" style="width: 32px; height: 32px; border-radius: 50%; background: #3bafda; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                {{ strtoupper(substr($u['name'], 0, 2)) }}
                              </div>
                              <div class="user-meta mr-3">
                                <div class="font-weight-bold text-white">{{ $u['name'] }} <span class="badge badge-info ml-1" style="font-size: 10px;">{{ $u['plan_name'] }}</span></div>
                                <div class="text-muted" style="font-size: 11px;"><i class="fa fa-envelope mr-1"></i>{{ $u['email'] }} @if(!empty($u['phone'])) &bull; {{ $u['phone'] }} @endif</div>
                              </div>
                              <button type="button" class="btn btn-xs btn-outline-danger btn-remove-chip ml-auto" data-id="{{ $u['id'] }}" title="Remove recipient" style="border: none; background: transparent; color: #ff5b5b; font-size: 16px; line-height: 1; padding: 2px 6px;">
                                <i class="fa fa-times-circle"></i>
                              </button>
                              <input type="hidden" name="user_ids[]" value="{{ $u['id'] }}" class="user-id-input" id="input_user_id_{{ $u['id'] }}">
                            </div>
                          @endforeach
                        </div>

                      </div>

                      <small class="form-text text-muted mt-2">
                        <i class="fa fa-info-circle text-primary"></i> You can search and add any number of users. To remove a user, click the red (×) icon.
                      </small>

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
                      <button type="button" id="btnQueueBroadcast" class="btn btn-primary waves-effect waves-light" @if($total_users == 0 && $selectedUsers->isEmpty()) disabled @endif>
                        <i class="fa fa-send"></i> <span id="btn_submit_text">@if($selectedUsers->isNotEmpty()) Send Email to Selected User(s) @else Queue & Send to Users @endif</span>
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
                            @elseif($camp->audience === 'specific_users')
                              <span class="badge badge-warning">Specific Users ({{ $camp->total_recipients }})</span>
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
                          <td colspan="8" class="text-center text-muted p-4">
                            No promotional campaigns queued or sent yet.
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

      // Audience Selection Toggle
      function updateAudienceUI() {
        var aud = $('#audience').val();
        if (aud === 'specific_users') {
          $('#specific_users_section').fadeIn(200);
          $('#btn_submit_text').text('Send Email to Selected User(s)');
        } else {
          $('#specific_users_section').fadeOut(200);
          $('#btn_submit_text').text('Queue & Send to Users');
        }
      }

      $('#audience').on('change', function() {
        updateAudienceUI();
        if ($(this).val() === 'specific_users') {
          $('#user_search_input').focus();
        }
      });

      // Helper to escape HTML characters
      function escapeHtml(text) {
        if (!text) return '';
        return $('<div>').text(text).html();
      }

      // Render Chip HTML for a selected user
      function renderUserChip(u) {
        var initials = (u.name || 'U').substring(0, 2).toUpperCase();
        var planName = u.plan_name || 'No Plan';
        var phoneText = u.phone ? (' &bull; ' + escapeHtml(u.phone)) : '';

        return `
          <div class="user-chip-item d-flex align-items-center m-1 p-2" style="background: #1e283d; border: 1px solid #334366; border-radius: 6px; color: #e2e8f0; font-size: 13px;" id="user_chip_${u.id}">
            <div class="avatar-mini mr-2" style="width: 32px; height: 32px; border-radius: 50%; background: #3bafda; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
              ${initials}
            </div>
            <div class="user-meta mr-3">
              <div class="font-weight-bold text-white">${escapeHtml(u.name)} <span class="badge badge-info ml-1" style="font-size: 10px;">${escapeHtml(planName)}</span></div>
              <div class="text-muted" style="font-size: 11px;"><i class="fa fa-envelope mr-1"></i>${escapeHtml(u.email)}${phoneText}</div>
            </div>
            <button type="button" class="btn btn-xs btn-outline-danger btn-remove-chip ml-auto" data-id="${u.id}" title="Remove recipient" style="border: none; background: transparent; color: #ff5b5b; font-size: 16px; line-height: 1; padding: 2px 6px;">
              <i class="fa fa-times-circle"></i>
            </button>
            <input type="hidden" name="user_ids[]" value="${u.id}" class="user-id-input" id="input_user_id_${u.id}">
          </div>
        `;
      }

      // Add a single user chip to the container
      function appendUserChip(u) {
        if ($('#input_user_id_' + u.id).length > 0) {
          return false;
        }
        $('#users_chips_wrapper').append(renderUserChip(u));
        $('#no_users_selected_notice').hide();
        updateSelectedUsersBadgeCount();
        return true;
      }

      // Update badge count and placeholder visibility
      function updateSelectedUsersBadgeCount() {
        var count = $('.user-id-input').length;
        $('#specific_count').text(count);
        if (count === 0) {
          $('#no_users_selected_notice').show();
        } else {
          $('#no_users_selected_notice').hide();
        }
      }

      // Remove User Chip
      $(document).on('click', '.btn-remove-chip', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $('#user_chip_' + id).remove();
        updateSelectedUsersBadgeCount();

        // If dropdown is open, update that user row
        var row = $(`.search-result-row[data-user-id="${id}"]`);
        if (row.length) {
          row.find('.search-row-cb').prop('disabled', false).prop('checked', false);
          row.find('.action-btn-area').html(`<button type="button" class="btn btn-xs btn-outline-primary btn-add-single" data-user-id="${id}" style="font-size: 11px; padding: 3px 8px;"><i class="fa fa-plus"></i> Add</button>`);
          syncSearchSelectionHeader();
        }
      });

      // Synchronize "Select All" checkbox and "Add Selected (X)" button in dropdown header
      function syncSearchSelectionHeader() {
        var unselectedAndEnabled = $('.search-row-cb:not(:disabled)');
        var checkedBoxes = $('.search-row-cb:checked');
        var checkedCount = checkedBoxes.length;

        $('#selected_search_count').text(checkedCount);
        $('#btn_add_selected_search').prop('disabled', checkedCount === 0);

        var totalAvailable = unselectedAndEnabled.length;
        $('#available_search_count').text(totalAvailable);

        if (totalAvailable > 0 && checkedCount === totalAvailable) {
          $('#check_all_search_results').prop('checked', true).prop('disabled', false);
        } else if (totalAvailable > 0) {
          $('#check_all_search_results').prop('checked', false).prop('disabled', false);
        } else {
          $('#check_all_search_results').prop('checked', false).prop('disabled', true);
        }
      }

      // User Search Autocomplete with Debounce
      var searchTimeout = null;
      var searchDropdown = $('#user_search_dropdown');
      var searchSpinner = $('#search_spinner');

      $('#user_search_input').on('keyup input', function() {
        var term = $(this).val().trim();
        clearTimeout(searchTimeout);

        if (term.length < 1) {
          searchDropdown.hide().empty();
          searchSpinner.hide();
          return;
        }

        searchSpinner.show();
        searchTimeout = setTimeout(function() {
          $.ajax({
            url: "{{ url('admin/users/promotional-email/search-users') }}",
            type: 'GET',
            data: { q: term },
            dataType: 'json',
            success: function(res) {
              searchSpinner.hide();
              renderSearchResults(res.results || [], term);
            },
            error: function() {
              searchSpinner.hide();
              searchDropdown.html('<div class="p-3 text-center text-danger"><i class="fa fa-exclamation-triangle"></i> Error searching users.</div>').show();
            }
          });
        }, 250);
      });

      // Render search results with selection controls
      function renderSearchResults(users, query) {
        if (!users || users.length === 0) {
          searchDropdown.html('<div class="p-3 text-center text-muted" style="color: #94a3b8;"><i class="fa fa-info-circle mr-1"></i> No matching users found for "' + escapeHtml(query) + '".</div>').show();
          return;
        }

        var availableCount = 0;
        var rowsHtml = '';

        users.forEach(function(u) {
          var alreadyAdded = $('#input_user_id_' + u.id).length > 0;
          if (!alreadyAdded) availableCount++;

          var initials = (u.name || 'U').substring(0, 2).toUpperCase();
          var planName = u.plan_name || 'No Plan';
          var phoneText = u.phone ? (' &bull; ' + escapeHtml(u.phone)) : '';

          rowsHtml += `
            <div class="search-result-row list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2" 
                 data-user='${JSON.stringify(u).replace(/'/g, "&apos;")}' 
                 data-user-id="${u.id}"
                 style="background: #1c273c; border-color: #2c3850; cursor: pointer; transition: background 0.15s ease;">
              <div class="d-flex align-items-center" style="flex: 1; min-width: 0;">
                <div class="custom-control custom-checkbox mr-2">
                  <input type="checkbox" class="custom-control-input search-row-cb" id="cb_user_${u.id}" data-user-id="${u.id}" ${alreadyAdded ? 'disabled' : ''}>
                  <label class="custom-control-label" for="cb_user_${u.id}" style="cursor: pointer;"></label>
                </div>
                <div class="avatar-mini mr-2 flex-shrink-0" style="width: 32px; height: 32px; border-radius: 50%; background: #3bafda; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                  ${initials}
                </div>
                <div class="user-meta text-truncate mr-2">
                  <div class="font-weight-bold text-white text-truncate" style="font-size: 13px;">
                    ${escapeHtml(u.name)} 
                    <span class="badge badge-info ml-1" style="font-size: 10px;">${escapeHtml(planName)}</span>
                    ${u.status == 1 ? '<span class="badge badge-success ml-1" style="font-size: 9px;">Active</span>' : '<span class="badge badge-danger ml-1" style="font-size: 9px;">Inactive</span>'}
                  </div>
                  <div class="text-muted text-truncate" style="font-size: 11px;">
                    <i class="fa fa-envelope mr-1"></i>${escapeHtml(u.email)}${phoneText}
                  </div>
                </div>
              </div>
              <div class="action-btn-area flex-shrink-0">
                ${alreadyAdded 
                  ? '<span class="badge badge-secondary" style="font-size: 11px; padding: 4px 7px;"><i class="fa fa-check"></i> Added</span>' 
                  : `<button type="button" class="btn btn-xs btn-outline-primary btn-add-single" data-user-id="${u.id}" style="font-size: 11px; padding: 3px 8px;"><i class="fa fa-plus"></i> Add</button>`
                }
              </div>
            </div>
          `;
        });

        var headerHtml = `
          <div class="d-flex align-items-center justify-content-between p-2 border-bottom border-secondary" style="background: #141c2b; position: sticky; top: 0; z-index: 10;">
            <div class="custom-control custom-checkbox ml-1">
              <input type="checkbox" class="custom-control-input" id="check_all_search_results" ${availableCount === 0 ? 'disabled' : ''}>
              <label class="custom-control-label text-white font-weight-bold" for="check_all_search_results" style="cursor: pointer; font-size: 12px;">
                Select All (<span id="available_search_count">${availableCount}</span>)
              </label>
            </div>
            <button type="button" id="btn_add_selected_search" class="btn btn-xs btn-primary font-weight-bold" disabled style="padding: 3px 10px; font-size: 12px;">
              <i class="fa fa-user-plus mr-1"></i> Add Selected (<span id="selected_search_count">0</span>)
            </button>
          </div>
        `;

        searchDropdown.html(headerHtml + '<div class="search-rows-container">' + rowsHtml + '</div>').show();
      }

      // Handle "Select All" Checkbox in Search Dropdown Header
      $(document).on('change', '#check_all_search_results', function() {
        var isChecked = $(this).is(':checked');
        $('.search-row-cb:not(:disabled)').prop('checked', isChecked);
        syncSearchSelectionHeader();
      });

      // Handle individual row checkbox change
      $(document).on('change', '.search-row-cb', function(e) {
        e.stopPropagation();
        syncSearchSelectionHeader();
      });

      // Handle clicking anywhere on a search result row to toggle checkbox
      $(document).on('click', '.search-result-row', function(e) {
        if ($(e.target).closest('.btn-add-single').length) {
          return;
        }
        if ($(e.target).is('.search-row-cb') || $(e.target).is('label[for^="cb_user_"]')) {
          return;
        }
        var cb = $(this).find('.search-row-cb');
        if (!cb.prop('disabled')) {
          cb.prop('checked', !cb.prop('checked')).trigger('change');
        }
      });

      // Handle single "+ Add" button click
      $(document).on('click', '.btn-add-single', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var row = $(this).closest('.search-result-row');
        var user = row.data('user');
        if (user) {
          appendUserChip(user);
          row.find('.search-row-cb').prop('checked', false).prop('disabled', true);
          row.find('.action-btn-area').html('<span class="badge badge-secondary" style="font-size: 11px; padding: 4px 7px;"><i class="fa fa-check"></i> Added</span>');
          syncSearchSelectionHeader();
        }
      });

      // Handle "+ Add Selected (X)" bulk button click
      $(document).on('click', '#btn_add_selected_search', function(e) {
        e.preventDefault();
        var checkedBoxes = $('.search-row-cb:checked');
        if (checkedBoxes.length === 0) return;

        var addedCount = 0;
        checkedBoxes.each(function() {
          var row = $(this).closest('.search-result-row');
          var user = row.data('user');
          if (user) {
            if (appendUserChip(user)) {
              addedCount++;
            }
          }
        });

        searchDropdown.hide();
        $('#user_search_input').val('');

        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 2500,
          timerProgressBar: false
        });
        Toast.fire({ icon: 'success', title: 'Added ' + addedCount + ' recipient(s)' });
      });

      // Close search dropdown on click outside
      $(document).on('click', function(e) {
        if (!$(e.target).closest('#user_search_input, #user_search_dropdown').length) {
          searchDropdown.hide();
        }
      });

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
        var subject = $('#subject').val().trim();
        var content = (typeof tinymce !== 'undefined' && tinymce.get('elm1'))
          ? tinymce.get('elm1').getContent().trim()
          : $('#elm1').val().trim();

        if (!subject) {
          Swal.fire({ icon: 'warning', title: 'Subject Required', text: 'Please enter a subject line for the email.', background: "#1a2234", color: "#fff" });
          $('#subject').focus();
          return;
        }

        if (!content) {
          Swal.fire({ icon: 'warning', title: 'Content Required', text: 'Please compose message content for the email.', background: "#1a2234", color: "#fff" });
          return;
        }

        var aud = $('#audience').val();

        if (aud === 'specific_users') {
          var userCount = $('.user-id-input').length;
          if (userCount === 0) {
            Swal.fire({
              icon: 'warning',
              title: 'No Recipients Selected',
              text: 'Please search and select at least one recipient user.',
              background: "#1a2234",
              color: "#fff"
            });
            $('#user_search_input').focus();
            return;
          }

          Swal.fire({
            title: 'Send Email to ' + userCount + ' Selected User(s)?',
            text: "The email will be dispatched to the selected user(s) immediately.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10c469',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Send Email Now!',
            cancelButtonText: "{{ trans('words.btn_cancel') }}",
            background: "#1a2234",
            color: "#fff"
          }).then((result) => {
            if (result.isConfirmed) {
              $('#promoEmailForm').submit();
            }
          });

        } else {
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
        }
      });
    });
  </script>

@endsection
