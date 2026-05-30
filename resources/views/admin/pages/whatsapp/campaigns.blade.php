@extends("admin.admin_app")

@section("content")
@include('admin.pages.whatsapp.partials.content_styles')

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
                                <p class="m-b-0">Create, schedule, pause, resume, and monitor WhatsApp Web campaigns.</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ URL::to('admin/whatsapp/campaigns/create') }}" class="btn btn-success">
                                    <i class="fa fa-plus"></i> New Campaign
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Campaign</th>
                                        <th>List</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Protection</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($campaigns as $campaign)
                                    <tr>
                                        <td>
                                            <strong>{{ $campaign->name }}</strong>
                                            <div>{{ $campaign->scheduled_at ? 'Scheduled '.$campaign->scheduled_at->format('M d, Y H:i') : 'No schedule' }}</div>
                                        </td>
                                        <td>{{ optional($campaign->contactList)->name ?: 'List missing' }}</td>
                                        <td><span class="badge badge-{{ $campaign->getStatusBadgeClass() }}">{{ ucfirst($campaign->status) }}</span></td>
                                        <td>
                                            <div>{{ $campaign->processed_contacts }}/{{ $campaign->total_contacts }}</div>
                                            <div class="whatsapp-progress"><span style="width: {{ $campaign->getProgressPercentage() }}%;"></span></div>
                                        </td>
                                        <td>
                                            <div>Delay {{ $campaign->min_delay_seconds }}-{{ $campaign->max_delay_seconds }} sec</div>
                                            <div>Pause {{ $campaign->pause_duration_seconds }} sec after {{ $campaign->pause_after_messages }}</div>
                                            <div>Daily limit {{ $campaign->daily_limit }}</div>
                                        </td>
                                        <td>
                                            <a href="{{ URL::to('admin/whatsapp/campaigns/'.$campaign->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ URL::to('admin/whatsapp/campaigns/edit/'.$campaign->id) }}" class="btn btn-success btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">No WhatsApp campaigns yet.</td></tr>
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
    @include('admin.copyright')
</div>

@include('admin.pages.whatsapp.partials.flash')
@endsection
