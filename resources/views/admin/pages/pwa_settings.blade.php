@extends('admin.admin_app')

@section('content')
<style type="text/css">
.nav-tabs .nav-link.active {
    background-color: #5fbeaa;
    color: white;
}
.icon-preview {
    max-width: 150px;
    max-height: 150px;
    margin-top: 10px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 5px;
}
.shortcut-item {
    background-color: #f8f9fa;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
.color-preview {
    display: inline-block;
    width: 30px;
    height: 30px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
    margin-left: 10px;
}
</style>

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="header-title mb-3">{{ trans('pwa.pwa_settings') }}</h4>

                                @if (Session::has('flash_message'))
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        {{ Session::get('flash_message') }}
                                    </div>
                                @endif

                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> {{ trans('pwa.pwa_info') }}
                                </div>

                                {!! Form::open(['url' => 'admin/pwa_settings', 'class' => 'form-horizontal', 'name' => 'pwa_form', 'id' => 'pwa_form', 'role' => 'form', 'enctype' => 'multipart/form-data']) !!}

                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#general">
                                            <i class="fa fa-cog"></i> {{ trans('pwa.general_tab') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#appearance">
                                            <i class="fa fa-paint-brush"></i> {{ trans('pwa.appearance_tab') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#icons">
                                            <i class="fa fa-image"></i> {{ trans('pwa.icons_images_tab') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#offline">
                                            <i class="fa fa-wifi"></i> {{ trans('pwa.offline_tab') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#notifications">
                                            <i class="fa fa-bell"></i> {{ trans('pwa.notifications_tab') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#shortcuts">
                                            <i class="fa fa-link"></i> {{ trans('pwa.shortcuts_tab') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#advanced">
                                            <i class="fa fa-sliders"></i> {{ trans('pwa.advanced_tab') }}
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    {{-- General Tab --}}
                                    <div id="general" class="tab-pane fade show active">
                                        <div class="row mt-4">
                                            <div class="col-md-8">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.pwa_enable') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="pwa_enabled" class="form-control">
                                                            <option value="0" {{ $pwa_settings->pwa_enabled == 0 ? 'selected' : '' }}>{{ trans('pwa.pwa_disabled') }}</option>
                                                            <option value="1" {{ $pwa_settings->pwa_enabled == 1 ? 'selected' : '' }}>{{ trans('pwa.pwa_enabled') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.app_name') }}*</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="app_name" value="{{ $pwa_settings->app_name }}" class="form-control" required>
                                                        <small class="form-text text-muted">{{ trans('pwa.app_name_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.app_short_name') }}*</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="app_short_name" value="{{ $pwa_settings->app_short_name }}" class="form-control" maxlength="12" required>
                                                        <small class="form-text text-muted">{{ trans('pwa.app_short_name_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.app_description') }}</label>
                                                    <div class="col-sm-9">
                                                        <textarea name="app_description" class="form-control" rows="3">{{ $pwa_settings->app_description }}</textarea>
                                                        <small class="form-text text-muted">{{ trans('pwa.app_description_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.start_url') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="start_url" value="{{ $pwa_settings->start_url }}" class="form-control">
                                                        <small class="form-text text-muted">{{ trans('pwa.start_url_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.scope') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="scope" value="{{ $pwa_settings->scope }}" class="form-control">
                                                        <small class="form-text text-muted">{{ trans('pwa.scope_help') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Appearance Tab --}}
                                    <div id="appearance" class="tab-pane fade">
                                        <div class="row mt-4">
                                            <div class="col-md-8">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.theme_color') }}</label>
                                                    <div class="col-sm-9">
                                                        <div class="input-group">
                                                            <input type="color" name="theme_color" value="{{ $pwa_settings->theme_color }}" class="form-control" style="width: 80px;">
                                                            <input type="text" value="{{ $pwa_settings->theme_color }}" class="form-control ml-2" id="theme_color_text" readonly>
                                                        </div>
                                                        <small class="form-text text-muted">{{ trans('pwa.theme_color_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.background_color') }}</label>
                                                    <div class="col-sm-9">
                                                        <div class="input-group">
                                                            <input type="color" name="background_color" value="{{ $pwa_settings->background_color }}" class="form-control" style="width: 80px;">
                                                            <input type="text" value="{{ $pwa_settings->background_color }}" class="form-control ml-2" id="background_color_text" readonly>
                                                        </div>
                                                        <small class="form-text text-muted">{{ trans('pwa.background_color_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.display_mode') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="display_mode" class="form-control">
                                                            <option value="standalone" {{ $pwa_settings->display_mode == 'standalone' ? 'selected' : '' }}>{{ trans('pwa.standalone') }}</option>
                                                            <option value="fullscreen" {{ $pwa_settings->display_mode == 'fullscreen' ? 'selected' : '' }}>{{ trans('pwa.fullscreen') }}</option>
                                                            <option value="minimal-ui" {{ $pwa_settings->display_mode == 'minimal-ui' ? 'selected' : '' }}>{{ trans('pwa.minimal_ui') }}</option>
                                                            <option value="browser" {{ $pwa_settings->display_mode == 'browser' ? 'selected' : '' }}>{{ trans('pwa.browser') }}</option>
                                                        </select>
                                                        <small class="form-text text-muted">{{ trans('pwa.display_mode_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.orientation') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="orientation" class="form-control">
                                                            <option value="any" {{ $pwa_settings->orientation == 'any' ? 'selected' : '' }}>{{ trans('pwa.any') }}</option>
                                                            <option value="portrait" {{ $pwa_settings->orientation == 'portrait' ? 'selected' : '' }}>{{ trans('pwa.portrait') }}</option>
                                                            <option value="landscape" {{ $pwa_settings->orientation == 'landscape' ? 'selected' : '' }}>{{ trans('pwa.landscape') }}</option>
                                                            <option value="portrait-primary" {{ $pwa_settings->orientation == 'portrait-primary' ? 'selected' : '' }}>{{ trans('pwa.portrait_primary') }}</option>
                                                            <option value="landscape-primary" {{ $pwa_settings->orientation == 'landscape-primary' ? 'selected' : '' }}>{{ trans('pwa.landscape_primary') }}</option>
                                                        </select>
                                                        <small class="form-text text-muted">{{ trans('pwa.orientation_help') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Icons & Images Tab --}}
                                    <div id="icons" class="tab-pane fade">
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <div class="alert alert-warning">
                                                    <i class="fa fa-lightbulb-o"></i> <strong>{{ trans('pwa.auto_generate') }}:</strong> {{ trans('pwa.auto_generate_help') }}
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.icon_192') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="file" name="icon_192" class="form-control" accept="image/*">
                                                        <small class="form-text text-muted">{{ trans('pwa.icon_192_help') }}</small>
                                                        @if($pwa_settings->icon_192)
                                                            <img src="{{ asset($pwa_settings->icon_192) }}" class="icon-preview" alt="Icon 192">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.icon_512') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="file" name="icon_512" class="form-control" accept="image/*">
                                                        <small class="form-text text-muted">{{ trans('pwa.icon_512_help') }}</small>
                                                        @if($pwa_settings->icon_512)
                                                            <img src="{{ asset($pwa_settings->icon_512) }}" class="icon-preview" alt="Icon 512">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.maskable_icon_192') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="file" name="maskable_icon_192" class="form-control" accept="image/*">
                                                        <small class="form-text text-muted">{{ trans('pwa.maskable_icon_help') }}</small>
                                                        @if($pwa_settings->maskable_icon_192)
                                                            <img src="{{ asset($pwa_settings->maskable_icon_192) }}" class="icon-preview" alt="Maskable 192">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.maskable_icon_512') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="file" name="maskable_icon_512" class="form-control" accept="image/*">
                                                        <small class="form-text text-muted">{{ trans('pwa.maskable_icon_help') }}</small>
                                                        @if($pwa_settings->maskable_icon_512)
                                                            <img src="{{ asset($pwa_settings->maskable_icon_512) }}" class="icon-preview" alt="Maskable 512">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.apple_touch_icon') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="file" name="apple_touch_icon" class="form-control" accept="image/*">
                                                        <small class="form-text text-muted">{{ trans('pwa.apple_touch_icon_help') }}</small>
                                                        @if($pwa_settings->apple_touch_icon)
                                                            <img src="{{ asset($pwa_settings->apple_touch_icon) }}" class="icon-preview" alt="Apple Touch Icon">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.screenshots') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="file" name="screenshots[]" class="form-control" accept="image/*" multiple>
                                                        <small class="form-text text-muted">{{ trans('pwa.screenshots_help') }}</small>
                                                        @if($pwa_settings->screenshots && count($pwa_settings->screenshots) > 0)
                                                            <div class="row mt-2">
                                                                @foreach($pwa_settings->screenshots as $screenshot)
                                                                    <div class="col-md-3">
                                                                        <img src="{{ asset($screenshot) }}" class="img-thumbnail" alt="Screenshot">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Offline Tab --}}
                                    <div id="offline" class="tab-pane fade">
                                        <div class="row mt-4">
                                            <div class="col-md-8">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.offline_page_enable') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="offline_page_enabled" class="form-control">
                                                            <option value="0" {{ $pwa_settings->offline_page_enabled == 0 ? 'selected' : '' }}>{{ trans('pwa.pwa_disabled') }}</option>
                                                            <option value="1" {{ $pwa_settings->offline_page_enabled == 1 ? 'selected' : '' }}>{{ trans('pwa.pwa_enabled') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.offline_page_title') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="offline_page_title" value="{{ $pwa_settings->offline_page_title }}" class="form-control">
                                                        <small class="form-text text-muted">{{ trans('pwa.offline_page_title_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.offline_page_message') }}</label>
                                                    <div class="col-sm-9">
                                                        <textarea name="offline_page_message" class="form-control" rows="4">{{ $pwa_settings->offline_page_message }}</textarea>
                                                        <small class="form-text text-muted">{{ trans('pwa.offline_page_message_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.cache_strategy') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="cache_strategy" class="form-control">
                                                            <option value="cache-first" {{ $pwa_settings->cache_strategy == 'cache-first' ? 'selected' : '' }}>{{ trans('pwa.cache_first') }}</option>
                                                            <option value="network-first" {{ $pwa_settings->cache_strategy == 'network-first' ? 'selected' : '' }}>{{ trans('pwa.network_first') }}</option>
                                                            <option value="stale-while-revalidate" {{ $pwa_settings->cache_strategy == 'stale-while-revalidate' ? 'selected' : '' }}>{{ trans('pwa.stale_while_revalidate') }}</option>
                                                        </select>
                                                        <small class="form-text text-muted">{{ trans('pwa.cache_strategy_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.cache_version') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" name="cache_version" value="{{ $pwa_settings->cache_version }}" class="form-control">
                                                        <small class="form-text text-muted">{{ trans('pwa.cache_version_help') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notifications Tab --}}
                                    <div id="notifications" class="tab-pane fade">
                                        <div class="row mt-4">
                                            <div class="col-md-8">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.push_enable') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="push_notification_enabled" class="form-control">
                                                            <option value="0" {{ $pwa_settings->push_notification_enabled == 0 ? 'selected' : '' }}>{{ trans('pwa.pwa_disabled') }}</option>
                                                            <option value="1" {{ $pwa_settings->push_notification_enabled == 1 ? 'selected' : '' }}>{{ trans('pwa.pwa_enabled') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.notification_icon') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="file" name="notification_icon" class="form-control" accept="image/*">
                                                        <small class="form-text text-muted">{{ trans('pwa.notification_icon_help') }}</small>
                                                        @if($pwa_settings->notification_icon)
                                                            <img src="{{ asset($pwa_settings->notification_icon) }}" class="icon-preview" alt="Notification Icon">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.notification_badge') }}</label>
                                                    <div class="col-sm-9">
                                                        <input type="file" name="notification_badge" class="form-control" accept="image/*">
                                                        <small class="form-text text-muted">{{ trans('pwa.notification_badge_help') }}</small>
                                                        @if($pwa_settings->notification_badge)
                                                            <img src="{{ asset($pwa_settings->notification_badge) }}" class="icon-preview" alt="Notification Badge">
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.vapid_public_key') }}</label>
                                                    <div class="col-sm-9">
                                                        <textarea name="vapid_public_key" class="form-control" rows="2">{{ $pwa_settings->vapid_public_key }}</textarea>
                                                        <small class="form-text text-muted">{{ trans('pwa.vapid_keys_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.vapid_private_key') }}</label>
                                                    <div class="col-sm-9">
                                                        <textarea name="vapid_private_key" class="form-control" rows="2">{{ $pwa_settings->vapid_private_key }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Shortcuts Tab --}}
                                    <div id="shortcuts" class="tab-pane fade">
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.shortcuts_enable') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="shortcuts_enabled" class="form-control">
                                                            <option value="0" {{ $pwa_settings->shortcuts_enabled == 0 ? 'selected' : '' }}>{{ trans('pwa.pwa_disabled') }}</option>
                                                            <option value="1" {{ $pwa_settings->shortcuts_enabled == 1 ? 'selected' : '' }}>{{ trans('pwa.pwa_enabled') }}</option>
                                                        </select>
                                                        <small class="form-text text-muted">{{ trans('pwa.shortcuts_help') }}</small>
                                                    </div>
                                                </div>

                                                <div id="shortcuts-container">
                                                    @if($pwa_settings->custom_shortcuts && count($pwa_settings->custom_shortcuts) > 0)
                                                        @foreach($pwa_settings->custom_shortcuts as $index => $shortcut)
                                                            <div class="shortcut-item">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>{{ trans('pwa.shortcut_name') }}</label>
                                                                            <input type="text" name="shortcut_names[]" value="{{ $shortcut['name'] ?? '' }}" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>{{ trans('pwa.shortcut_short_name') }}</label>
                                                                            <input type="text" name="shortcut_short_names[]" value="{{ $shortcut['short_name'] ?? '' }}" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>{{ trans('pwa.shortcut_description') }}</label>
                                                                            <input type="text" name="shortcut_descriptions[]" value="{{ $shortcut['description'] ?? '' }}" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>{{ trans('pwa.shortcut_url') }}</label>
                                                                            <input type="text" name="shortcut_urls[]" value="{{ $shortcut['url'] ?? '' }}" class="form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>{{ trans('pwa.shortcut_icon') }}</label>
                                                                            <input type="file" name="shortcut_icons[{{ $index }}]" class="form-control" accept="image/*">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-danger btn-sm remove-shortcut">{{ trans('pwa.remove_shortcut') }}</button>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <button type="button" class="btn btn-primary" id="add-shortcut">
                                                    <i class="fa fa-plus"></i> {{ trans('pwa.add_shortcut') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Advanced Tab --}}
                                    <div id="advanced" class="tab-pane fade">
                                        <div class="row mt-4">
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.categories') }}</label>
                                                    <div class="col-sm-9">
                                                        <div class="row">
                                                            @php
                                                                $categories = ['books', 'business', 'education', 'entertainment', 'finance', 'fitness', 'food', 'games', 'health', 'lifestyle', 'music', 'news', 'photo', 'productivity', 'shopping', 'social', 'sports', 'travel', 'utilities'];
                                                                $selected_categories = $pwa_settings->categories ?? [];
                                                            @endphp
                                                            @foreach($categories as $category)
                                                                <div class="col-md-4">
                                                                    <div class="checkbox">
                                                                        <input type="checkbox" name="categories[]" value="{{ $category }}" id="cat_{{ $category }}" {{ in_array($category, $selected_categories) ? 'checked' : '' }}>
                                                                        <label for="cat_{{ $category }}">{{ trans('pwa.' . $category) }}</label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <small class="form-text text-muted">{{ trans('pwa.categories_help') }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.language') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="lang" class="form-control">
                                                            <option value="en" {{ $pwa_settings->lang == 'en' ? 'selected' : '' }}>English</option>
                                                            <option value="es" {{ $pwa_settings->lang == 'es' ? 'selected' : '' }}>Español</option>
                                                            <option value="fr" {{ $pwa_settings->lang == 'fr' ? 'selected' : '' }}>Français</option>
                                                            <option value="pt" {{ $pwa_settings->lang == 'pt' ? 'selected' : '' }}>Português</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.text_direction') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="dir" class="form-control">
                                                            <option value="ltr" {{ $pwa_settings->dir == 'ltr' ? 'selected' : '' }}>{{ trans('pwa.ltr') }}</option>
                                                            <option value="rtl" {{ $pwa_settings->dir == 'rtl' ? 'selected' : '' }}>{{ trans('pwa.rtl') }}</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-3 col-form-label">{{ trans('pwa.prefer_related_apps') }}</label>
                                                    <div class="col-sm-9">
                                                        <select name="prefer_related_apps" class="form-control">
                                                            <option value="0" {{ $pwa_settings->prefer_related_apps == 0 ? 'selected' : '' }}>{{ trans('pwa.pwa_disabled') }}</option>
                                                            <option value="1" {{ $pwa_settings->prefer_related_apps == 1 ? 'selected' : '' }}>{{ trans('pwa.pwa_enabled') }}</option>
                                                        </select>
                                                        <small class="form-text text-muted">{{ trans('pwa.prefer_related_apps_help') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="offset-sm-0 col-sm-12 text-center">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="fa fa-save"></i> {{ trans('pwa.save_settings') }}
                                        </button>
                                    </div>
                                </div>

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Color picker sync
    $('input[name="theme_color"]').on('change', function() {
        $('#theme_color_text').val($(this).val());
    });

    $('input[name="background_color"]').on('change', function() {
        $('#background_color_text').val($(this).val());
    });

    // Add shortcut
    $('#add-shortcut').click(function() {
        var shortcutHtml = `
            <div class="shortcut-item">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('pwa.shortcut_name') }}</label>
                            <input type="text" name="shortcut_names[]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('pwa.shortcut_short_name') }}</label>
                            <input type="text" name="shortcut_short_names[]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ trans('pwa.shortcut_description') }}</label>
                            <input type="text" name="shortcut_descriptions[]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('pwa.shortcut_url') }}</label>
                            <input type="text" name="shortcut_urls[]" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('pwa.shortcut_icon') }}</label>
                            <input type="file" name="shortcut_icons[]" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-shortcut">{{ trans('pwa.remove_shortcut') }}</button>
            </div>
        `;
        $('#shortcuts-container').append(shortcutHtml);
    });

    // Remove shortcut
    $(document).on('click', '.remove-shortcut', function() {
        $(this).closest('.shortcut-item').remove();
    });
</script>

@endsection
