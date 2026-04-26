@extends('site_app')

@section('head_title', ($campaign ? 'Edit Campaign' : 'Create Campaign').' | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12"><h2>{{ $campaign ? 'Edit Campaign' : 'Create Campaign' }}</h2></div></div></div>
</div>
<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        @include('pages.user.promotions._nav')

        <div class="promotion-panel">
            <form method="post" action="{{ URL::to('promotions/campaigns/save') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $campaign->id ?? null }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="promotion-label">Campaign Name</label>
                            <input type="text" name="name" class="form-control promotion-input" value="{{ old('name', $campaign->name ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="promotion-label">Email List</label>
                            <select name="contact_list_id" class="form-control promotion-select" required>
                                <option value="">Select List</option>
                                @foreach($lists as $list)
                                    <option value="{{ $list->id }}" @if(old('contact_list_id', $campaign->contact_list_id ?? '') == $list->id) selected @endif>{{ $list->name }} ({{ $list->contacts_count }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="promotion-label">SMTP Server</label>
                            <select name="smtp_server_id" class="form-control promotion-select" required>
                                <option value="">Select SMTP Server</option>
                                @foreach($servers as $server)
                                    <option value="{{ $server->id }}" @if(old('smtp_server_id', $campaign->smtp_server_id ?? '') == $server->id) selected @endif>{{ $server->server_name }}@if($server->is_default) (Default) @endif</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="promotion-label">Sending Domain</label>
                            <select name="sending_domain_id" class="form-control promotion-select">
                                <option value="">Select Verified Sending Domain</option>
                                @foreach($sendingDomains as $domain)
                                    <option value="{{ $domain->id }}" @if(old('sending_domain_id', $campaign->sending_domain_id ?? '') == $domain->id) selected @endif>{{ $domain->domain }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="promotion-label">From Name</label>
                            <input type="text" name="from_name" class="form-control promotion-input" value="{{ old('from_name', $campaign->from_name ?? Auth::user()->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="promotion-label">From Email</label>
                            <input type="email" name="from_email" class="form-control promotion-input" value="{{ old('from_email', $campaign->from_email ?? Auth::user()->email) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="promotion-label">Reply To Email</label>
                            <input type="email" name="reply_to_email" class="form-control promotion-input" value="{{ old('reply_to_email', $campaign->reply_to_email ?? Auth::user()->email) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="promotion-label">Tracking Domain</label>
                            <select name="tracking_domain_id" class="form-control promotion-select">
                                <option value="">Optional Tracking Domain</option>
                                @foreach($trackingDomains as $domain)
                                    <option value="{{ $domain->id }}" @if(old('tracking_domain_id', $campaign->tracking_domain_id ?? '') == $domain->id) selected @endif>{{ $domain->domain }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="promotion-label">Subject</label>
                            <input type="text" name="subject" class="form-control promotion-input" value="{{ old('subject', $campaign->subject ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="promotion-label">Schedule At</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control promotion-input" value="{{ old('scheduled_at', !empty($campaign->scheduled_at) ? $campaign->scheduled_at->format('Y-m-d\\TH:i') : '') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="promotion-label">Preview Text</label>
                    <input type="text" name="preview_text" class="form-control promotion-input" value="{{ old('preview_text', $campaign->preview_text ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="promotion-label">Email Body</label>
                    <textarea name="html_content" id="promotion-editor" class="form-control promotion-textarea" rows="18">{{ old('html_content', $campaign->html_content ?? '<p>Hello {{name}},</p><p>Write your campaign content here.</p>') }}</textarea>
                </div>

                <div class="text-right">
                    <a href="{{ URL::to('promotions/campaigns') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-danger">{{ $campaign ? 'Update Campaign' : 'Save Campaign' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ URL::asset('admin_assets/plugins/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript">
    tinymce.init({
        selector: '#promotion-editor',
        height: 520,
        menubar: true,
        plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media | code fullscreen preview | help'
    });
</script>
@include('pages.user.promotions._flash')
@endsection
