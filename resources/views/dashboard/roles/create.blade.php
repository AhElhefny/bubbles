@extends('dashboard.layouts.master')
@section('toolbar')
@endsection
@section('content')
<div class="card card-xl-stretch mb-5 mb-xl-12">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
             <span class="card-label fw-bolder fs-3 mb-1">{{trans('admin.create_permission')}}</span>
        </h3>
        <div class="card-toolbar">
            {{trans('admin.new_role')}}
        </div>
    </div>
     {!! Form::open(['url' => route('roles.store'), 'method' => 'post', 'files'=>true]) !!}
    <div class="card card-xl-stretch mb-5 mb-xl-10">
        <hr>
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                 <span class="card-label fw-bolder fs-3 mb-1">  </span>
            </h3>
            <div class="card-toolbar">
            </div>
        </div>
        <div class="card-body py-3">
            <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_update_role_header" data-kt-scroll-wrappers="#kt_modal_update_role_scroll" data-kt-scroll-offset="300px" style="max-height: 355px;">
				<div class="fv-row mb-10 fv-plugins-icon-container">
					<label class="fs-5 fw-bolder form-label mb-2">
						<span class="required">{{__('admin.Role_name')}}</span>
					</label>

					<input class="form-control form-control-solid" placeholder="{{__('admin.Enter_role_name')}}" name="name" >
					<!--end::Input-->
				<div class="fv-plugins-message-container invalid-feedback"></div></div>
				<!--end::Input group-->
				<!--begin::Permissions-->
				<div class="fv-row">
					<!--begin::Label-->
					<label class="fs-5 fw-bolder form-label mb-2">{{__('admin.Role_Permissions')}}</label>
					<!--end::Label-->
					<!--begin::Table wrapper-->
					<div class="table-responsive">
						<!--begin::Table-->
						<table class="table align-middle table-row-dashed fs-6 gy-5">
							<!--begin::Table body-->
							<tbody class="text-gray-600 fw-bold">
								<!--begin::Table row-->
								<tr>
									<td class="text-gray-800"> {{__('admin.Administrator_Access')}}
									<i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="" data-bs-original-title="Allows a full access to the system" aria-label="Allows a full access to the system"></i></td>
									<td>
										<!--begin::Checkbox-->
										<label class="form-check form-check-sm form-check-custom form-check-solid me-9">
											<input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all" onchange="selectPermission('per',$(this))">
											<span class="form-check-label" for="kt_roles_select_all">{{__('admin.Select_all')}}</span>
										</label>
										<!--end::Checkbox-->
									</td>
								</tr>
								  @foreach($modules as $module)
								    <tr>
									<!--begin::Label-->
									<td class="text-gray-800">
										{{$module->title}}
										<input class="form-check-input per" type="checkbox" onchange="selectPermission('{{$module->name}}',$(this))">
									</td>
									<!--end::Label-->
									<!--begin::Input group-->
									<td>
										<!--begin::Wrapper-->
										<div class="d-flex" id="{{$module->name}}">
											@foreach($module->permissions as $permission)
											<!--begin::Checkbox-->
											<label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
												<input class="form-check-input {{$module->name}} per" id="" type="checkbox" value="{{$permission->id}}" name="permissions[]">
												<span class="form-check-label">{{str_replace('_'.$module->name,'',$permission->name)}}</span>
											</label>
											<!--end::Checkbox-->
											@endforeach
										</div>
										<!--end::Wrapper-->
									</td>
									<!--end::Input group-->
								</tr>
								<!--end::Table row-->
								@endforeach
							</tbody>
							<!--end::Table body-->
						</table>
						<!--end::Table-->
					</div>
					<!--end::Table wrapper-->
				</div>
				<!--end::Permissions-->
			</div>
        </div>
    </div>

    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="reset" class="btn btn-light btn-active-light-primary me-2"  onClick="window.location.href=window.location.href">{{__('admin.Discard')}}</button>
        <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">{{__('admin.Save')}} </button>
    </div>
         {!! Form::close() !!}
</div>

@endsection

@section('script')

<script type="text/javascript">

	function selectPermission(id,ele)
	{
		console.log(id)
		// $('.'+id).prop("checked",true)
		if(ele.prop("checked")==true)
	     	$('.'+id).prop("checked", true);
	   	else
	     	$('.'+id).prop("checked", false)
	}

</script>
@endsection
