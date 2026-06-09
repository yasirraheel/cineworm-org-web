@extends('site_app')

@section('head_title', 'Email Lists | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12">
        <h2>Email Lists</h2>
        <nav id="breadcrumbs"><ul>
            <li><a href="{{ URL::to('/') }}">Home</a></li>
            <li><a href="{{ URL::to('promotions') }}">Promotions</a></li>
            <li>Email Lists</li>
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
            {{-- ── Create List ── --}}
            <div class="col-md-4">
                <div class="promo-panel" style="position:sticky;top:20px;">
                    <div class="promo-panel-header" style="margin-bottom:20px;">
                        <div>
                            <h3><i class="fa fa-plus-circle" style="color:#ff0f28;margin-right:8px;"></i>Create New List</h3>
                            <p class="promo-subtitle">Lists group your contacts. Create a list first, then add contacts manually or import via CSV.</p>
                        </div>
                    </div>
                    <form method="post" action="{{ URL::to('promotions/lists/save') }}">
                        @csrf
                        <div class="promo-form-group">
                            <label class="promo-label">List Name <span style="color:#ff0f28;">*</span></label>
                            <input type="text" name="name" class="promo-input form-control"
                                   value="{{ old('name') }}" placeholder="e.g. Film Investors" required>
                        </div>
                        <div class="promo-form-group">
                            <label class="promo-label">Description <span style="color:rgba(255,255,255,0.3);font-weight:500;">(optional)</span></label>
                            <textarea name="description" class="promo-textarea form-control" rows="3"
                                      placeholder="What is this list for?">{{ old('description') }}</textarea>
                        </div>
                        <button type="submit" class="promo-btn promo-btn-primary" style="width:100%;justify-content:center;">
                            <i class="fa fa-save"></i> Create List
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Lists Table ── --}}
            <div class="col-md-8">
                <div class="promo-panel">
                    <div class="promo-panel-header">
                        <div>
                            <h3>Your Lists</h3>
                            <p class="promo-subtitle">Open a list to add or import contacts, then use it in a campaign.</p>
                        </div>
                    </div>
                    <div class="promo-table-wrap">
                        <table class="promo-table">
                            <thead>
                                <tr>
                                    <th>List Name</th>
                                    <th>Contacts</th>
                                    <th>Description</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lists as $list)
                                    <tr>
                                        <td>
                                            <strong style="color:#fff;">{{ $list->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="promo-badge promo-badge-info">
                                                <i class="fa fa-users" style="font-size:10px;"></i>
                                                {{ $list->contacts_count }}
                                            </span>
                                        </td>
                                        <td style="color:rgba(255,255,255,0.45);font-size:13px;max-width:180px;">{{ $list->description ?: '—' }}</td>
                                        <td>
                                            <div class="promo-table-actions">
                                                <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts') }}"
                                                   class="promo-btn promo-btn-primary promo-btn-sm">
                                                    <i class="fa fa-users"></i> Manage Contacts
                                                </a>
                                                <a href="{{ URL::to('promotions/lists/'.$list->id.'/contacts/sample-file') }}"
                                                   class="promo-btn promo-btn-ghost promo-btn-sm">
                                                    <i class="fa fa-download"></i> Sample CSV
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="promo-table-empty">
                                            <i class="fa fa-list-ul" style="font-size:32px;display:block;margin-bottom:14px;opacity:0.18;"></i>
                                            No lists yet. Create your first list on the left.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('_particles.pagination', ['paginator' => $lists])
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.user.promotions._flash')
@endsection
