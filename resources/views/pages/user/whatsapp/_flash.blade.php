@if(Session::has('flash_message'))
    <div class="alert alert-dismissible fade show" role="alert" style="background: rgba(37, 211, 102, 0.12); border: 1px solid rgba(37, 211, 102, 0.4); color: #25D366; border-radius: 8px; padding: 12px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; font-size: 14px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa fa-check-circle" style="font-size: 18px; color: #25D366;"></i>
            <span>{{ Session::get('flash_message') }}</span>
        </div>
        <button type="button" class="close" data-bs-dismiss="alert" data-dismiss="alert" aria-label="Close" style="background: none; border: none; color: #25D366; font-size: 20px; line-height: 1; opacity: 0.7; cursor: pointer; padding: 0; margin: 0;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(Session::has('error_flash_message'))
    <div class="alert alert-dismissible fade show" role="alert" style="background: rgba(220, 53, 69, 0.12); border: 1px solid rgba(220, 53, 69, 0.4); color: #ff6b6b; border-radius: 8px; padding: 12px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; font-size: 14px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa fa-exclamation-circle" style="font-size: 18px; color: #ff6b6b;"></i>
            <span>{{ Session::get('error_flash_message') }}</span>
        </div>
        <button type="button" class="close" data-bs-dismiss="alert" data-dismiss="alert" aria-label="Close" style="background: none; border: none; color: #ff6b6b; font-size: 20px; line-height: 1; opacity: 0.7; cursor: pointer; padding: 0; margin: 0;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="alert alert-dismissible fade show" role="alert" style="background: rgba(220, 53, 69, 0.12); border: 1px solid rgba(220, 53, 69, 0.4); color: #ff6b6b; border-radius: 8px; padding: 12px 18px; margin-bottom: 20px; font-size: 14px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
        <div style="display: flex; align-items: flex-start; justify-content: space-between;">
            <div style="display: flex; align-items: flex-start; gap: 10px;">
                <i class="fa fa-exclamation-triangle" style="font-size: 18px; color: #ff6b6b; margin-top: 2px;"></i>
                <ul class="mb-0 pl-3" style="margin: 0; padding-left: 15px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="close" data-bs-dismiss="alert" data-dismiss="alert" aria-label="Close" style="background: none; border: none; color: #ff6b6b; font-size: 20px; line-height: 1; opacity: 0.7; cursor: pointer; padding: 0; margin: 0;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif
