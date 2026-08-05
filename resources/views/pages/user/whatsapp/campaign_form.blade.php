@extends('site_app')

@section('head_title', $page_title.' | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">
                <h2>{{ $page_title }}</h2>
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ URL::to('/') }}">Home</a></li>
                        <li><a href="{{ URL::to('user/whatsapp') }}">WhatsApp</a></li>
                        <li><a href="{{ URL::to('user/whatsapp/campaigns') }}">Campaigns</a></li>
                        <li>{{ $campaign->exists ? 'Edit' : 'Create' }}</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                @include('pages.user._sidebar')
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    @include('pages.user.whatsapp._nav')

                    @include('pages.user.whatsapp._flash')

                    <div class="card mb-4" style="background:#161b26;border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:25px;">
                        <h4 style="color:#25D366;font-weight:700;margin-top:0;" class="mb-4">
                            <i class="fa fa-paper-plane"></i> {{ $page_title }}
                        </h4>

                        <form action="{{ URL::to('user/whatsapp/campaigns/save') }}" method="POST">
                            @csrf
                            @if($campaign->exists)
                                <input type="hidden" name="id" value="{{ $campaign->id }}">
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-light">Campaign Title *</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $campaign->title) }}" placeholder="e.g. Summer Promo Broadcast" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-light">Target Contact List *</label>
                                    <select name="contact_list_id" class="form-control" required>
                                        <option value="">-- Select Contact List --</option>
                                        @foreach($lists as $list)
                                            <option value="{{ $list->id }}" {{ old('contact_list_id', $campaign->contact_list_id) == $list->id ? 'selected' : '' }}>
                                                {{ $list->name }} ({{ $list->contacts()->count() }} contacts)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-light">WhatsApp Message Content *</label>
                                <textarea name="message" class="form-control" rows="5" placeholder="Type your WhatsApp message template here..." required>{{ old('message', $campaign->message) }}</textarea>
                                <div class="p-2 mt-2 rounded" style="background:rgba(255,255,255,0.04);font-size:12px;color:#94a3b8;">
                                    <strong>Available Personalization Placeholders:</strong><br>
                                    <code>{{ '{{name}}' }}</code> &bull; <code>{{ '{{phone}}' }}</code> &bull; <code>{{ '{{company}}' }}</code> &bull; <code>{{ '{{tags}}' }}</code>
                                </div>
                            </div>

                            <h5 style="color:#38bdf8;font-weight:700;" class="mt-4 mb-3"><i class="fa fa-sliders"></i> Delivery & Anti-Spam Controls</h5>

                            <div class="row mb-3">
                                <div class="col-md-3 col-6 mb-3">
                                    <label class="form-label text-light">Min Delay (sec)</label>
                                    <input type="number" name="min_delay_seconds" class="form-control" value="{{ old('min_delay_seconds', $campaign->min_delay_seconds ?: 4) }}" min="1" max="120">
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <label class="form-label text-light">Max Delay (sec)</label>
                                    <input type="number" name="max_delay_seconds" class="form-control" value="{{ old('max_delay_seconds', $campaign->max_delay_seconds ?: 12) }}" min="1" max="180">
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <label class="form-label text-light">Batch Size</label>
                                    <input type="number" name="batch_size" class="form-control" value="{{ old('batch_size', $campaign->batch_size ?: 10) }}" min="1" max="50">
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <label class="form-label text-light">Daily Limit</label>
                                    <input type="number" name="daily_limit" class="form-control" value="{{ old('daily_limit', $campaign->daily_limit ?: 500) }}" min="0">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ URL::to('user/whatsapp/campaigns') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-success px-4" style="background:#25D366;border-color:#25D366;font-weight:600;">
                                    <i class="fa fa-save"></i> Save Campaign
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
