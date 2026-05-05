@extends('site_app')

@section('head_title', ($campaign ? 'Edit Campaign' : 'Create Campaign').' | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
@php
    $defaultPromotionHtml = '<p>Hello [[name]],</p><p>Write your campaign content here.</p>';
    $defaultScheduledAt = !empty($campaign->scheduled_at)
        ? $campaign->scheduled_at->format('Y-m-d\TH:i')
        : now()->format('Y-m-d\TH:i');
    $isEdit = !empty($campaign->id);
@endphp

<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12">
        <h2>{{ $isEdit ? 'Edit Campaign' : 'Create Campaign' }}</h2>
        <nav id="breadcrumbs"><ul>
            <li><a href="{{ URL::to('/') }}">Home</a></li>
            <li><a href="{{ URL::to('promotions') }}">Promotions</a></li>
            <li><a href="{{ URL::to('promotions/campaigns') }}">Campaigns</a></li>
            <li>{{ $isEdit ? 'Edit' : 'New' }}</li>
        </ul></nav>
    </div></div></div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        @include('pages.user.promotions._nav')

        <form method="post" action="{{ URL::to('promotions/campaigns/save') }}" id="campaign-form">
            @csrf
            <input type="hidden" name="id" value="{{ $campaign->id ?? null }}">

            {{-- ── Section 1: Campaign Basics ── --}}
            <div class="promo-panel">
                <div class="promo-panel-header">
                    <div>
                        <h3><i class="fa fa-tag" style="color:#ff0f28;margin-right:8px;"></i>Campaign Details</h3>
                        <p class="promo-subtitle">Give your campaign a name, subject line, and choose which list to send to.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="promo-form-group">
                            <label class="promo-label">Campaign Name <span style="color:#ff0f28;">*</span></label>
                            <input type="text" name="name" class="promo-input form-control"
                                   value="{{ old('name', $campaign->name ?? '') }}"
                                   placeholder="e.g. Summer Promo 2025" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="promo-form-group">
                            <label class="promo-label">Email List <span style="color:#ff0f28;">*</span></label>
                            <select name="contact_list_id" class="promo-select form-control" required>
                                <option value="">— Select a List —</option>
                                @foreach($lists as $list)
                                    <option value="{{ $list->id }}" @if(old('contact_list_id', $campaign->contact_list_id ?? '') == $list->id) selected @endif>
                                        {{ $list->name }} ({{ $list->contacts_count }} contacts)
                                    </option>
                                @endforeach
                            </select>
                            @if($lists->isEmpty())
                                <p class="promo-input-hint">No lists yet. <a href="{{ URL::to('promotions/lists') }}" style="color:#ff0f28;">Create one first →</a></p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="promo-form-group">
                            <label class="promo-label">Email Subject <span style="color:#ff0f28;">*</span></label>
                            <input type="text" name="subject" class="promo-input form-control"
                                   value="{{ old('subject', $campaign->subject ?? '') }}"
                                   placeholder="e.g. 🎬 Exclusive offer for {{ '[[name]]' }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="promo-form-group">
                            <label class="promo-label">Schedule At</label>
                            <input type="datetime-local" name="scheduled_at" class="promo-input form-control"
                                   value="{{ old('scheduled_at', $defaultScheduledAt) }}">
                            <p class="promo-input-hint">Leave as now to send immediately.</p>
                        </div>
                    </div>
                </div>

                <div class="promo-form-group">
                    <label class="promo-label">Preview Text <span style="color:rgba(255,255,255,0.3);font-weight:500;">(optional)</span></label>
                    <input type="text" name="preview_text" class="promo-input form-control"
                           value="{{ old('preview_text', $campaign->preview_text ?? '') }}"
                           placeholder="Short summary shown in inbox previews…">
                </div>
            </div>

            {{-- ── Section 2: Sender Identity ── --}}
            <div class="promo-panel">
                <div class="promo-panel-header">
                    <div>
                        <h3><i class="fa fa-user-circle" style="color:#ff0f28;margin-right:8px;"></i>Sender Identity</h3>
                        <p class="promo-subtitle">
                            Set the display name recipients will see. The sending email address is handled automatically by the system.
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="promo-form-group">
                            <label class="promo-label">From Name <span style="color:#ff0f28;">*</span></label>
                            <input type="text" name="from_name" class="promo-input form-control"
                                   value="{{ old('from_name', $campaign->from_name ?? Auth::user()->name) }}"
                                   placeholder="Your name or brand name" required>
                            <p class="promo-input-hint">This is the name recipients see in their inbox.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="promo-form-group">
                            <label class="promo-label">Reply-To Email</label>
                            <input type="email" name="reply_to_email" class="promo-input form-control"
                                   value="{{ old('reply_to_email', $campaign->reply_to_email ?? Auth::user()->email) }}"
                                   placeholder="{{ Auth::user()->email }}">
                            <p class="promo-input-hint">Replies will go here. Defaults to your account email.</p>
                        </div>
                    </div>
                </div>

                {{-- Info note about auto-assigned SMTP --}}
                <div class="promo-alert promo-alert-info" style="margin-top:0;">
                    <i class="fa fa-info-circle" style="flex-shrink:0;margin-top:1px;"></i>
                    <span>The sending email address and mail server are automatically assigned by the system to ensure best deliverability.</span>
                </div>
            </div>

            {{-- ── Section 3: Email Body ── --}}
            <div class="promo-panel">
                <div class="promo-panel-header">
                    <div>
                        <h3><i class="fa fa-pencil-square-o" style="color:#ff0f28;margin-right:8px;"></i>Email Body</h3>
                        <p class="promo-subtitle">
                            Compose your email below. Use <code style="background:rgba(255,255,255,0.08);padding:2px 7px;border-radius:4px;font-size:12px;">[[name]]</code>
                            to personalise with each contact's name.
                        </p>
                    </div>
                </div>
                <div class="promo-form-group" style="margin-bottom:0;">
                    <textarea name="html_content" id="promotion-editor" class="form-control promo-textarea" rows="18">{{ old('html_content', $campaign->html_content ?? $defaultPromotionHtml) }}</textarea>
                </div>
            </div>

            {{-- ── Actions ── --}}
            <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:40px;flex-wrap:wrap;">
                <a href="{{ URL::to('promotions/campaigns') }}" class="promo-btn promo-btn-ghost">
                    <i class="fa fa-times"></i> Cancel
                </a>
                <button type="submit" class="promo-btn promo-btn-primary">
                    <i class="fa fa-check"></i> {{ $isEdit ? 'Update Campaign' : 'Save Campaign' }}
                </button>
            </div>
        </form>
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
