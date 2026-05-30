<style>
    .whatsapp-admin-page .card-box,
    .whatsapp-admin-page .card-box p,
    .whatsapp-admin-page .card-box label,
    .whatsapp-admin-page .card-box strong,
    .whatsapp-admin-page .card-box span,
    .whatsapp-admin-page .card-box td,
    .whatsapp-admin-page .card-box th,
    .whatsapp-admin-page .card-box div,
    .whatsapp-admin-page .card-box li,
    .whatsapp-admin-page .header-title {
        color: #f2f4f8;
    }

    .whatsapp-admin-page .text-muted,
    .whatsapp-admin-page .helper-text,
    .whatsapp-admin-page small {
        color: #c7d2e0 !important;
    }

    .whatsapp-admin-page .form-control {
        background: #25262b;
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.12);
    }

    .whatsapp-admin-page .form-control:focus {
        background: #2d2f36;
        color: #ffffff;
        border-color: #10c469;
    }

    .whatsapp-admin-page option {
        background: #25262b;
        color: #ffffff;
    }

    .whatsapp-admin-page .badge,
    .whatsapp-admin-page .btn,
    .whatsapp-admin-page .btn i {
        color: inherit;
    }

    .whatsapp-metric {
        min-height: 108px;
        border-left: 3px solid #10c469;
    }

    .whatsapp-status-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0;
    }

    .whatsapp-status-connected { background: #10c469; color: #ffffff; }
    .whatsapp-status-qr,
    .whatsapp-status-connecting { background: #f9c851; color: #1f1f1f; }
    .whatsapp-status-unavailable,
    .whatsapp-status-error,
    .whatsapp-status-disconnected,
    .whatsapp-status-logged_out { background: #ff5b5b; color: #ffffff; }

    .whatsapp-qr-box {
        min-height: 315px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: #1f2025;
        border-radius: 4px;
        padding: 20px;
    }

    .whatsapp-qr-box img {
        width: 300px;
        max-width: 100%;
        background: #ffffff;
        padding: 12px;
        border-radius: 4px;
    }

    .whatsapp-inline-alert {
        display: none;
        padding: 10px 12px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .whatsapp-inline-alert.success {
        display: block;
        background: rgba(16, 196, 105, 0.16);
        border: 1px solid rgba(16, 196, 105, 0.45);
    }

    .whatsapp-inline-alert.error {
        display: block;
        background: rgba(255, 91, 91, 0.16);
        border: 1px solid rgba(255, 91, 91, 0.45);
    }

    .whatsapp-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
    }

    .whatsapp-nav a {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 4px;
        background: rgba(255,255,255,0.06);
        color: #f2f4f8;
        font-weight: 600;
    }

    .whatsapp-nav a:hover,
    .whatsapp-nav a.active {
        background: #10c469;
        color: #ffffff;
    }

    .whatsapp-progress {
        height: 8px;
        background: rgba(255,255,255,0.1);
        border-radius: 4px;
        overflow: hidden;
    }

    .whatsapp-progress span {
        display: block;
        height: 8px;
        background: #10c469;
    }
</style>
