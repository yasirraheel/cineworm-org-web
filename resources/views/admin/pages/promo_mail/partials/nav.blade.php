<div class="row m-b-20">
    <div class="col-12">
        <div class="btn-group" role="group" aria-label="Promotional mail navigation">
            <a href="{{ URL::to('admin/promo_mail/servers') }}" class="btn {{ classActivePath('promo_mail.servers') ? 'btn-danger' : 'btn-secondary' }}">
                <i class="fa fa-server"></i> SMTP Servers
            </a>
            <a href="{{ URL::to('admin/promo_mail/sending-domains') }}" class="btn {{ classActivePath('promo_mail.sending-domains') ? 'btn-danger' : 'btn-secondary' }}">
                <i class="fa fa-shield"></i> Sending Domains
            </a>
            <a href="{{ URL::to('admin/promo_mail/tracking-domains') }}" class="btn {{ classActivePath('promo_mail.tracking-domains') ? 'btn-danger' : 'btn-secondary' }}">
                <i class="fa fa-link"></i> Tracking Domains
            </a>
        </div>
    </div>
</div>
