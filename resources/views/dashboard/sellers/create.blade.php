@extends('dashboard.layouts.master')
@section('toolbar')
@endsection
@section('content')

              <div class="card card-xl-stretch mb-5 mb-xl-10">
                  <div class="card-header border-0 pt-5">
                     <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">{{$data->id? __('admin.edit_info'): __('admin.add_new')}}</span>
                     </h3>
                       <div class="card-toolbar">
                      </div>
                 </div>

                       {!! Form::open(['url' => route('sellers.store'), 'method' => 'post', 'files'=>true]) !!}

                       <div class="card-body py-3">
                           <div class="tab-content">
                              <div class="row">
                                  <div class="col-md-6">
                                    <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.user_name')}}</label>
                                      <div class="mb-5 {{ $errors->has('name') ? 'has-error' : '' }}">
                                           <input type="text" name="name" class="form-control form-control-solid"  placeholder="{{__('admin.user_name')}}"/>
                                          <span class="text-danger">{{ $errors->first('name') }}</span>
                                      </div>
                                 </div>

                                   <div class="col-md-6">
                                      <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.email')}}</label>
                                        <div class="mb-5 {{ $errors->has('email') ? 'has-error' : '' }}">
                                            <input type="email" name="email" class="form-control form-control-solid"  placeholder="{{__('admin.email')}}"/>
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        </div>
                                   </div>

                                   <div class="col-md-6">
                                       <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.phone')}}</label>
                                          <div class="mb-5 {{ $errors->has('mobile') ? 'has-error' : '' }}">
                                             <input type="number" name="mobile" class="form-control form-control-solid"  placeholder="{{__('admin.phone')}}"/>
                                             <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                        </div>
                                    </div>

{{--                                     <div class="col-md-6">--}}
{{--                                         <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.number_of_branches')}}</label>--}}
{{--                                         <div class="mb-5 {{ $errors->has('number_of_branches') ? 'has-error' : '' }}">--}}
{{--                                             <input type="number" name="number_of_branches" class="form-control form-control-solid"  placeholder="{{__('admin.number_of_branches')}}"/>--}}
{{--                                             <span class="text-danger">{{ $errors->first('number_of_branches') }}</span>--}}
{{--                                         </div>--}}
{{--                                    </div>--}}

                                     <div class="col-md-6">
                                         <div class="d-flex flex-column mb-8 fv-row">
                                            <label  class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.cities')}}</label>
                                                <select class="form-select"  data-control="select2" name="city_id">
                                                   @foreach($cities as $city)
                                                    <option value="{{$city->id}}">{{ $city->name }}</option>
                                                   @endforeach
                                               </select>
                                          </div>
                                      </div>

                                      <div class="col-md-6">
                                         <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.password')}}</label>
                                           <div class="mb-5 {{ $errors->has('password') ? 'has-error' : '' }}">
                                                <input type="password" name="password" class="form-control form-control-solid"  placeholder="{{__('admin.password')}}"/>
                                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                           </div>
                                       </div>

                                         <div class="col-md-6">
                                            <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.password_confirmation')}}</label>
                                                <div class="mb-5">
                                                   <input type="password" name="password_confirmation" class="form-control form-control-solid"  placeholder="{{__('admin.password_confirmation')}}"/>
                                               </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="card-footer d-flex justify-content-end py-6 px-9">
                                  <button type="reset" class="btn btn-light btn-active-light-primary me-2"  onClick="window.location.href=window.location.href">{{__('admin.discard')}}</button>
                                  <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">{{__('admin.save')}}</button>
                             </div>
                                 {!! Form::close() !!}
                         </div>
@endsection

