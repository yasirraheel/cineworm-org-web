@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            {!! Form::open([
                                'url' => isset($editItem) && $editItem ? ['admin/updateBtn', $editItem->id] : ['admin/createBtn'],
                                'class' => 'form-horizontal',
                                'name' => 'settings_form',
                                'id' => 'settings_form',
                                'role' => 'form',
                                'enctype' => 'multipart/form-data',
                            ]) !!}



                            <h5 class="mb-4" style="color:#f9f9f9"><i class="fa fa-buysellads pr-2"></i> <b>{{ $page_title }}</b>
                            </h5>

                            {{-- <div class="alert alert-info"><b>Note:</b> Leave empty if not want to display</div> --}}

                            <!-- Home Top Section -->
                            <div class="form-group row">
                                <input type="hidden" name="type" value="{{ $type }}">
                                @if ($type == 'banners')
                                    <label class="col-sm-3 col-form-label">{{ trans('Banner Image') }} *</label>
                                    <div class="col-sm-8">

                                        <div class="input-group">
                                            <input type="text" name="home_top_text" id="home_top_text"
                                                value="{{ old('home_top_text', $editItem->image ?? '') }}"
                                                class="form-control" readonly>
                                            <div class="input-group-append">
                                                <button type="button"
                                                    class="btn btn-dark waves-effect waves-light popup_selector"
                                                    data-input="home_top_text" data-preview="holder_logo"
                                                    data-inputid="home_top_text">Select</button>
                                            </div>
                                        </div>
                                        <small id="emailHelp"
                                            class="form-text text-muted">({{ trans('words.recommended_resolution') }} :
                                            180x50)</small>
                                        <div id="home_top_text_holder" style="margin-top:5px;max-height:100px;"></div>
                                        @if (!empty($editItem->image))
                                            <div style="margin-top:10px;">
                                                <img src="{{ url($editItem->image) }}" alt="Banner Ad" class="img-thumbnail" width="180">
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            @if (isset($settings->home_top_text))
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">&nbsp;</label>
                                    <div class="col-sm-8">
                                        <img src="" alt="video image" class="img-thumbnail" width="160">
                                    </div>
                                </div>
                            @endif
                            @if ($type == 'buttons')
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">{{ trans('Button Title') }} *</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="title" value="{{ old('title', $editItem->title ?? '') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">{{ trans('Color') }} *</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="color" value="{{ old('color', $editItem->color ?? '') }}" class="form-control">
                                        <p>Get color code from <a href="https://colorhunt.co/" target="_blank">here</a> color code example (EFB036)</p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">{{ trans('Placement') }} *</label>
                                    <div class="col-sm-8">
                                        <select name="placement" class="form-control">
                                            @foreach ($buttonPlacements as $placementValue => $placementLabel)
                                                <option value="{{ $placementValue }}" {{ old('placement', $editItem->placement ?? \App\Models\ButtonsBanners::PLACEMENT_DEFAULT) == $placementValue ? 'selected' : '' }}>{{ $placementLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">{{ trans('Ad URL') }} *</label>
                                <div class="col-sm-8">
                                    <input type="text" name="ad_url" value="{{ old('ad_url', $editItem->link ?? '') }}" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="offset-sm-3 col-sm-9 pl-1">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        {{ isset($editItem) && $editItem ? trans('Update') : trans('words.save_settings') }}
                                    </button>
                                    @if (isset($editItem) && $editItem)
                                        <a href="{{ url('admin/' . $type) }}" class="btn btn-secondary waves-effect waves-light">
                                            {{ trans('Cancel') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered text text-center">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Title/ Image</th>
                                        @if ($type == 'buttons')
                                            <th>Placement</th>
                                        @endif
                                        <th>Link</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $api)
                                        <tr id="api_id_{{ $api->id }}">
                                            <td>{{ $api->id }}</td>
                                            @if ($api->type == 'banners')
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <img src="{{ url($api->image) }}" alt="Banner Ad"
                                                        style="width:180px; height:50px; display:block; margin: 0 auto;">
                                                </td>
                                            @else
                                            <td>
                                                <a href="#"
                                                   class="custom-btn"
                                                   style="background-color: #{{ $api->color ?? '#007bff' }};">
                                                    🌟 {{ $api->title }}
                                                </a>
                                            </td>

                                            @endif
                                            @if ($type == 'buttons')
                                                <td>{{ $buttonPlacements[$api->placement ?? \App\Models\ButtonsBanners::PLACEMENT_DEFAULT] ?? 'Default player/sidebar top' }}</td>
                                            @endif
                                            <td>
                                                {{ $api->link }}
                                            </td>
                                            <td>
                                                <a href="{{ url('admin/editBtn/' . $api->id) }}"
                                                    class="btn btn-icon waves-effect waves-light btn-primary m-b-5"
                                                    data-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="{{ url('admin/deleteBtn/' . $api->id) }}"
                                                    class="btn btn-icon waves-effect waves-light btn-danger m-b-5 data_remove"
                                                    data-toggle="tooltip" title="Remove" data-id="{{ $api->id }}">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>


                        </div>
                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $data])
                        </nav>

                    </div>
                </div>
            </div>
        </div>
        <style>
            .custom-btn {
                display: block;
                width: 100%;
                margin-bottom: 15px;
                padding: 15px;
                font-size: 18px;
                font-weight: bold;
                text-align: center;
                color: #fff;
                /* Adjust as needed */
                background-color: #007bff;
                /* Primary color */
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                text-decoration: none;
                /* Remove underline */
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .custom-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3);
            }

            .custom-btn:active {
                transform: translateY(0);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            }
        </style>
        @include('admin.copyright')
    </div>
    <script type="text/javascript">
        // function to update the file selected by elfinder
        function processSelectedFile(filePath, requestingField) {

            //alert(requestingField);

            var elfinderUrl = "{{ URL::to('/') }}/";

            if (requestingField == "home_top_text") {
                var target_preview = $('#home_top_text_holder');
                target_preview.html('');
                target_preview.append(
                    $('<img>').css('height', '5rem').attr('src', elfinderUrl + filePath.replace(/\\/g, "/"))
                );
                target_preview.trigger('change');
            }

            if (requestingField == "site_favicon") {
                var target_preview = $('#site_favicon_holder');
                target_preview.html('');
                target_preview.append(
                    $('<img>').css('height', '5rem').attr('src', elfinderUrl + filePath.replace(/\\/g, "/"))
                );
                target_preview.trigger('change');
            }

            //$('#' + requestingField).val(filePath.split('\\').pop()).trigger('change'); //For only filename
            $('#' + requestingField).val(filePath.replace(/\\/g, "/")).trigger('change');

        }
    </script>
    <script type="text/javascript">
        @if (Session::has('flash_message'))

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
                /*didOpen: (toast) => {
                  toast.addEventListener('mouseenter', Swal.stopTimer)
                  toast.addEventListener('mouseleave', Swal.resumeTimer)
                }*/
            })

            Toast.fire({
                icon: 'success',
                title: '{{ Session::get('flash_message') }}'
            })
        @endif

        @if (count($errors) > 0)

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<p>@foreach ($errors->all() as $error) {{ $error }}<br/> @endforeach</p>',
                showConfirmButton: true,
                confirmButtonColor: '#10c469',
                background: "#1a2234",
                color: "#fff"
            })
        @endif
    </script>
@endsection
