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

    .promotion-panel h3,
    .promotion-panel h4,
    .promotion-panel p,
    .promotion-panel strong,
    .promotion-panel label,
    .promotion-card h3,
    .promotion-card h4,
    .promotion-card p,
    .promotion-card strong,
    .promotion-card label {
        color: #fff;
    }

    .promotion-panel .form-group {
        margin-bottom: 22px;
    }

    .promotion-input,
    .promotion-textarea,
    .promotion-select {
        background: #2e2e32;
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
        border-radius: 10px;
        min-height: 48px;
        padding: 12px 16px;
        width: 100%;
    }

    .promotion-input:focus,
    .promotion-textarea:focus,
    .promotion-select:focus {
        background: #2e2e32;
        color: #fff;
        border-color: #ff0f28;
        box-shadow: none;
    }

    select.promotion-select.form-control {
        height: 48px !important;
        line-height: 24px !important;
        padding-top: 11px !important;
        padding-bottom: 11px !important;
        padding-right: 42px !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image:
            linear-gradient(45deg, transparent 50%, #ffffff 50%),
            linear-gradient(135deg, #ffffff 50%, transparent 50%);
        background-position:
            calc(100% - 20px) calc(50% - 3px),
            calc(100% - 14px) calc(50% - 3px);
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
    }

    select.promotion-select.form-control[multiple] {
        min-height: 180px !important;
        height: auto !important;
        background-image: none;
        padding-right: 16px !important;
    }

    .promotion-select option {
        background: #2e2e32;
        color: #fff;
    }

    .promotion-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .promotion-panel .btn {
        border-radius: 10px;
    }

    .promotion-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .promotion-header h3,
    .promotion-header h4 {
        margin-top: 0;
        margin-bottom: 6px;
    }

    .promotion-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
    }

    .promotion-inline-form {
        display: inline-block;
        margin: 0;
    }

    .promotion-meta p {
        margin-bottom: 12px;
    }

    .promotion-alert {
        margin-top: 18px;
        border-radius: 10px;
    }

    .promotion-panel .row {
        margin-left: -12px;
        margin-right: -12px;
    }

    .promotion-panel .row > [class*="col-"] {
        padding-left: 12px;
        padding-right: 12px;
    }

    .promotion-table th,
    .promotion-table td,
    .promotion-table p,
    .promotion-table span,
    .promotion-table strong {
        color: #fff;
    }

    .promotion-table tbody tr td {
        vertical-align: middle;
    }

    .promotion-table td .promotion-help-text {
        margin-top: 6px;
    }

    .promotion-table .label {
        display: inline-block;
        min-width: 82px;
        text-align: center;
        border-radius: 999px;
        padding: 7px 12px;
    }

    .tox-tinymce {
        border-radius: 14px !important;
        overflow: hidden !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .tox .tox-toolbar,
    .tox .tox-toolbar__primary,
    .tox .tox-menubar {
        background: #f5f5f5 !important;
    }

    .tox .tox-edit-area__iframe,
    .tox .tox-edit-area {
        background: #ffffff !important;
    }

    @media only screen and (max-width: 991px) {
        .promotion-nav-tabs .btn {
            width: 100%;
            margin-right: 0;
        }

        .promotion-header {
            flex-direction: column;
        }

        .promotion-actions {
            width: 100%;
            justify-content: stretch;
        }

        .promotion-actions .btn,
        .promotion-actions .promotion-inline-form {
            width: 100%;
        }

        .promotion-actions .promotion-inline-form .btn {
            width: 100%;
        }

        .promotion-panel,
        .promotion-card {
            padding: 18px;
        }
    }
</style>

<script type="text/javascript">
    (function () {
        function normalizePromotionSelects() {
            var selects = document.querySelectorAll('select.promotion-select');

            selects.forEach(function (select) {
                if (!select.hasAttribute('multiple')) {
                    select.removeAttribute('size');
                    select.size = 1;
                }

                var next = select.nextElementSibling;
                if (next && next.classList && next.classList.contains('nice-select')) {
                    next.parentNode.removeChild(next);
                }

                select.style.display = 'block';
            });
        }

        document.addEventListener('DOMContentLoaded', normalizePromotionSelects);
        window.addEventListener('load', normalizePromotionSelects);
        setTimeout(normalizePromotionSelects, 300);
        setTimeout(normalizePromotionSelects, 1200);
        setTimeout(normalizePromotionSelects, 3000);
    })();
</script>
