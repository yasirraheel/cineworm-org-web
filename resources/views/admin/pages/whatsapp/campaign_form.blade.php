@extends("admin.admin_app")

@section("content")
@include('admin.pages.whatsapp.partials.content_styles')

@php
    $campaign = $campaign ?? null;
@endphp

<div class="content-page whatsapp-admin-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        @include('admin.pages.whatsapp.partials.nav')

                        <div class="row m-b-20">
                            <div class="col-md-8">
                                <h4 class="header-title m-t-0">{{ $page_title }}</h4>
                                <p class="m-b-0">Use only opted-in numbers. Keep pacing conservative for safer WhatsApp Web delivery.</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ URL::to('admin/whatsapp/campaigns') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Campaigns
                                </a>
                            </div>
                        </div>

                        <form method="post" action="{{ URL::to('admin/whatsapp/campaigns/save') }}">
                            @csrf
                            @if($campaign)
                                <input type="hidden" name="id" value="{{ $campaign->id }}">
                            @endif

                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="card-box">
                                        <h4 class="header-title m-t-0">Campaign Content</h4>
                                        <div class="form-group">
                                            <label>Campaign Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $campaign->name ?? '') }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile List</label>
                                            <select name="contact_list_id" class="form-control" required>
                                                <option value="">Select list</option>
                                                @foreach($lists as $list)
                                                    <option value="{{ $list->id }}" {{ (int) old('contact_list_id', $campaign->contact_list_id ?? 0) === (int) $list->id ? 'selected' : '' }}>
                                                        {{ $list->name }} ({{ $list->contacts_count }} active)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea name="message" rows="10" class="form-control" maxlength="5000" required>{{ old('message', $campaign->message ?? '') }}</textarea>
                                            <small>Available variables: @{{name}}, @{{phone}}, @{{company}}, @{{tags}}</small>
                                        </div>
                                        <div class="form-group">
                                            <label>Schedule</label>
                                            <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $campaign && $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                                            <small>Leave empty to start when launched.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="card-box">
                                        <h4 class="header-title m-t-0">Ban Protection</h4>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Batch Size</label>
                                                    <input type="number" name="batch_size" class="form-control" value="{{ old('batch_size', $campaign->batch_size ?? 10) }}" min="1" max="50" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Daily Limit</label>
                                                    <input type="number" name="daily_limit" class="form-control" value="{{ old('daily_limit', $campaign->daily_limit ?? 250) }}" min="1" max="5000" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Min Delay Sec</label>
                                                    <input type="number" name="min_delay_seconds" class="form-control" value="{{ old('min_delay_seconds', $campaign->min_delay_seconds ?? 25) }}" min="5" max="3600" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Max Delay Sec</label>
                                                    <input type="number" name="max_delay_seconds" class="form-control" value="{{ old('max_delay_seconds', $campaign->max_delay_seconds ?? 75) }}" min="5" max="3600" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Pause After</label>
                                                    <input type="number" name="pause_after_messages" class="form-control" value="{{ old('pause_after_messages', $campaign->pause_after_messages ?? 20) }}" min="1" max="500" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Pause Seconds</label>
                                                    <input type="number" name="pause_duration_seconds" class="form-control" value="{{ old('pause_duration_seconds', $campaign->pause_duration_seconds ?? 900) }}" min="60" max="86400" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Quiet Start</label>
                                                    <input type="time" name="quiet_hours_start" class="form-control" value="{{ old('quiet_hours_start', $campaign->quiet_hours_start ?? '23:00') }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Quiet End</label>
                                                    <input type="time" name="quiet_hours_end" class="form-control" value="{{ old('quiet_hours_end', $campaign->quiet_hours_end ?? '08:00') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fa fa-save"></i> Save Campaign
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.copyright')
</div>

@include('admin.pages.whatsapp.partials.flash')
@endsection
