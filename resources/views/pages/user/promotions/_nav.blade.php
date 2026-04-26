<div class="promotion-nav-tabs mb-4">
    <a href="{{ URL::to('promotions') }}" class="btn {{ request()->is('promotions') ? 'btn-danger' : 'btn-default' }}">Overview</a>
    <a href="{{ URL::to('promotions/lists') }}" class="btn {{ request()->is('promotions/lists') || request()->is('promotions/lists/*') ? 'btn-danger' : 'btn-default' }}">Email Lists</a>
    <a href="{{ URL::to('promotions/campaigns') }}" class="btn {{ request()->is('promotions/campaigns') || request()->is('promotions/campaigns/*') ? 'btn-danger' : 'btn-default' }}">Campaigns</a>
</div>

<style>
    .promotion-nav-tabs .btn {
        margin-right: 10px;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .promotion-panel,
    .promotion-card {
        background: rgba(20, 20, 20, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .promotion-stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
    }

    .promotion-stat-label,
    .promotion-help-text {
        color: rgba(255, 255, 255, 0.72);
    }

    .promotion-label {
        display: block;
        color: #fff;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .promotion-input,
    .promotion-textarea,
    .promotion-select {
        background: #2e2e32;
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    .promotion-input:focus,
    .promotion-textarea:focus,
    .promotion-select:focus {
        background: #2e2e32;
        color: #fff;
        border-color: #ff0f28;
        box-shadow: none;
    }

    .promotion-table th,
    .promotion-table td,
    .promotion-table p,
    .promotion-table span,
    .promotion-table strong {
        color: #fff;
    }
</style>
