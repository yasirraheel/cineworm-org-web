@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        @include('admin.pages.promo_mail.partials.nav')

                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ URL::to('admin/promo_mail/servers') }}">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size: 20px;">
                                        <i class="fa fa-arrow-left"></i> Back To SMTP Servers
                                    </h4>
                                </a>
                            </div>
                        </div>

                        {!! Form::open(array('url' => array('admin/promo_mail/servers/save'),'name'=>'server_form','id'=>'server_form','role'=>'form')) !!}
                        <input type="hidden" name="id" value="{{ isset($server->id) ? $server->id : null }}">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Gateway Name*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="server_name" value="{{ old('server_name', isset($server->server_name) ? $server->server_name : '') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Gateway Type*</label>
                                    <div class="col-sm-9">
                                        <select name="gateway_type" class="form-control">
                                            <option value="smtp" @if(old('gateway_type', isset($server->gateway_type) ? $server->gateway_type : 'smtp')=='smtp') selected @endif>SMTP</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">From Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="from_name" value="{{ old('from_name', isset($server->from_name) ? $server->from_name : '') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Sender Email*</label>
                                    <div class="col-sm-9">
                                        <input type="email" name="sender_email" value="{{ old('sender_email', isset($server->sender_email) ? $server->sender_email : '') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Reply-To Email</label>
                                    <div class="col-sm-9">
                                        <input type="email" name="reply_to_email" value="{{ old('reply_to_email', isset($server->reply_to_email) ? $server->reply_to_email : '') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Host*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="host" value="{{ old('host', isset($server->host) ? $server->host : '') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Port*</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="port" value="{{ old('port', isset($server->port) ? $server->port : '587') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Encryption</label>
                                    <div class="col-sm-9">
                                        <select name="encryption" class="form-control">
                                            <option value="" @if(old('encryption', isset($server->encryption) ? $server->encryption : '')=='') selected @endif>None</option>
                                            <option value="ssl" @if(old('encryption', isset($server->encryption) ? $server->encryption : '')=='ssl') selected @endif>Secure encryption (SSL)</option>
                                            <option value="tls" @if(old('encryption', isset($server->encryption) ? $server->encryption : '')=='tls') selected @endif>Transport Layer Security (TLS)</option>
                                            <option value="starttls" @if(old('encryption', isset($server->encryption) ? $server->encryption : '')=='starttls') selected @endif>STARTTLS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Username*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="username" value="{{ old('username', isset($server->username) ? $server->username : '') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Password{{ isset($server->id) ? '' : '*' }}</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="smtp_password" value="" class="form-control" placeholder="{{ isset($server->id) ? 'Leave blank to keep current password' : '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">EHLO Domain</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="ehlo_domain" value="{{ old('ehlo_domain', isset($server->ehlo_domain) ? $server->ehlo_domain : '') }}" class="form-control" placeholder="mail.yourdomain.com">
                                    </div>
                                </div>

                                <hr>
                                <h4 class="header-title">Sending Limits</h4>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Min Delay Per Message</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="min_delay_per_message" value="{{ old('min_delay_per_message', isset($server->min_delay_per_message) ? $server->min_delay_per_message : 0) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Max Delay Per Message</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="max_delay_per_message" value="{{ old('max_delay_per_message', isset($server->max_delay_per_message) ? $server->max_delay_per_message : 0) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Pause After N Messages</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="pause_after_messages" value="{{ old('pause_after_messages', isset($server->pause_after_messages) ? $server->pause_after_messages : 0) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Pause Duration (Sec)</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="pause_duration" value="{{ old('pause_duration', isset($server->pause_duration) ? $server->pause_duration : 0) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Reset Counter After N Messages</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="reset_counter_after_messages" value="{{ old('reset_counter_after_messages', isset($server->reset_counter_after_messages) ? $server->reset_counter_after_messages : 0) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Max Messages Per Day</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="max_messages_per_day" value="{{ old('max_messages_per_day', isset($server->max_messages_per_day) ? $server->max_messages_per_day : 0) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Notes / Warmup Plan</label>
                                    <div class="col-sm-9">
                                        <textarea name="notes" class="form-control elm1_editor" rows="6">{{ old('notes', isset($server->notes) ? $server->notes : '') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Status</label>
                                    <div class="col-sm-4">
                                        <select name="status" class="form-control">
                                            <option value="1" @if(old('status', isset($server->status) ? $server->status : 1)==1) selected @endif>Active</option>
                                            <option value="0" @if(old('status', isset($server->status) ? $server->status : 1)==0) selected @endif>Inactive</option>
                                        </select>
                                    </div>
                                    <label class="col-sm-2 col-form-label">Default</label>
                                    <div class="col-sm-3">
                                        <div class="checkbox checkbox-primary" style="padding-top: 8px;">
                                            <input id="is_default" type="checkbox" name="is_default" value="1" @if(old('is_default', isset($server->is_default) ? $server->is_default : 0)) checked @endif>
                                            <label for="is_default">Use for new campaigns</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="offset-sm-3 col-sm-9 pl-1">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save Server</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card-box bg-light">
                                    <h4 class="header-title">Configuration Guide</h4>
                                    <ul class="m-b-0 pl-3">
                                        <li>Use a dedicated mailbox only for campaigns and promotional traffic.</li>
                                        <li>Match the sender email with a verified sending domain before mailing.</li>
                                        <li>Use SSL on port 465 or TLS/STARTTLS on 587 if your provider supports it.</li>
                                        <li>Start with low daily volume and use the sending limits to warm up safely.</li>
                                        <li>Keep one server marked default so future campaign forms can auto-select it.</li>
                                    </ul>
                                </div>

                                <div class="card-box bg-light">
                                    <h4 class="header-title">What To Configure Next</h4>
                                    <p class="text-muted">After saving the server, continue with:</p>
                                    <ol class="m-b-0 pl-3">
                                        <li>Add a sending domain and publish SPF, DKIM, and DMARC records.</li>
                                        <li>Add a tracking domain for branded open/click links.</li>
                                        <li>Only then connect this server to a promotion/campaign workflow.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

<script type="text/javascript">
    @if(Session::has('flash_message'))
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: false,
    });

    Toast.fire({
        icon: 'success',
        title: '{{ Session::get('flash_message') }}'
    });
    @endif

    @if (count($errors) > 0)
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: '<p>@foreach ($errors->all() as $error) {{$error}}<br/> @endforeach</p>',
        showConfirmButton: true,
        confirmButtonColor: '#10c469',
        background:"#1a2234",
        color:"#fff"
    });
    @endif
</script>

@endsection
