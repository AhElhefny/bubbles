<div class="modal fade" id="kt_modal_update_role" tabindex="-1" style="display: none;" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered mw-750px">
	            	<div class="modal-content">
			             <div class="modal-header">
				             <h2 class="fw-bolder">Update Role</h2>
			            	     <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-roles-modal-action="close">
					                  <span class="svg-icon svg-icon-1">
					                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
						 	                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"></rect>
						 	                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
						                   </svg>
					                 </span>
                               </div>
		  	             </div>
		
		             	<div class="modal-body scroll-y mx-5 my-7">
			                {!! Form::open(['url' => route('roles.store'), 'method' => 'post','id'=>'kt_modal_update_role_form' ,'class'=>'form fv-plugins-bootstrap5 fv-plugins-framework','files'=>true]) !!}
				 	      <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_role_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_update_role_header" data-kt-scroll-wrappers="#kt_modal_update_role_scroll" data-kt-scroll-offset="300px" style="max-height: 355px;">
						     <div class="fv-row mb-10 fv-plugins-icon-container">
						 	     <label class="fs-5 fw-bolder form-label mb-2">
								     <span class="required">Role name</span>
							    </label>
							
						       <input class="form-control form-control-solid" placeholder="Enter a role name" name="name" >
						           <div class="fv-plugins-message-container invalid-feedback"></div></div>
					    	          <div class="fv-row">
							              <label class="fs-5 fw-bolder form-label mb-2">{{__('admin.Role Permissions')}}</label>
							                  <div class="table-responsive">
								                 <table class="table align-middle table-row-dashed fs-6 gy-5">
								    	            <tbody class="text-gray-600 fw-bold">
									  	             <tr>
											            <td class="text-gray-800">Administrator Access 
											               <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="" data-bs-original-title="Allows a full access to the system" aria-label="Allows a full access to the system"></i></td>
											            <td>
												         <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
												 	         <input class="form-check-input" type="checkbox" value="" id="kt_roles_select_all" onchange="selectPermission('per',$(this))">
												  	          <span class="form-check-label" for="kt_roles_select_all">{{__('admin.Select all')}}</span>
												            </label>
											            </td>
									         	    </tr>
										               @foreach($modules as $module)
													 <tr>
													   <td class="text-gray-800">
															  {{$module->name}}
														    <input class="form-check-input per" type="checkbox" onchange="selectPermission('{{$module->name}}',$(this))">
													   </td>
													   <td>
														<div class="d-flex" id="{{$module->name}}">
														  	   @foreach($module->permissions as $permission)
															  <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
																  <input class="form-check-input {{$module->name}} per" id="" type="checkbox" value="{{$permission->id}}" name="permissions[]">
																  <span class="form-check-label">{{str_replace('_'.$module->name,'',$permission->name)}}</span>
															 </label>
															   @endforeach
														  </div>
												       </td>
												   </tr>
										            @endforeach
								      	       </tbody>
								            </table>
							           </div>
						          </div>
					       </div>
				
				 	        <div class="text-center pt-15">
						        <button type="reset" class="btn btn-light me-3" data-kt-roles-modal-action="cancel">Discard</button>
						          <button type="submit" class="btn btn-primary" data-kt-roles-modal-action="submit">
							          <span class="indicator-label">Submit</span>
							          <span class="indicator-progress">Please wait... 
							          <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
					             </button>
				            </div>
				          <div></div>
		                   {!! Form::close() !!}
			         </div>
                </div>
	      </div>
   </div>

<script type="text/javascript" src="https://preview.keenthemes.com/metronic8/demo1/assets/js/custom/modals/create-app.js"></script>
<script type="text/javascript" src="https://preview.keenthemes.com/metronic8/demo1/assets/js/custom/modals/upgrade-plan.js"></script>
<script type="text/javascript" src="https://preview.keenthemes.com/metronic8/demo1/assets/js/custom/intro.js"></script>

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