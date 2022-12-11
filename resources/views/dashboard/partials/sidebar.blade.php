@inject('pages','App\Models\Page')
@php
    $allpages = $pages->all();
@endphp
     <div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
        <div class="aside-logo flex-column-auto" id="kt_aside_logo">
	        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
		           <span class="svg-icon svg-icon-1 rotate-180">
		  	            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
				              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				                  <polygon points="0 0 24 0 24 24 0 24" />
				      	          <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
				                  <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.5" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
			    	         </g>
			          </svg>
	               </span>
	    	 </div>
       </div>

        <div class="aside-menu flex-column-fluid">
             <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
                 <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
		              @if(Auth::user()->user_type == 'admin')
		             <div class="menu-item">
						  <a class="menu-link" href="{{url('admin')}}">
							  <span class="menu-icon">
							   <span class="svg-icon svg-icon-2">
							 	  <i class="fas fa-home"></i>
							   </span>
						    </span>
							  <span class="menu-title">الرئيسية</span>
				       	  </a>
		 	          </div>
			          @endif

			  	     @if(Auth::user()->user_type =='seller')
				     <div class="menu-item">
						  <a class="menu-link" href="{{route('dashboard')}}">
						    <span class="menu-icon">
							 <span class="svg-icon svg-icon-2">
								<i class="fas fa-home"></i>
							</span>
							 </span>
							 <span class="menu-title">الرئيسية</span>
						 </a>
					  </div>
					  @endif

					   @if(Auth::user()->hasPermissionName('read_order'))
					     <div class="menu-item">
							 <a class="menu-link" href="{{route('orders.index')}}">
							   <span class="menu-icon">
								 <span class="svg-icon svg-icon-2">
									 <i class="fas fa-cart-arrow-down"></i>
								  </span>
							   </span>
							 	<span class="menu-title">{{__('admin.weborders')}}</span>
							  </a>
						  </div>
					      @endif
			          
					     @if(Auth::user()->hasPermissionName('read_category'))
				         <div class="menu-item">
							  <a class="menu-link" href="{{route('category.index')}}">
								   <span class="menu-icon">
									   <span class="svg-icon svg-icon-2">
									 	  <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											  <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
											  <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
										   </svg>
									   </span>
								    </span>
								    <span class="menu-title">{{__('admin.categories')}}</span>
						 	    </a>
					       </div>
					       @endif
					       
                    	     @if(Auth::user()->user_type =="seller")
	                         @if(Auth::user()->hasPermissionName('read_service') || Auth::user()->user_type == 'seller')
				             <div class="menu-item">
							     <a class="menu-link" href="{{route('services.index')}}">
								      <span class="menu-icon">
									     <span class="svg-icon svg-icon-2">
										      <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											    <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
											    <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
										   </svg>
								 	  </span>
								  </span>
									 <span class="menu-title">{{__('admin.services')}}</span>
						    	 </a>
			              </div>
			              @endif
			              @endif

						   @if(Auth::user()->hasPermissionName('read_seller'))
					  	  <div class="menu-item">
						 	   <a class="menu-link" href="{{route('sellers.index')}}">
							 	   <span class="menu-icon">
								 	   <span class="svg-icon svg-icon-2">
										   <i class="fas fa-users"></i>
									  </span>
								 </span>
								 <span class="menu-title">{{__('admin.sellers')}}</span>
							 </a>
						 </div>
						 @endif
						 
						 @if(Auth::user()->user_type =="seller")
					     @if(Auth::user()->hasPermissionName('read_branch'))
			              <div class="menu-item">
						       <a class="menu-link" href="{{route('branches.index')}}">
						 	        <span class="menu-icon">
                                     <span class="svg-icon svg-icon-2">
							             <i class="fas fa-users"></i>
						            </span>
							     </span>
						   	      <span class="menu-title">{{__('admin.branches')}}</span>
					           </a>
				          </div>
				         @endif
				         @endif
 
						 @if(Auth::user()->user_type =="seller")
			               <div class="menu-item">
						         <a class="menu-link" href="{{route('worktimes.index')}}">
						 	        <span class="menu-icon">
                                     <span class="svg-icon svg-icon-2">
							             <i class="fas fa-users"></i>
						            </span>
							     </span>
						   	      <span class="menu-title">{{__('admin.worktimes')}}</span>
					           </a>
				         </div>
				         @endif

				         @if(Auth::user()->hasPermissionName('read_customer'))
				        <div class="menu-item">
				   	         <a class="menu-link" href="{{route('customers.index')}}">
						        <span class="menu-icon">
							        <span class="svg-icon svg-icon-2">
							           <i class="fas fa-users"></i>
							       </span>
					  	        </span>
					             <span class="menu-title">{{__('admin.customers')}}</span>
					         </a>
			            </div>
			            @endif

						@if(Auth::user()->hasPermissionName('read_bank_account'))
						<div class="menu-item">
							<a class="menu-link" href="{{route('banks.index')}}">
								<span class="menu-icon">
									<span class="svg-icon svg-icon-2">
									<i class="fa fa-building"></i>
								</span>
								</span>
									<span class="menu-title">{{__('admin.bank_accounts')}}</span>
								</span>
							</a>
						</div>
					  @endif

					 @if(Auth::user()->hasPermissionName('read_promocode'))
					 <div class="menu-item">
							<a class="menu-link" href="{{route('promocodes.index')}}">
								<span class="menu-icon">
								<span class="svg-icon svg-icon-2">
								<i class="fas fa-tags"></i>
								</span>
							</span>
								<span class="menu-title">{{__('admin.promocodes')}}</span>
						</a>
					</div>
					@endif

    	            @if(Auth::user()->hasPermissionName('read_notification'))
	                <div class="menu-item">
	     	            <a class="menu-link" href="{{ route('notification-messages.index') }}">
	                       <span class="menu-icon">
	                          <span class="svg-icon svg-icon-2">
			                     <i class="fas fa-bell"></i>
			                  </span>
                            </span>
		        	          <span class="menu-title">{{__('admin.app_notifications')}}</span>
	 	                  </a>
                       </div>
                      @endif

				      @if(Auth::user()->hasPermissionName('read_contact'))
				       <div class="menu-item">
						      <a class="menu-link" href="{{ url('admin/contacts')}}">
							      <span class="menu-icon">
								      <span class="svg-icon svg-icon-2">
								         <i class="fas fa-phone"></i>
							         </span>
							      </span>
						     	  <span class="menu-title">{{__('admin.contactus')}}</span>
						     </a>
					   </div>
				        @endif

					     @if(Auth::user()->hasPermissionName('read_page'))
                    
						   <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
						  	    <span class="menu-link">
								    <span class="menu-icon">
								     <span class="svg-icon svg-icon-2">
								         <i class="fas fa-pen-nib"></i>
								     </span>
						  	     </span>
							        <span class="menu-title">{{__('admin.pages')}}</span>
						    	    <span class="menu-arrow"></span>
					    	    </span>

				          	  <div class="menu-sub menu-sub-accordion">
						    	 <div class="menu-item">
									 <a  class="menu-link" href="{{ url('admin/settings/home_page') }}">
									   <span class="menu-bullet">
									  	  <span class="bullet bullet-dot"></span>
									   </span>
									   <span class="menu-title">{{__('admin.home_page')}}</span>
								     </a>
							   </div>
					           @foreach($allpages as $page)
					           <div class="menu-item">
							       <a class="menu-link" href="{{ url('admin/pages/'.$page->id.'/edit') }}">
								        <span class="menu-bullet">
								        <span class="bullet bullet-dot"></span>
								      </span>
								       <span class="menu-title">{{ $page->title }}</span>
						 	       </a>
					 	      </div>
					          @endforeach
					      </div>
					  </div>
					  @endif

					   @if(Auth::user()->hasPermissionName('read_financial'))
					    <div class="menu-item">
					         <a class="menu-link" href="{{ url('admin/financial')}}">
							       <span class="menu-bullet">
								     <span class="bullet bullet-dot"></span>
							      </span>
							      <span class="menu-title">{{__('admin.payment_methods')}}</span>
					   	     </a>
					    </div>
					    @endif

                        @if(Auth::user()->user_type == 'admin')
                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                            <span class="menu-link">
                                  <span class="menu-icon">
                                     <span class="svg-icon svg-icon-2">
				            	         <i class="fas fa-unlock-alt"></i>
				                    </span>
				                 </span>
                                 <span class="menu-title">{{__('admin.users_staf')}}</span>
                                 <span class="menu-arrow"></span>
                           	</span>
                           	@if(Auth::user()->hasPermissionName('read_user'))
                           	<div class="menu-sub menu-sub-accordion">
                               <div class="menu-item">
                                   <a class="menu-link" href="{{ route('users.index') }}">
                                        <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                       </span>
                                      <span class="menu-title">{{__('admin.users_staf')}}</span>
                                  </a>
                            </div>
                            @endif
                            @if(Auth::user()->hasPermissionName('read_permission'))
                             <div class="menu-item">
                                  <a class="menu-link" href="{{route('roles.index')}}">
                                        <span class="menu-bullet">
                                         <span class="bullet bullet-dot"></span>
                                        </span>
                                      <span class="menu-title">{{__('admin.permissions')}} </span>
                                  </a>
                    	    </div>
                    	    @endif
                    	    <!--@if(Auth::user()->hasPermissionName('read_activitylog'))-->
                         <!--  <div class="menu-item">-->
                         <!--          <a class="menu-link" id="kt_docs8_sweetalert_basic"  href="#">-->
                         <!--               <span class="menu-bullet">-->
                         <!--                 <span class="bullet bullet-dot"></span>-->
                         <!--              </span>-->
                         <!--              <span class="menu-title">{{__('admin.activitylog')}}</span>-->
                         <!--          </a>-->
                         <!--      </div>-->
                         <!--        @endif-->
                            </div>
                        </div>
                         @endif

                         <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                         	@if(Auth::user()->hasPermissionName('read_setting'))
					 	    <span class="menu-link">
							   <span class="menu-icon">
								   <span class="svg-icon svg-icon-2">
							     	  <i class="fas fa-cogs"></i>
						          </span>
							  </span>
							   <span class="menu-title">{{__('admin.website_settings')}}</span>
							  <span class="menu-arrow"></span>
					 	 </span>
						  @endif
                        <div class="menu-sub menu-sub-accordion">
			   	           @if(Auth::user()->hasPermissionName('read_slider'))
			   	           <div class="menu-item">
					           <a class="menu-link" href="{{ route('slider.index') }}">
						              <span class="menu-bullet">
						              <span class="bullet bullet-dot"></span>
						           </span>
				              	    <span class="menu-title">{{__('admin.slider')}}</span>
					            </a>
			   	          </div>
			   	           @endif

			   	  <!--         @if(Auth::user()->hasPermissionName('read_worktime'))-->
						   <!--<div class="menu-item">-->
								 <!--<a class="menu-link" href="{{ url('admin/settings/worktimes') }}">-->
									<!-- <span class="menu-bullet">-->
									<!--	 <span class="bullet bullet-dot"></span>-->
									<!-- </span>-->
								 <!--	 <span class="menu-title">{{__('admin.worktimes_settings')}}</span>-->
								 <!--</a>-->
						   <!--   </div>-->
						   <!--  @endif-->
						     
						    <!-- @if(Auth::user()->hasPermissionName('read_order'))-->
          <!--                         <div class="menu-item">-->
          <!--                             <a class="menu-link" href="{{ route('order.settings') }}">-->
									 <!--<span class="menu-bullet">-->
										<!-- <span class="bullet bullet-dot"></span>-->
									 <!--</span>-->
          <!--                                 <span class="menu-title">{{__('admin.order_settings')}}</span>-->
          <!--                             </a>-->
          <!--                         </div>-->
          <!--                     @endif-->
					    </div>
				    </div>
                    @if(Auth::user()->hasPermissionName('read_region') || Auth::user()->hasPermissionName('read_city'))
                     <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
    	                 <span class="menu-link">
							 <span class="menu-icon">
							   <span class="svg-icon svg-icon-2">
							  	  <i class="fa fa-map-marker"></i>
						     </span>
						  </span>
	             	       <span class="menu-title">{{__('admin.regions')}}</span>
                           <span class="menu-arrow"></span>
                       </span>
                      <div class="menu-sub menu-sub-accordion">
                        	@if(Auth::user()->hasPermissionName('read_region'))
                         <div class="menu-item">
	               	         <a class="menu-link" href="{{route('region.index')}}">
		 		                  <span class="menu-bullet">
			    	               <span class="bullet bullet-dot"></span>
			 	               </span>
		     	                  <span class="menu-title">{{__('admin.regions')}}</span>
		                       </a>
                          </div>
                          @endif
                          @if(Auth::user()->hasPermissionName('read_city'))
                            <div class="menu-item">
					  	         <a class="menu-link" href="{{route('cities.index')}}">
						  	          <span class="menu-bullet">
							  	          <span class="bullet bullet-dot"></span>
							         </span>
                                       <span class="menu-title">{{__('admin.cities')}}</span>
				 	   	          </a>
				            </div>
				          @endif
		  	           </div>
		           </div>
		           @endif
              </div>
         </div>
      </div>
  </div>
<script>

const button = document.getElementById('kt_docs_sweetalert_basic');
const button2 = document.getElementById('kt_docs2_sweetalert_basic');
const button3 = document.getElementById('kt_docs3_sweetalert_basic');
const button4 = document.getElementById('kt_docs4_sweetalert_basic');
const button7 = document.getElementById('kt_docs7_sweetalert_basic');
const button8 = document.getElementById('kt_docs8_sweetalert_basic');


button.addEventListener('click', e =>{
    e.preventDefault();

    Swal.fire({
        text: "عفواً هذي الخاصية غير متاحة لك يرجى طلب تطويرها من شركة بديع الحلول",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "Ok",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
});


button2.addEventListener('click', e =>{
    e.preventDefault();

    Swal.fire({
        text: "عفواً هذي الخاصية غير متاحة لك يرجى طلب تطويرها من شركة بديع الحلول",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "Ok",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
});

button3.addEventListener('click', e =>{
    e.preventDefault();

    Swal.fire({
        text: "عفواً هذي الخاصية غير متاحة لك يرجى طلب تطويرها من شركة بديع الحلول",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "Ok",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
});

button4.addEventListener('click', e =>{
    e.preventDefault();

    Swal.fire({
        text: "عفواً هذي الخاصية غير متاحة لك يرجى طلب تطويرها من شركة بديع الحلول",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "Ok",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
});



button7.addEventListener('click', e =>{
    e.preventDefault();

    Swal.fire({
        text: "عفواً هذي الخاصية غير متاحة لك يرجى طلب تطويرها من شركة بديع الحلول",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "Ok",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
});

button8.addEventListener('click', e =>{
    e.preventDefault();

    Swal.fire({
        text: "عفواً هذي الخاصية غير متاحة لك يرجى طلب تطويرها من شركة بديع الحلول",
        icon: "success",
        buttonsStyling: false,
        confirmButtonText: "Ok",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    });
});


</script>
