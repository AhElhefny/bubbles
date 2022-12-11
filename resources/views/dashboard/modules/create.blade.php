@extends('dashboard.layouts.master')
@section('toolbar')
@endsection
@section('content')

<div class="card card-xl-stretch mb-5 mb-xl-12">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
             <span class="card-label fw-bolder fs-3 mb-1">{{trans('admin.create_module')}}</span>
        </h3>
        <div class="card-toolbar">
            {{trans('admin.new_module')}}
        </div>
    </div>
    {!! Form::open(['url' => route('modules.store'), 'method' => 'post', 'files'=>true]) !!}
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
          <div class="row g-9 mb-8">
              <div class="col-sm-6">
                  <label class="required fw-bold fs-6 mb-2">{{__('admin.name')}}</label>
                     <input type="text" name="name" class="form-control form-control-solid mb-3 mb-lg-0" />
                   <small class="text-danger">{{ $errors->first('title') }}</small>
              </div>
              <div class="col-sm-6">
                 <label class="required fw-bold fs-6 mb-2">{{__('admin.title')}}</label>
                     <input type="text" name="title" class="form-control form-control-solid mb-3 mb-lg-0" />
                  <small class="text-danger">{{ $errors->first('title') }}</small>
              </div>
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
