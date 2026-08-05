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

<style>
/* Modern Dark Modal & High Contrast Input Design System */
.modal-content {
    background-color: #141a26 !important;
    color: #f8fafc !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 12px !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6) !important;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    padding: 18px 24px !important;
}

.modal-body {
    padding: 24px !important;
}

.modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
    padding: 16px 24px !important;
}

/* Dark Input Fields with Readable High Contrast */
.modal .form-control, 
.modal input[type="text"], 
.modal input[type="number"], 
.modal input[type="file"], 
.modal textarea, 
.modal select {
    background-color: #0d121c !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    transition: all 0.2s ease-in-out !important;
}

.modal .form-control:focus, 
.modal input:focus, 
.modal textarea:focus, 
.modal select:focus {
    background-color: #121824 !important;
    color: #ffffff !important;
    border-color: #25D366 !important;
    box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.25) !important;
    outline: none !important;
}

/* High Contrast Placeholders */
.modal .form-control::placeholder, 
.modal input::placeholder, 
.modal textarea::placeholder {
    color: #94a3b8 !important;
    opacity: 1 !important;
}

.modal label, .modal .form-label {
    color: #e2e8f0 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    margin-bottom: 6px !important;
}

.modal .text-muted {
    color: #94a3b8 !important;
}
</style>
