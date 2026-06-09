@extends('site_app')

@section('head_title', 'Manage Contacts — '.$list->name.' | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12">
        <h2>{{ $list->name }} — Contacts</h2>
        <nav id="breadcrumbs"><ul>
            <li><a href="{{ URL::to('/') }}">Home</a></li>
            <li><a href="{{ URL::to('promotions') }}">Promotions</a></li>
            <li><a href="{{ URL::to('promotions/lists') }}">Email Lists</a></li>
            <li>{{ Str::limit($list->name, 30) }}</li>
        </ul></nav>
    </div></div></div>
</div>

<div class="vfx-item-ptb vfx-item-info">
    <div class="container-fluid">
        <div class="profile-section">
            <div class="row">
                @include('pages.user._sidebar')
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12">
                    @include('pages.user.promotions._nav')

        <div class="row">

            {{-- ── LEFT: Add & Import ── --}}
            <div class="col-md-4" style="margin-bottom:22px;">

                {{-- Tab switcher --}}
                <div style="display:flex;gap:0;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:4px;margin-bottom:18px;">
                    <button type="button" id="tab-btn-manual" class="promo-tab-btn active"
                        onclick="switchTab('manual')" style="flex:1;border:none;background:rgba(255,15,40,0.9);color:#fff;border-radius:9px;padding:9px 0;font-size:13px;font-weight:700;cursor:pointer;transition:all 0.18s;">
                        <i class="fa fa-user-plus"></i> Add Manually
                    </button>
                    <button type="button" id="tab-btn-import" class="promo-tab-btn"
                        onclick="switchTab('import')" style="flex:1;border:none;background:transparent;color:rgba(255,255,255,0.55);border-radius:9px;padding:9px 0;font-size:13px;font-weight:700;cursor:pointer;transition:all 0.18s;">
                        <i class="fa fa-upload"></i> Import CSV
                    </button>
                </div>

                {{-- Add Manually --}}
                <div id="tab-manual" class="promo-panel" style="margin-bottom:0;">
                    <div class="promo-panel-header" style="margin-bottom:18px;">
                        <div>
                            <h3><i class="fa fa-user-plus" style="color:#ff0f28;margin-right:8px;"></i>Add Contact</h3>
                            <p class="promo-subtitle">Add a single contact to this list.</p>
                        </div>
                    </div>
                    <form method="post" action="{{ URL::to('promotions/lists/'.$list->id.'/contacts/save') }}">
                        @csrf
                        <div class="promo-form-group">
                            <label class="promo-label">Name</label>
                            <input type="text" name="name" class="promo-input form-control"
                                   value="{{ old('name') }}" placeholder="John Doe">
                        </div>
                        <div class="promo-form-group">
                            <label class="promo-label">Email <span style="color:#ff0f28;">*</span></label>
                            <input type="email" name="email" class="promo-input form-control"
                                   value="{{ old('email') }}" placeholder="john@example.com" required>
                        </div>
                        <div class="promo-form-group">
                            <label class="promo-label">Company</label>
                            <input type="text" name="company" class="promo-input form-control"
                                   value="{{ old('company') }}" placeholder="Studio / Company name">
                        </div>
                        <div class="promo-form-group">
                            <label class="promo-label">Tags</label>
                            <input type="text" name="tags" class="promo-input form-control"
                                   value="{{ old('tags') }}" placeholder="vip, investors, press">
                            <p class="promo-input-hint">Comma-separated. Used for filtering.</p>
                        </div>
                        <button type="submit" class="promo-btn promo-btn-primary" style="width:100%;justify-content:center;">
                            <i class="fa fa-save"></i> Save Contact
                        </button>
                    </form>
                </div>

                {{-- Import CSV --}}
                <div id="tab-import" class="promo-panel" style="display:none;margin-bottom:0;">
                    <div class="promo-panel-header" style="margin-bottom:18px;">
                        <div>
                            <h3><i class="fa fa-upload" style="color:#ff0f28;margin-right:8px;"></i>Import Contacts</h3>
                            <p class="promo-subtitle">Upload a CSV or paste rows. Format: <code style="background:rgba(255,255,255,0.08);padding:2px 6px;border-radius:4px;font-size:11px;">name, email, company, tags</code></p>
                        </div>
                    </div>
                    <form method="post"
                          action="{{ URL::to('promotions/lists/'.$list->id.'/contacts/import') }}"
                          enctype="multipart/form-data">
                        @csrf

                        {{-- Dropzone --}}
                        <div class="promo-form-group">
                            <label class="promo-label">CSV File</label>
                            <label class="promo-dropzone-label" for="promoDropzoneInput">
                                <i class="fa fa-cloud-upload"></i>
                                <span>Click to browse or drag & drop your CSV here</span>
                            </label>
                            <input type="file" name="csv_file" accept=".csv,text/csv"
                                   id="promoDropzoneInput" class="promo-dropzone-input">
                            <div id="promoDropzoneName" class="promo-dropzone-filename"></div>
                        </div>

                        <div style="text-align:center;color:rgba(255,255,255,0.3);font-size:12px;margin:6px 0 18px;">— or paste rows below —</div>

                        <div class="promo-form-group">
                            <label class="promo-label">Paste CSV Rows</label>
                            <textarea name="import_source" rows="7" class="promo-textarea form-control"
                                      placeholder="name,email,company,tags&#10;John Doe,john@example.com,Studio X,investor&#10;Jane Smith,jane@example.com,,vip"></textarea>
                        </div>

                        <div style="display:flex;gap:10px;flex-wrap:wrap;">
                            <button type="submit" class="promo-btn promo-btn-primary" style="flex:1;justify-content:center;">
                                <i class="fa fa-upload"></i> Import
                            </button>
                            <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts/sample-file') }}"
                               class="promo-btn promo-btn-ghost" style="white-space:nowrap;">
                                <i class="fa fa-download"></i> Sample CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── RIGHT: Contact Table ── --}}
            <div class="col-md-8">
                <div class="promo-panel">
                    <div class="promo-panel-header">
                        <div>
                            <h3>List Contacts</h3>
                            <p class="promo-subtitle">
                                <span class="promo-badge promo-badge-info" style="margin-right:8px;">
                                    <i class="fa fa-users" style="font-size:10px;"></i>
                                    {{ $contacts->total() ?? $contacts->count() }}
                                </span>
                                contacts in <strong style="color:#fff;">{{ $list->name }}</strong>
                            </p>
                        </div>
                        <div class="promo-panel-actions">
                            <a href="{{ URL::to('promotions/campaigns/create') }}" class="promo-btn promo-btn-primary promo-btn-sm">
                                <i class="fa fa-paper-plane"></i> Create Campaign
                            </a>
                        </div>
                    </div>
                    <div class="promo-table-wrap">
                        <table class="promo-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Company</th>
                                    <th>Tags</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $contact)
                                    <tr>
                                        <td style="color:rgba(255,255,255,0.85);font-weight:500;">{{ $contact->name ?: '—' }}</td>
                                        <td style="color:rgba(255,255,255,0.55);font-size:13px;">{{ $contact->email }}</td>
                                        <td style="color:rgba(255,255,255,0.45);font-size:13px;">{{ $contact->company ?: '—' }}</td>
                                        <td>
                                            @if($contact->tags)
                                                @foreach(explode(',', $contact->tags) as $tag)
                                                    <span class="promo-badge promo-badge-default" style="margin-right:4px;margin-bottom:4px;font-size:10.5px;padding:3px 9px;">{{ trim($tag) }}</span>
                                                @endforeach
                                            @else
                                                <span style="color:rgba(255,255,255,0.25);">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="promo-table-actions">
                                                <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts/delete/'.$contact->id) }}"
                                                   class="promo-btn promo-btn-danger-ghost promo-btn-sm"
                                                   onclick="return confirm('Delete this contact?');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="promo-table-empty">
                                            <i class="fa fa-users" style="font-size:32px;display:block;margin-bottom:14px;opacity:0.18;"></i>
                                            No contacts yet.<br>
                                            <span style="font-size:13px;">Use the panel on the left to add contacts manually or import via CSV.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('_particles.pagination', ['paginator' => $contacts])
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    var manual = document.getElementById('tab-manual');
    var importPanel = document.getElementById('tab-import');
    var btnManual = document.getElementById('tab-btn-manual');
    var btnImport = document.getElementById('tab-btn-import');

    if (tab === 'manual') {
        manual.style.display = '';
        importPanel.style.display = 'none';
        btnManual.style.background = 'rgba(255,15,40,0.9)';
        btnManual.style.color = '#fff';
        btnImport.style.background = 'transparent';
        btnImport.style.color = 'rgba(255,255,255,0.55)';
    } else {
        manual.style.display = 'none';
        importPanel.style.display = '';
        btnImport.style.background = 'rgba(255,15,40,0.9)';
        btnImport.style.color = '#fff';
        btnManual.style.background = 'transparent';
        btnManual.style.color = 'rgba(255,255,255,0.55)';
    }
}
</script>

@include('pages.user.promotions._flash')
@endsection
