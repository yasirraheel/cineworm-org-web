<div class="promo-sub-nav mb-4" style="display:flex;gap:10px;background:rgba(255,255,255,0.03);padding:10px 15px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);">
    <a href="{{ URL::to('user/whatsapp') }}" class="btn btn-sm {{ Request::is('user/whatsapp') ? 'btn-success' : 'btn-outline-secondary' }}" style="font-weight:600;">
        <i class="fa fa-dashboard"></i> Dashboard
    </a>
    <a href="{{ URL::to('user/whatsapp/lists') }}" class="btn btn-sm {{ Request::is('user/whatsapp/lists*') ? 'btn-success' : 'btn-outline-secondary' }}" style="font-weight:600;">
        <i class="fa fa-list-ul"></i> Contact Lists
    </a>
    <a href="{{ URL::to('user/whatsapp/campaigns') }}" class="btn btn-sm {{ Request::is('user/whatsapp/campaigns*') ? 'btn-success' : 'btn-outline-secondary' }}" style="font-weight:600;">
        <i class="fa fa-paper-plane"></i> Campaigns
    </a>
</div>
