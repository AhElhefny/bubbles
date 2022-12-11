@extends('dashboard.layouts.master')
@section('toolbar')
@endsection
@section('content')

       <div class="card card-xl-stretch mb-5 mb-xl-10">
            @if(Session::has('success'))
               <div class="col-sm-6">
               <div class="alert alert-success text-center"><em> {!! session('success') !!}</em></div>
           </div>
           @endif

            <div class="card-header border-0 pt-5">
               <h3 class="card-title align-items-start flex-column">
                  <span class="card-label fw-bolder fs-3 mb-1">{{$page_title}}</span>
              </h3>
                  <div class="card-toolbar">
                 </div>
             </div>

                {!! Form::open(['url' => route('settings.update'), 'method' =>'post', 'files'=>true]) !!}

              <div class="card-body py-3">
                  <div class="tab-content">
                      <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.start_time')}}</label>
                            <div class="mb-5">
                                 <input type="text" class="form-control" placeholder="{{ __('admin.start_time') }}" name="starttime" value="{{ $gs->starttime }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                             <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.end_time')}}</label>
                                 <div class="mb-5">
                                     <input type="text" class="form-control" placeholder="{{__('admin.end_time') }}" name="endtime" value="{{ $gs->endtime }}">
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
