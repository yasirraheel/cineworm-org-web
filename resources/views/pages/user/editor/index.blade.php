@extends('admin.admin_app')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">

<style>
    .veditor-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 28px;
    }
    .veditor-header h3 {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }
    .veditor-header p {
        color: rgba(255,255,255,0.5);
        font-size: 13px;
        margin: 4px 0 0;
    }
    .veditor-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: linear-gradient(135deg, #fe0278, #c8005f);
        border: none;
        border-radius: 10px;
        color: #fff !important;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(254,2,120,0.3);
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .veditor-btn-primary:hover {
        background: linear-gradient(135deg, #ff2e90, #e00070);
        box-shadow: 0 6px 20px rgba(254,2,120,0.46);
        transform: translateY(-1px);
        color: #fff !important;
    }
    .veditor-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        background: transparent;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 9px;
        color: rgba(255,255,255,0.75) !important;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .veditor-btn-ghost:hover {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.38);
        color: #fff !important;
    }
    .veditor-btn-export {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        background: transparent;
        border: 1px solid rgba(16,185,129,0.4);
        border-radius: 9px;
        color: #10b981 !important;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .veditor-btn-export:hover {
        background: rgba(16,185,129,0.1);
        border-color: #10b981;
        color: #10b981 !important;
    }
    .veditor-btn-del {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: transparent;
        border: 1px solid rgba(255,15,40,0.4);
        border-radius: 9px;
        color: #ff0f28 !important;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.18s ease;
        cursor: pointer;
    }
    .veditor-btn-del:hover {
        background: rgba(255,15,40,0.1);
        border-color: #ff0f28;
        color: #ff0f28 !important;
    }
    .veditor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .veditor-card {
        background: rgba(18,18,22,0.96);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 14px;
        overflow: hidden;
        transition: border-color 0.2s ease, transform 0.2s ease;
    }
    .veditor-card:hover {
        border-color: rgba(254,2,120,0.3);
        transform: translateY(-3px);
    }
    .veditor-card-thumb {
        width: 100%;
        height: 160px;
        object-fit: cover;
        background: #111;
        display: block;
    }
    .veditor-card-thumb-placeholder {
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, rgba(254,2,120,0.08), rgba(18,18,22,0.98));
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .veditor-card-thumb-placeholder::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: repeating-linear-gradient(
            0deg,
            transparent,
            transparent 18px,
            rgba(254,2,120,0.04) 18px,
            rgba(254,2,120,0.04) 20px
        );
    }
    .veditor-card-thumb-placeholder i {
        font-size: 42px;
        color: rgba(254,2,120,0.2);
        position: relative;
        z-index: 1;
    }
    .veditor-card-body {
        padding: 16px;
    }
    .veditor-card-title {
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .veditor-card-desc {
        color: rgba(255,255,255,0.4);
        font-size: 12px;
        margin: 0 0 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .veditor-card-meta {
        display: flex;
        gap: 16px;
        margin-bottom: 12px;
        font-size: 11.5px;
        color: rgba(255,255,255,0.35);
    }
    .veditor-card-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .veditor-card-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 12px;
    }
    .veditor-status-draft {
        background: rgba(99,102,241,0.15);
        color: #818cf8;
    }
    .veditor-status-exporting {
        background: rgba(245,158,11,0.15);
        color: #f59e0b;
    }
    .veditor-status-completed {
        background: rgba(16,185,129,0.15);
        color: #10b981;
    }
    .veditor-status-failed {
        background: rgba(239,68,68,0.15);
        color: #ef4444;
    }
    .veditor-card-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .veditor-empty {
        background: rgba(18,18,22,0.96);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 16px;
        padding: 60px 24px;
        text-align: center;
    }
    .veditor-empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 24px;
        background: linear-gradient(135deg, rgba(254,2,120,0.12), rgba(254,2,120,0.04));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .veditor-empty-icon i {
        font-size: 36px;
        color: rgba(254,2,120,0.4);
    }
    .veditor-empty h4 {
        color: rgba(255,255,255,0.6);
        font-size: 17px;
        margin: 0 0 8px;
    }
    .veditor-empty p {
        color: rgba(255,255,255,0.3);
        font-size: 13.5px;
        margin: 0 0 24px;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }
    /* Film reel decorative border */
    .veditor-reel-border {
        position: relative;
        padding: 8px 0;
        margin-bottom: 20px;
    }
    .veditor-reel-border::before,
    .veditor-reel-border::after {
        content: '';
        display: block;
        height: 3px;
        background: repeating-linear-gradient(90deg,
            #fe0278 0px, #fe0278 12px,
            transparent 12px, transparent 18px
        );
        opacity: 0.3;
    }
    .veditor-reel-border::before { margin-bottom: 4px; }
    .veditor-reel-border::after { margin-top: 4px; }
</style>

        <div class="veditor-header">
            <div>
                <h3><i class="fa fa-cut" style="color:#fe0278;margin-right:8px;"></i>Film Editor</h3>
                <p>Create and manage your film editing projects. Cut, splice, and export your masterpiece.</p>
            </div>
            <a href="{{ URL::to('user/editor/create') }}" class="veditor-btn-primary">
                <i class="fa fa-plus"></i> New Project
            </a>
        </div>

        <div class="veditor-reel-border"></div>

        @if($projects->count())
            <div class="veditor-grid">
                @foreach($projects as $project)
                    <div class="veditor-card">
                        @if($project->thumbnail)
                            <img src="{{ URL::asset($project->thumbnail) }}" alt="{{ $project->title }}" class="veditor-card-thumb"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="veditor-card-thumb-placeholder" style="display:none;"><i class="fa fa-cut"></i></div>
                        @else
                            <div class="veditor-card-thumb-placeholder"><i class="fa fa-cut"></i></div>
                        @endif
                        <div class="veditor-card-body">
                            <div class="veditor-card-title">{{ $project->title }}</div>
                            @if($project->description)
                                <div class="veditor-card-desc">{{ $project->description }}</div>
                            @endif
                            <div class="veditor-card-meta">
                                <span><i class="fa fa-film"></i> {{ $project->clips->count() }} clip{{ $project->clips->count() != 1 ? 's' : '' }}</span>
                                <span><i class="fa fa-clock-o"></i>
                                    @if($project->total_duration)
                                        {{ gmdate('H:i:s', intval($project->total_duration)) }}
                                    @else
                                        0:00
                                    @endif
                                </span>
                                <span><i class="fa fa-calendar"></i> {{ $project->created_at->diffForHumans() }}</span>
                            </div>
                            @php
                                $statusClass = 'veditor-status-' . $project->status;
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'exporting' => 'Exporting…',
                                    'completed' => 'Exported',
                                    'failed' => 'Export Failed',
                                ];
                                $statusIcons = [
                                    'draft' => 'fa-pencil',
                                    'exporting' => 'fa-spinner fa-spin',
                                    'completed' => 'fa-check-circle',
                                    'failed' => 'fa-exclamation-circle',
                                ];
                            @endphp
                            <span class="veditor-card-status {{ $statusClass }}">
                                <i class="fa {{ $statusIcons[$project->status] ?? 'fa-circle' }}"></i>
                                {{ $statusLabels[$project->status] ?? ucfirst($project->status) }}
                            </span>
                            <div class="veditor-card-actions">
                                <a href="{{ URL::to('user/editor/'.$project->id) }}" class="veditor-btn-ghost">
                                    <i class="fa fa-pencil"></i> Edit
                                </a>
                                @if($project->status === 'completed' && $project->exported_file)
                                    <a href="{{ URL::to('user/editor/'.$project->id.'/download') }}" class="veditor-btn-export">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                @endif
                                <form method="POST" action="{{ URL::to('user/editor/'.$project->id.'/delete') }}" style="display:inline;"
                                      onsubmit="return confirm('Delete this project and all its clips? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="veditor-btn-del">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $projects->links() }}
        @else
            <div class="veditor-empty">
                <div class="veditor-empty-icon"><i class="fa fa-cut"></i></div>
                <h4>No editing projects yet</h4>
                <p>Start your first project to cut, splice, and assemble your film on a classic reel-to-reel editing bench.</p>
                <a href="{{ URL::to('user/editor/create') }}" class="veditor-btn-primary">
                    <i class="fa fa-plus"></i> Create Your First Project
                </a>
            </div>
        @endif

                    </div>{{-- /card-box --}}
                </div>
            </div>
        </div>
    </div>
    @include('admin.copyright')
</div>

@if(Session::has('flash_message'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, icon:'success', title:'{{ Session::get("flash_message") }}' });
    });
</script>
@endif
@if(Session::has('error_flash_message'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, icon:'error', title:'{{ Session::get("error_flash_message") }}' });
    });
</script>
@endif
@endsection
