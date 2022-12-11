@extends('admin.layouts.master')
@section('content')
                            <div id="kt_content_container" class="container">
                                <div class="card mb-5 mb-xl-10">
								   <div class="card-body pt-9 pb-0">
								     <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
								      <div class="me-7 mb-4">
                                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                              @if($user->avatar)
                                                <img src="{{$user->avatar}}" alt="image" />
                                                <div class="bg-{{customerStatusClass($user->user_name)}} position-absolute border border-4 border-white h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3"></div>
                                                     @else
                                                     @php

                                                      $firstLetterOfCustomer = strtoupper(substr(Str::slug($user->name), 0, 1));
                                                      $customerBgColor = randomBootstrapColorsLabel($user->id);
                                                
                                                     @endphp
                                                    <span class="symbol-label fs-2x fw-bold text-{{$customerBgColor}} bg-light-{{$customerBgColor}}">{{$firstLetterOfCustomer}}</span>
                                                    <div class="bg-{{customerStatusClass($user->name)}} position-absolute border border-4 border-white h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3"></div>
                                                       @endif
                                                  </div>
                                              </div>
                                        
											  <div class="flex-grow-1">
												   <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
													  <div class="d-flex flex-column">
														    <div class="d-flex align-items-center mb-2">
														    	<a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bolder me-1">{{$user->name}}</a>
                                                            </div>
													
														   <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
															  <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
															     <span class="svg-icon svg-icon-4 me-1">
															 	    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																    	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		    <polygon points="0 0 24 0 24 24 0 24" />
																		    <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																	    	<path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
																	     </g>
															    	</svg>
															   </span>
															       {{ $user->user_type }}</a>
                                                                 <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
														     	 <span class="svg-icon svg-icon-4 me-1">
															       <i class="fa fa-phone"></i>
														    	 </span>
															         {{$user->phone}}</a>
														     	 <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
															       <span class="svg-icon svg-icon-4 me-1">
															    	 <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																    	<path d="M11.575,21.2 C6.175,21.2 2.85,17.4 2.85,12.575 C2.85,6.875 7.375,3.05 12.525,3.05 C17.45,3.05 21.125,6.075 21.125,10.85 C21.125,15.2 18.825,16.925 16.525,16.925 C15.4,16.925 14.475,16.4 14.075,15.65 C13.3,16.4 12.125,16.875 11,16.875 C8.25,16.875 6.85,14.925 6.85,12.575 C6.85,9.55 9.05,7.1 12.275,7.1 C13.2,7.1 13.95,7.35 14.525,7.775 L14.625,7.35 L17,7.35 L15.825,12.85 C15.6,13.95 15.85,14.825 16.925,14.825 C18.25,14.825 19.025,13.725 19.025,10.8 C19.025,6.9 15.95,5.075 12.5,5.075 C8.625,5.075 5.05,7.75 5.05,12.575 C5.05,16.525 7.575,19.1 11.575,19.1 C13.075,19.1 14.625,18.775 15.975,18.075 L16.8,20.1 C15.25,20.8 13.2,21.2 11.575,21.2 Z M11.4,14.525 C12.05,14.525 12.7,14.35 13.225,13.825 L14.025,10.125 C13.575,9.65 12.925,9.425 12.3,9.425 C10.65,9.425 9.45,10.7 9.45,12.375 C9.45,13.675 10.075,14.525 11.4,14.525 Z" fill="#000000" />
															    	 </svg>
                                                                   </span>
															           {{$user->email}}</a>
													  	            </div>
													            </div>
												            </div>
										  	            </div>
										          </div>
									
								    	          <div class="d-flex overflow-auto h-55px">
											         <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
												          <li class="nav-item">
													          <a class="nav-link text-active-primary me-6 active" href="">Overview</a>
												           </li>
											            </ul>
                                                    </div>
									            </div>
								           </div>
								 
										 <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
										     <div class="card-header cursor-pointer">
											 	 <div class="card-title m-0">
												 	 <h3 class="fw-bolder m-0">Profile Details</h3>
												 </div>
										    </div>
									
										    <div class="card-body p-9">
										 	   <div class="row mb-7">
												 <label class="col-lg-4 fw-bold text-muted">Full Name</label>
												     <div class="col-lg-8">
													     <span class="fw-bolder fs-6 text-gray-800">{{$user->name}}</span>
												     </div>
										   	   </div>
											
											   <div class="row mb-7">
												   <label class="col-lg-4 fw-bold text-muted">City</label>
												      <div class="col-lg-8 fv-row">
												    	 <span class="fw-bold text-gray-800 fs-6">{{ $user->city->name }}</span>
												     </div>
										 	    </div>
											
											  <div class="row mb-7">
												   <label class="col-lg-4 fw-bold text-muted">Contact Phone
												    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i></label>
												     <div class="col-lg-8 d-flex align-items-center">
												    	 <span class="fw-bolder fs-6 text-gray-800 me-2">{{$user->phone}}</span>
												   </div>
											   </div>
										
										   	   <div class="row mb-7">
											   	  <label class="col-lg-4 fw-bold text-muted">Email</label>
												     <div class="col-lg-8">
												        <a href="#" class="fw-bold fs-6 text-gray-800 text-hover-primary">{{$user->email}}</a>
												     </div>
										  	  </div>

								
						 				
    
@endsection

@section('after_content')
@endsection
@section('css')

    <link href="{{asset('dashboard/dist/assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('script')
    <script src="{{asset('dashboard/dist/assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>

@endsection
