@extends('admin.admin_app')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">

<style>
    .veditor-create-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 28px;
    }
    .veditor-create-header a {
        color: #fe0278;
        font-size: 20px;
        text-decoration: none;
        transition: transform 0.15s ease;
    }
    .veditor-create-header a:hover {
        transform: translateX(-3px);
        color: #ff2e90;
    }
    .veditor-create-header h3 {
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }
    .veditor-create-form {
        max-width: 600px;
    }
    .veditor-form-group {
        margin-bottom: 24px;
    }
    .veditor-form-group label {
        display: block;
        color: rgba(255,255,255,0.7);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .veditor-form-group label span {
        color: #fe0278;
    }
    .veditor-input {
        width: 100%;
        padding: 12px 16px;
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .veditor-input:focus {
        border-color: rgba(254,2,120,0.5);
        box-shadow: 0 0 0 3px rgba(254,2,120,0.1);
    }
    .veditor-input::placeholder {
        color: rgba(255,255,255,0.25);
    }
    textarea.veditor-input {
        resize: vertical;
        min-height: 100px;
    }
    .veditor-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        background: linear-gradient(135deg, #fe0278, #c8005f);
        border: none;
        border-radius: 10px;
        color: #fff !important;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(254,2,120,0.3);
        transition: all 0.18s ease;
    }
    .veditor-btn-primary:hover {
        background: linear-gradient(135deg, #ff2e90, #e00070);
        box-shadow: 0 6px 20px rgba(254,2,120,0.46);
        transform: translateY(-1px);
    }
    .veditor-create-tip {
        background: rgba(254,2,120,0.06);
        border: 1px solid rgba(254,2,120,0.15);
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 28px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .veditor-create-tip i {
        color: #fe0278;
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .veditor-create-tip p {
        color: rgba(255,255,255,0.5);
        font-size: 13px;
        margin: 0;
        line-height: 1.5;
    }
</style>

        <div class="veditor-create-header">
            <a href="{{ URL::to('user/editor') }}"><i class="fa fa-arrow-left"></i></a>
            <h3>Create New Project</h3>
        </div>

        @if(count($errors) > 0)
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ URL::to('user/editor/store') }}" class="veditor-create-form">
            @csrf

            <div class="veditor-form-group">
                <label>Project Title <span>*</span></label>
                <input type="text" name="title" class="veditor-input"
                       placeholder="e.g. My Short Film — Final Cut"
                       value="{{ old('title') }}" required maxlength="255" autofocus>
            </div>

            <div class="veditor-form-group">
                <label>Description <span style="color:rgba(255,255,255,0.25);text-transform:lowercase;font-weight:400;">(optional)</span></label>
                <textarea name="description" class="veditor-input"
                          placeholder="Brief notes about this editing project…">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="veditor-btn-primary">
                <i class="fa fa-cut"></i> Create &amp; Start Editing
            </button>
        </form>

        <div class="veditor-create-tip">
            <i class="fa fa-info-circle"></i>
            <p>
                After creating your project, you'll be taken to the editing bench where you can upload video clips, 
                arrange them on the film strip, cut and splice scenes, and export your finished film.
            </p>
        </div>

                    </div>{{-- /card-box --}}
                </div>
            </div>
        </div>
    </div>
    @include('admin.copyright')
</div>
@endsection
