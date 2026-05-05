<style>
    /* =============================================
       PROMOTION SERVICES — PROFESSIONAL UI SYSTEM
       ============================================= */

    /* ── Page Layout ── */
    .promo-layout {
        display: flex;
        gap: 28px;
        align-items: flex-start;
    }

    .promo-sidebar {
        flex: 0 0 220px;
        min-width: 0;
    }

    .promo-main {
        flex: 1 1 0;
        min-width: 0;
    }

    /* ── Sidebar Navigation ── */
    .promo-sidebar-nav {
        background: rgba(18, 18, 22, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 16px;
        padding: 8px;
        margin-bottom: 20px;
    }

    .promo-sidebar-nav-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.38);
        padding: 10px 14px 6px;
    }

    .promo-sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 14px;
        border-radius: 10px;
        color: rgba(255, 255, 255, 0.72);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.18s ease;
        margin-bottom: 2px;
    }

    .promo-sidebar-nav a i {
        font-size: 15px;
        width: 18px;
        text-align: center;
        flex-shrink: 0;
    }

    .promo-sidebar-nav a:hover {
        background: rgba(255, 15, 40, 0.12);
        color: #fff;
    }

    .promo-sidebar-nav a.active {
        background: linear-gradient(135deg, #ff0f28 0%, #c8001f 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 16px rgba(255, 15, 40, 0.35);
    }

    /* ── Stat Cards ── */
    .promo-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .promo-stat-card {
        background: rgba(18, 18, 22, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 16px;
        padding: 22px 20px;
        position: relative;
        overflow: hidden;
        transition: border-color 0.2s ease, transform 0.2s ease;
    }

    .promo-stat-card:hover {
        border-color: rgba(255, 15, 40, 0.35);
        transform: translateY(-2px);
    }

    .promo-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #ff0f28, transparent);
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .promo-stat-card:hover::before {
        opacity: 1;
    }

    .promo-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(255, 15, 40, 0.14);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        font-size: 17px;
        color: #ff0f28;
    }

    .promo-stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #fff;
        line-height: 1;
        margin-bottom: 4px;
        letter-spacing: -0.03em;
    }

    .promo-stat-label {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.45);
    }

    /* ── Panel (card wrapper) ── */
    .promo-panel {
        background: rgba(18, 18, 22, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 22px;
    }

    /* ── Panel Header ── */
    .promo-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .promo-panel-header h3,
    .promo-panel-header h4 {
        margin: 0 0 4px 0;
        color: #fff;
        font-size: 17px;
        font-weight: 700;
    }

    .promo-panel-header .promo-subtitle {
        color: rgba(255, 255, 255, 0.5);
        font-size: 13px;
        margin: 0;
    }

    .promo-panel-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* ── Section Divider ── */
    .promo-divider {
        border: none;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
        margin: 22px 0;
    }

    /* ── Buttons ── */
    .promo-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.03em;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: all 0.18s ease;
        white-space: nowrap;
    }

    .promo-btn-primary {
        background: linear-gradient(135deg, #ff0f28 0%, #c8001f 100%);
        color: #fff !important;
        box-shadow: 0 4px 14px rgba(255, 15, 40, 0.32);
    }

    .promo-btn-primary:hover {
        background: linear-gradient(135deg, #ff2e44 0%, #e0001f 100%);
        box-shadow: 0 6px 20px rgba(255, 15, 40, 0.46);
        transform: translateY(-1px);
        color: #fff !important;
    }

    .promo-btn-ghost {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: rgba(255, 255, 255, 0.8) !important;
    }

    .promo-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.38);
        color: #fff !important;
    }

    .promo-btn-danger-ghost {
        background: transparent;
        border: 1px solid rgba(255, 15, 40, 0.5);
        color: #ff0f28 !important;
    }

    .promo-btn-danger-ghost:hover {
        background: rgba(255, 15, 40, 0.12);
        border-color: #ff0f28;
        color: #ff0f28 !important;
    }

    .promo-btn-sm {
        padding: 6px 14px;
        font-size: 12px;
        border-radius: 8px;
    }

    .promo-btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff !important;
    }

    .promo-btn-warning:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #fff !important;
    }

    /* ── Form Elements ── */
    .promo-form-group {
        margin-bottom: 20px;
    }

    .promo-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.55);
        margin-bottom: 8px;
    }

    .promo-input,
    .promo-select,
    .promo-textarea {
        width: 100% !important;
        background: #1e1e24 !important;
        background-color: #1e1e24 !important;
        border: 1px solid rgba(255, 255, 255, 0.13) !important;
        border-radius: 10px !important;
        color: #ffffff !important;
        font-size: 14px !important;
        padding: 12px 16px !important;
        transition: border-color 0.18s ease, box-shadow 0.18s ease !important;
        box-sizing: border-box !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .promo-input:focus,
    .promo-input:active,
    .promo-select:focus,
    .promo-select:active,
    .promo-textarea:focus,
    .promo-textarea:active {
        border-color: #ff0f28 !important;
        background: #1e1e24 !important;
        background-color: #1e1e24 !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(255, 15, 40, 0.18) !important;
        outline: none !important;
    }

    /* Override Bootstrap .form-control focus */
    .promo-input.form-control,
    .promo-select.form-control,
    .promo-textarea.form-control {
        background: #1e1e24 !important;
        background-color: #1e1e24 !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.13) !important;
        box-shadow: none !important;
    }

    .promo-input.form-control:focus,
    .promo-select.form-control:focus,
    .promo-textarea.form-control:focus {
        background: #1e1e24 !important;
        background-color: #1e1e24 !important;
        color: #ffffff !important;
        border-color: #ff0f28 !important;
        box-shadow: 0 0 0 3px rgba(255, 15, 40, 0.18) !important;
    }

    /* Browser autofill override */
    .promo-input:-webkit-autofill,
    .promo-input:-webkit-autofill:hover,
    .promo-input:-webkit-autofill:focus,
    .promo-select:-webkit-autofill,
    .promo-textarea:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 1000px #1e1e24 inset !important;
        -webkit-text-fill-color: #ffffff !important;
        caret-color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.13) !important;
    }

    .promo-input::placeholder,
    .promo-textarea::placeholder {
        color: rgba(255, 255, 255, 0.3) !important;
    }

    .promo-select {
        height: 46px !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' fill='none'%3E%3Cpath d='M1 1l5 5 5-5' stroke='rgba(255,255,255,0.5)' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 16px center !important;
        background-color: #1e1e24 !important;
        padding-right: 42px !important;
        cursor: pointer !important;
    }

    .promo-select option {
        background: #1a1a1e;
        color: #fff;
    }

    select.promo-select[multiple] {
        height: auto !important;
        min-height: 160px;
        background-image: none;
        padding-right: 16px;
    }

    .promo-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .promo-input-hint {
        font-size: 11.5px;
        color: rgba(255, 255, 255, 0.38);
        margin-top: 6px;
    }

    /* ── Table ── */
    .promo-table-wrap {
        overflow-x: auto;
        border-radius: 12px;
    }

    .promo-table {
        width: 100%;
        border-collapse: collapse;
    }

    .promo-table thead tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .promo-table th {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.4);
        padding: 12px 16px;
        text-align: left;
        white-space: nowrap;
    }

    .promo-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.85);
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    }

    .promo-table tbody tr:last-child td {
        border-bottom: none;
    }

    .promo-table tbody tr {
        transition: background 0.15s ease;
    }

    .promo-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    .promo-table-name strong {
        display: block;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
    }

    .promo-table-sub {
        color: rgba(255, 255, 255, 0.45);
        font-size: 12px;
        margin-top: 3px;
    }

    .promo-table-empty {
        text-align: center;
        padding: 48px 24px !important;
        color: rgba(255, 255, 255, 0.3) !important;
        font-size: 14px;
    }

    .promo-table-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: nowrap;
    }

    /* ── Badges ── */
    .promo-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: capitalize;
    }

    .promo-badge-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .promo-badge-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }

    .promo-badge-success .promo-badge-dot {
        background: #10b981;
    }

    .promo-badge-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .promo-badge-danger .promo-badge-dot {
        background: #ef4444;
    }

    .promo-badge-warning {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }

    .promo-badge-warning .promo-badge-dot {
        background: #f59e0b;
    }

    .promo-badge-info {
        background: rgba(99, 102, 241, 0.15);
        color: #818cf8;
    }

    .promo-badge-info .promo-badge-dot {
        background: #818cf8;
    }

    .promo-badge-default {
        background: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.6);
    }

    .promo-badge-default .promo-badge-dot {
        background: rgba(255, 255, 255, 0.4);
    }

    /* ── Progress Bar ── */
    .promo-progress-bar-wrap {
        background: rgba(255, 255, 255, 0.07);
        border-radius: 999px;
        height: 6px;
        overflow: hidden;
        min-width: 80px;
    }

    .promo-progress-bar {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #ff0f28, #ff6b7a);
        transition: width 0.4s ease;
    }

    /* ── Meta Grid ── */
    .promo-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0;
    }

    .promo-meta-item {
        padding: 14px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .promo-meta-item:nth-child(odd) {
        padding-right: 24px;
    }

    .promo-meta-item:nth-child(even) {
        padding-left: 24px;
        border-left: 1px solid rgba(255, 255, 255, 0.05);
    }

    .promo-meta-key {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: rgba(255, 255, 255, 0.38);
        margin-bottom: 4px;
    }

    .promo-meta-val {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.82);
        font-weight: 500;
    }

    /* ── Alert ── */
    .promo-alert {
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 13.5px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 20px;
    }

    .promo-alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.22);
        color: #fca5a5;
    }

    .promo-alert-info {
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.22);
        color: #a5b4fc;
    }

    /* ── CSV Drop Zone ── */
    .promo-dropzone-label {
        display: block;
        border: 2px dashed rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 28px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.18s ease, background 0.18s ease;
    }

    .promo-dropzone-label:hover {
        border-color: rgba(255, 15, 40, 0.5);
        background: rgba(255, 15, 40, 0.04);
    }

    .promo-dropzone-label i {
        font-size: 28px;
        color: rgba(255, 255, 255, 0.25);
        display: block;
        margin-bottom: 10px;
    }

    .promo-dropzone-label span {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.45);
    }

    .promo-dropzone-input {
        display: none;
    }

    .promo-dropzone-filename {
        font-size: 12.5px;
        color: #10b981;
        margin-top: 8px;
        display: none;
    }

    /* ── TinyMCE Dark Wrap ── */
    .tox-tinymce {
        border-radius: 12px !important;
        overflow: hidden !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* ── Responsive ── */
    @media only screen and (max-width: 991px) {
        .promo-layout {
            flex-direction: column;
        }

        .promo-sidebar {
            flex: none;
            width: 100%;
        }

        .promo-sidebar-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 10px;
        }

        .promo-sidebar-nav-label {
            display: none;
        }

        .promo-sidebar-nav a {
            flex: 1 1 auto;
            justify-content: center;
            margin-bottom: 0;
            padding: 9px 14px;
            font-size: 13px;
        }

        .promo-stat-grid {
            grid-template-columns: 1fr 1fr;
        }

        .promo-panel {
            padding: 20px;
        }

        .promo-meta-grid {
            grid-template-columns: 1fr;
        }

        .promo-meta-item:nth-child(even) {
            border-left: none;
            padding-left: 0;
        }
    }

    @media only screen and (max-width: 575px) {
        .promo-stat-grid {
            grid-template-columns: 1fr 1fr;
        }

        .promo-panel-header {
            flex-direction: column;
        }

        .promo-panel-actions {
            width: 100%;
        }

        .promo-panel-actions .promo-btn {
            flex: 1;
            justify-content: center;
        }
    }

    /* ── Legacy Bootstrap compat shims ── */
    .promo-panel h3,
    .promo-panel h4,
    .promo-panel h5,
    .promo-panel p,
    .promo-panel strong,
    .promo-panel label,
    .promo-stat-card h3,
    .promo-stat-card p {
        color: #fff;
    }
</style>

@php
    $promoRoutes = [
        ['url' => 'promotions',          'label' => 'Overview',      'icon' => 'fa fa-th-large'],
        ['url' => 'promotions/lists',     'label' => 'Email Lists',   'icon' => 'fa fa-list-ul'],
        ['url' => 'promotions/campaigns', 'label' => 'Campaigns',     'icon' => 'fa fa-paper-plane'],
    ];
@endphp

<nav class="promo-sidebar-nav mb-4" id="promo-sidebar-nav">
    <div class="promo-sidebar-nav-label">Navigation</div>
    @foreach($promoRoutes as $route)
        <a href="{{ URL::to($route['url']) }}"
           class="{{ (request()->is($route['url']) || (str_starts_with(request()->path(), $route['url'].'/') && $route['url'] !== 'promotions')) || ($route['url'] === 'promotions' && request()->is('promotions')) ? 'active' : '' }}">
            <i class="{{ $route['icon'] }}"></i>
            {{ $route['label'] }}
        </a>
    @endforeach
</nav>

<script>
    (function () {
        // Fix select elements — remove nice-select hijacking
        function fixPromoSelects() {
            document.querySelectorAll('select.promo-select, select.promotion-select').forEach(function (sel) {
                if (!sel.hasAttribute('multiple')) {
                    sel.removeAttribute('size');
                    sel.size = 1;
                }
                var next = sel.nextElementSibling;
                if (next && next.classList && next.classList.contains('nice-select')) {
                    next.parentNode.removeChild(next);
                }
                sel.style.display = 'block';
            });
        }
        document.addEventListener('DOMContentLoaded', fixPromoSelects);
        window.addEventListener('load', fixPromoSelects);
        setTimeout(fixPromoSelects, 300);
        setTimeout(fixPromoSelects, 1200);
        setTimeout(fixPromoSelects, 3000);

        // Dropzone file name preview
        document.addEventListener('DOMContentLoaded', function () {
            var dz = document.getElementById('promoDropzoneInput');
            var dzName = document.getElementById('promoDropzoneName');
            if (dz && dzName) {
                dz.addEventListener('change', function () {
                    if (dz.files.length) {
                        dzName.textContent = dz.files[0].name;
                        dzName.style.display = 'block';
                    } else {
                        dzName.style.display = 'none';
                    }
                });
            }
        });
    })();
</script>
