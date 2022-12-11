@extends('dashboard.layouts.master')
@section('content')

                  <div class="post d-flex flex-column-fluid" id="kt_post">
                       <div id="kt_content_container" class="container">
                            <div class="card">
                                <div class="card-header border-0 pt-5">
                                   <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder fs-3 mb-1">{{ __('admin.add_new')}}</span>
                                  </h3>
                                   <div class="card-toolbar">
                                      <ul class="nav">

                                      </ul>
                                  </div>
                              </div>
  
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                   {!! Form::open(['url' => route('worktimes.store'), 'method' => 'post', 'files'=>true]) !!}
                                  <div class="row">
                                       <div class="col-md-6">
                                           <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.start_time')}}</label>
                                             <div class="mb-5">
                                                 <input  type="text" class="form-control form-control-solid kt_datepicker_2" name="start_time"/>
                                                 <span class="text-danger">{{ $errors->first('start_time') }}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                             <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.end_time')}}</label>
                                                <div class="mb-5">
                                                    <input  type="text" class="form-control form-control-solid kt_datepicker_2"  name="end_time" />
                                                    <span class="text-danger">{{ $errors->first('start_time') }}</span>
                                                </div>
                                            </div>
                                       </div>
              
                                      <div class="card-footer d-flex justify-content-end py-6 px-9">
                                           <button type="submit" class="btn btn-primary" >{{__('admin.save')}}
                                          </button>
                                    </div>
                                </div>
                           </form>
                      </div>
                </div>
         </div>

@endsection
@section('script')

    <script type="text/javascript">

        $(".kt_datepicker_2").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",

        });

    </script>

@endsection
