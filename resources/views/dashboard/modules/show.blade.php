@extends('dashboard.layouts.master')
@section('toolbar')
@endsection
@section('content')
  
         <div class="d-flex flex-column flex-xl-row">
			  <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
			 	  <div class="card mb-5 mb-xl-8">
				 	  <div class="card-body pt-15">
					
						<div class="d-flex flex-center flex-column mb-5">
							<a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-1">{{$hospital->name}}</a>
						
							  <div class="fs-5 fw-bold text-muted mb-6">{{$hospital->type}}</div>
							
						    	<div class="d-flex flex-wrap flex-center">
							
								<div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
									<div class="fs-4 fw-bolder text-gray-700">
										<span class="w-75px">{{$hospital->country}}</span>
									
									</div>
									<div class="fw-bold text-muted">Country</div>
								</div>
								
								<div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
									<div class="fs-4 fw-bolder text-gray-700">
										<span class="w-50px">{{$hospital->city}}</span>
										
										
									</div>
									<div class="fw-bold text-muted">City</div>
								</div>
							
								<div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
									<div class="fs-4 fw-bolder text-gray-700">
										<span class="w-50px">{{$hospital->address}}</span>
									</div>
									<div class="fw-bold text-muted">Address</div>
								</div>
							
							</div>
						</div>
					
						<div class="d-flex flex-stack fs-4 py-3">
							<div class="fw-bolder rotate collapsible" data-bs-toggle="collapse" href="#kt_customer_view_details" role="button" aria-expanded="false" aria-controls="kt_customer_view_details">Details
							<span class="ms-2 rotate-180">
								<span class="svg-icon svg-icon-3">
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
										<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<polygon points="0 0 24 0 24 24 0 24" />
											<path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999)" />
										  </g>
									    </svg>
								     </span>

                                </span></div>
						  </div>
					
						<div class="separator separator-dashed my-3"></div>
						    <div id="kt_customer_view_details" class="collapse show">
							    <div class="py-5 fs-6">
								<div class="fw-bolder mt-5">Contact Person</div>
								<div class="text-gray-600">{{$hospital->user->name}}</div>
							
								<div class="fw-bolder mt-5">Admin User</div>
								<div class="text-gray-600">
									<a href="#" class="text-gray-600 text-hover-primary">{{$hospital->admin_name}}</a>
								</div>
							
								<div class="fw-bolder mt-5">Contact Number</div>
								<div class="text-gray-600">{{$hospital->user->phone}}</div>

								<div class="fw-bolder mt-5">Email</div>
								<div class="text-gray-600">{{$hospital->user->email}}</div>
								
								<div class="fw-bolder mt-5">Users Allowed</div>
								<div class="text-gray-600">{{$hospital->number_of_users}}</div>
                    
								<div class="fw-bolder mt-5">Start Date</div>
								<div class="text-gray-600">{{$hospital->start_date}}</div>
								
								<div class="fw-bolder mt-5">End Date</div>
								<div class="text-gray-600">{{$hospital->end_date}}</div>

							</div>
						</div>
						
					</div>
				</div>
			</div>
			
			<div class="flex-lg-row-fluid ms-lg-15">
				<ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-8">
					<li class="nav-item">
						<a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_customer_view_overview_tab">Overview</a>
					</li>
					
					<li class="nav-item">
						<a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_customer_view_overview_events_and_logs_tab">Events &amp; Logs</a>
					</li>
				
					<li class="nav-item ms-auto">
						<!--begin::Action menu-->
						<a href="#" class="btn btn-primary ps-7" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">Actions
						<!--begin::Svg Icon | path: icons/duotone/Navigation/Angle-down.svg-->
						<span class="svg-icon svg-icon-2 me-0">
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<polygon points="0 0 24 0 24 24 0 24" />
									<path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999)" />
								</g>
							</svg>
						</span>
						<!--end::Svg Icon--></a>
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold py-4 w-250px fs-6" data-kt-menu="true">
							
							<div class="menu-item px-5">
								<div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">Payments</div>
							</div>
							
							<div class="menu-item px-5">
								<a href="#" class="menu-link px-5">Create invoice</a>
							</div>
							
							<div class="menu-item px-5">
								<a href="#" class="menu-link flex-stack px-5">Create payments
								<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Specify a target name for future usage and reference"></i></a>
							</div>
							
							<div class="menu-item px-5" data-kt-menu-trigger="hover" data-kt-menu-placement="left-start" data-kt-menu-flip="center, top">
								<a href="#" class="menu-link px-5">
									<span class="menu-title">Subscription</span>
									<span class="menu-arrow"></span>
								</a>
							
								<div class="menu-sub menu-sub-dropdown w-175px py-4">
									
									<div class="menu-item px-3">
										<a href="#" class="menu-link px-5">Apps</a>
									</div>
									
									<div class="menu-item px-3">
										<a href="#" class="menu-link px-5">Billing</a>
									</div>
									
									<div class="menu-item px-3">
										<a href="#" class="menu-link px-5">Statements</a>
									</div>
								
									<div class="separator my-2"></div>
									
									<div class="menu-item px-3">
										<div class="menu-content px-3">
											<label class="form-check form-switch form-check-custom form-check-solid">
												<input class="form-check-input w-30px h-20px" type="checkbox" value="" name="notifications" checked="checked" id="kt_user_menu_notifications" />
												<span class="form-check-label text-muted fs-6" for="kt_user_menu_notifications">Notifications</span>
											</label>
										</div>
									</div>
								</div>
							</div>
							
							<div class="separator my-3"></div>
						
							<div class="menu-item px-5">
								<div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">Account</div>
							</div>
							
							<div class="menu-item px-5">
								<a href="#" class="menu-link px-5">Reports</a>
							</div>
						
							<div class="menu-item px-5 my-1">
								<a href="#" class="menu-link px-5">Account Settings</a>
							</div>
							
							<div class="menu-item px-5">
								<a href="#" class="menu-link text-danger px-5">Delete customer</a>
							</div>
						</div>
					
					</li>
				</ul>
				
				<div class="tab-content" id="myTabContent">
					<!--begin:::Tab pane-->
					<div class="tab-pane fade show active" id="kt_customer_view_overview_tab" role="tabpanel">
						<!--begin::Card-->
						<div class="card pt-4 mb-6 mb-xl-9">
							<!--begin::Card header-->
							<div class="card-header border-0">
								<!--begin::Card title-->
								<div class="card-title">
									<h2>Payment Records</h2>
								</div>
						   </div>
						
							<div class="card-body pt-0 pb-5">
								<!--begin::Table-->
								<table class="table align-middle table-row-dashed gy-5" id="kt_table_customers_payment">
									<!--begin::Table head-->
									<thead class="border-bottom border-gray-200 fs-7 fw-bolder">
										<!--begin::Table row-->
										<tr class="text-start text-muted text-uppercase gs-0">
											<th class="min-w-100px">Invoice No.</th>
											<th>Status</th>
											<th>Amount</th>
											<th class="min-w-100px">Date</th>
											<th class="text-end min-w-100px pe-4">Actions</th>
										</tr>
									</thead>
									
									<tbody class="fs-6 fw-bold text-gray-600">
										<tr>
											<td>
												<a href="#" class="text-gray-600 text-hover-primary mb-1">4494-9476</a>
											</td>
											
											<td>
												<span class="badge badge-light-warning">Pending</span>
											</td>
											<!--end::Status=-->
											<!--begin::Amount=-->
											<td>$8,700.00</td>
											<!--end::Amount=-->
											<!--begin::Date=-->
											<td>01 Sep 2020, 4:58 pm</td>
											<!--end::Date=-->
											<!--begin::Action=-->
											<td class="pe-0 text-end">
												<a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Actions
												<!--begin::Svg Icon | path: icons/duotone/Navigation/Angle-down.svg-->
												<span class="svg-icon svg-icon-5 m-0">
													<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
														<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
															<polygon points="0 0 24 0 24 24 0 24" />
															<path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999)" />
														</g>
													</svg>
												</span>
												<!--end::Svg Icon--></a>
												<!--begin::Menu-->
												<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
													<!--begin::Menu item-->
													<div class="menu-item px-3">
														<a href="../../demo1/dist/apps/customers/view.html" class="menu-link px-3">View</a>
													</div>
													<!--end::Menu item-->
													<!--begin::Menu item-->
													<div class="menu-item px-3">
														<a href="#" class="menu-link px-3" data-kt-customer-table-filter="delete_row">Delete</a>
													</div>
													<!--end::Menu item-->
												</div>
												<!--end::Menu-->
											</td>
											<!--end::Action=-->
										</tr>
										<!--end::Table row-->
									</tbody>
									<!--end::Table body-->
								</table>
								<!--end::Table-->
							</div>
							<!--end::Card body-->
						</div>
						<!--end::Card-->
			
					</div>
					<!--end:::Tab pane-->
					<!--begin:::Tab pane-->
					<div class="tab-pane fade" id="kt_customer_view_overview_events_and_logs_tab" role="tabpanel">
						<!--begin::Card-->
						<div class="card pt-4 mb-6 mb-xl-9">
							<!--begin::Card header-->
							<div class="card-header border-0">
								<!--begin::Card title-->
								<div class="card-title">
									<h2>Logs</h2>
								</div>
								
							</div>
							<!--end::Card header-->
							<!--begin::Card body-->
							<div class="card-body py-0">
								<!--begin::Table wrapper-->
								<div class="table-responsive">
									<!--begin::Table-->
									<table class="table align-middle table-row-dashed fw-bold text-gray-600 fs-6 gy-5" id="kt_table_customers_logs">
										<!--begin::Table body-->
										<tbody>
									
											<tr>
												<!--begin::Badge=-->
												<td class="min-w-70px">
													<div class="badge badge-light-danger">500 ERR</div>
												</td>
												<!--end::Badge=-->
												<!--begin::Status=-->
												<td>POST /v1/invoice/in_9980_6705/invalid</td>
												<!--end::Status=-->
												<!--begin::Timestamp=-->
												<td class="pe-0 text-end min-w-200px">25 Oct 2021, 5:30 pm</td>
												<!--end::Timestamp=-->
											</tr>
											<!--end::Table row-->
										</tbody>
										<!--end::Table body-->
									</table>
									<!--end::Table-->
								</div>
								<!--end::Table wrapper-->
							</div>
							<!--end::Card body-->
						</div>

					</div>
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

@endsection
