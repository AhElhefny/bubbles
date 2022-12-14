@extends('dashboard.layouts.master')
@section('toolbar')
@endsection
@section('content')
                         
					    	 <div class="card">
							     <div class="card-header border-0 pt-6">
							 	    <div class="card-title">
									  <!-- <div class="d-flex align-items-center position-relative my-1">
										<span class="svg-icon svg-icon-1 position-absolute ms-6">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
												<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
												<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
											</svg>
										</span>
										<input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search Permission" />
									</div> -->
							    </div>
							
	                   	       <div class="card-toolbar">
			                       <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
									    <a href="{{route('modules.create')}}" class="btn btn-primary" >
									       <span class="svg-icon svg-icon-2">
										      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
										  	       <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="black" />
											       <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black" />
										     </svg>
								  	     </span>
									         {{trans('admin.create_module')}}</a>
							  	       </div>
							    </div>
						  </div>
								
	                       <div class="card-body pt-0">
		                       <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users" style="direction:rtl;">
			                      <thead>
							         <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
								        <th class="min-w-125px">{{trans('admin.name')}}</th>
								         <th class="min-w-125px">{{trans('admin.name')}}</th>
							    	     <th class="text-end min-w-100px">{{__('admin.actions')}}</th>
							        </tr>
						       </thead>
						
		  	                   <tbody class="text-gray-600 fw-bold">
                                    @foreach($rows as $row)											 	 
			 	                   <tr>
									  <td class="d-flex align-items-center">
										 <div class="d-flex flex-column">
											 <span>{{ $row->title }}</span>
										 </div>
									 </td>
									 <td>{{$row->name}}</td>
					
									 <td class="text-end min-w-100px">
									  <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Actions
										  <span class="svg-icon svg-icon-5 m-0"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><polygon points="0 0 24 0 24 24 0 24" /><path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999)" /></g></svg></span><!--end::Svg Icon--></a>
											<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
											<!-- <div class="menu-item px-3">
												<a href="{{route('modules.show',$row->id)}}" class="menu-link px-3">{{trans('admin.show')}}</a>
											</div> -->
											<div class="menu-item px-3">
												<a href="{{route('modules.edit', $row->id)}}" class="menu-link px-3">{{trans('admin.edit')}}</a>
											</div>

											<div class="menu-item px-3">
												<a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_1{{$row->id}}">{{trans('admin.delete')}}</a>				
											</div>
										</div>
									</td>
								</tr>

								    <div id="kt_modal_1{{$row->id}}"  tabindex="-1" class="modal fade">
								  	 <div class="modal-dialog">
									 	  <div class="modal-dialog modal-dialog-centered mw-650px">
										    <div class="modal-content">
											  <div class="modal-header" style="text-align:center;">
												<h5 class="modal-title" >{{__('admin.Delete Confirmation')}}</h5>
												   <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
													    <span class="svg-icon svg-icon-2x"></span>
												    </div>
										  	    </div>
 
											      <div class="modal-body text-center">
												    <p>{{ __('admin.Areyousure')}}</p>
											 	    <div class="text-center pt-15">
											 	      <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="position:relative;float:right;">{{__('admin.Close')}}</button>
													    {!! Form::open(['url' => route('modules.destroy', $row->id), 'method' => 'delete']) !!}
													       <button type="submit" class="btn btn-primary mt-2">{{__('admin.Delete')}}</a>
												     	{!! Form::close() !!}
												     </div>
										   	      </div>
										      </div>
									       </div>
								        </div>
								    </div>
							      @endforeach
							  </tbody>
						</table>
					</div>
			    </div>
		    </div>
	    </div>
    </div>
				
@endsection
@section('after_content')
@endsection
@section('css')

<link href="{{asset('public/dashboard/dist/assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css"/>

@endsection
@section('script')
    <script src="{{asset('public/dashboard/dist/assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    <script>
        $(function (){
            $('#db_table').dataTable({
                "ordering": false
            });
        })
            $(".confirm-delete").click(function (e) {
                e.preventDefault();
                var url = $(this).data("href");
                $("#delete-modal").modal("show");
                $("#delete-link").attr("href", url);
            });

$(document).ready(function(){
 
     $("#modal").click(function(){
    $("#kt_modal_add_user").modal('hide');

  });

 });


 $('#kt_table_users').DataTable({
 	 order:[[0,"desc"]],
 });
    </script>
@endsection


