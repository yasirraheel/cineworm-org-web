@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box p-0" style="overflow:hidden;">
                            <div class="p-3" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08);">
                                <h4 class="header-title m-0"><i class="fa fa-file-code-o text-primary"></i> CineMeet Swagger API Reference</h4>
                                <div>
                                    <a href="{{ $apiUrl }}/api" target="_blank" class="btn btn-sm btn-primary waves-effect waves-light"><i class="fa fa-external-link"></i> Open in New Tab</a>
                                    <a href="{{ $apiUrl }}/admin-api/status" target="_blank" class="btn btn-sm btn-info waves-effect waves-light m-l-5"><i class="fa fa-plug"></i> Test Status</a>
                                </div>
                            </div>
                            <iframe src="{{ $apiUrl }}/api"
                                title="CineMeet API Documentation"
                                style="width:100%; height:78vh; border:none; display:block;"
                                sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox">
                            </iframe>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
