@php
    $path = Request::path();
@endphp

<div class="whatsapp-nav">
    <a href="{{ URL::to('admin/whatsapp') }}" class="{{ $path === 'admin/whatsapp' ? 'active' : '' }}">
        <i class="fa fa-dashboard"></i> Dashboard
    </a>
    <a href="{{ URL::to('admin/whatsapp/lists') }}" class="{{ strpos($path, 'admin/whatsapp/lists') === 0 ? 'active' : '' }}">
        <i class="fa fa-address-book"></i> Mobile Lists
    </a>
    <a href="{{ URL::to('admin/whatsapp/campaigns') }}" class="{{ strpos($path, 'admin/whatsapp/campaigns') === 0 ? 'active' : '' }}">
        <i class="fa fa-send"></i> Campaigns
    </a>
</div>
