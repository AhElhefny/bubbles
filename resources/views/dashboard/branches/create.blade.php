@extends('dashboard.layouts.master')
@section('toolbar')
@endsection
@section('content')

            <div class="card card-xl-stretch mb-5 mb-xl-10">
                 <div class="card-header border-0 pt-5">
                      <h3 class="card-title align-items-start flex-column">
                          <span class="card-label fw-bolder fs-3 mb-1">{{ __('admin.add_new') }}</span>
                     </h3>
                     <div class="card-toolbar">

                       </div>
                   </div>

                      {!! Form::open(['url' => route('branches.store'), 'method' => 'post', 'files'=>true]) !!}

                      <div class="card-body py-3">
                           <div class="tab-content">
                               <div class="row">
                                  <div class="col-md-6">
                                      <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.thumbnail')}}</label>
                                       <div class="mb-5">
                                           @include('dashboard.components.image_upload_input', ['input_name' => 'img', 'current_image' => $data->img])
                                       </div>
                                  </div>

                                   <div class="col-md-6">
                                       <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.name')}}</label>
                                           <div class="mb-5 {{ $errors->has('name') ? 'has-error' : '' }}">
                                               <input type="text" name="name" class="form-control form-control-solid"  placeholder="{{__('admin.user_name')}}"/>
                                               <span class="text-danger">{{ $errors->first('name') }}</span>
                                          </div>
                                     </div>

                                      <div class="col-md-6">
                                          <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.email')}}</label>
                                            <div class="mb-5 {{ $errors->has('email') ? 'has-error' : '' }}">
                                                <input type="email" name="email" class="form-control form-control-solid" placeholder="{{__('admin.email')}}"/>
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                           </div>
                                      </div>

                                       <div class="col-md-6">
                                           <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.price_ranges')}}</label>
                                              <div class="mb-5 {{ $errors->has('range_price') ? 'has-error' : '' }}">
                                                 <input type="text" name="range_price" class="form-control form-control-solid" placeholder="{{__('admin.price_ranges')}}"/>
                                                 <span class="text-danger">{{ $errors->first('range_price') }}</span>
                                            </div>
                                       </div>

                                       <div class="col-md-6">
                                           <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.phone')}}</label>
                                              <div class="mb-5 {{ $errors->has('mobile') ? 'has-error' : '' }}">
                                                  <input type="number" name="mobile" class="form-control form-control-solid" placeholder="{{__('admin.phone')}}"/>
                                                  <span class="text-danger">{{ $errors->first('mobile') }}</span>
                                             </div>
                                        </div>

                                        <div class="col-md-6">
                                           <div class="d-flex flex-column mb-8 fv-row">
                                                 <label  class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.categories')}}</label>
                                                     <select class="form-select"  data-control="select2" name="category_id">
                                                        @foreach($categories as $category)
                                                          <option value="{{$category->id}}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                              </div>
                                         </div>

                                         <div class="col-md-6">
                                              <div class="d-flex flex-column mb-8 fv-row">
                                                  <label  class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.cities')}}</label>
                                                      <select class="form-select"  data-control="select2" name="city_id">
                                                         @foreach($cities as $city)
                                                           <option value="{{$city->id}}">{{$city->name}}</option>
                                                         @endforeach
                                                     </select>
                                                </div>
                                           </div>

                                           <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.address')}}</label>
                                           <input id="address" name="address" class="form-control form-control-solid" type="text" placeholder="Enter address here" />
                                           <div>
                                          <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.latitude')}}</label>
                                           <input type="text" name="latitude" class="form-control form-control-solid" id="latitude" readonly />
                                           <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.longitude')}}</label>
                                               <input type="text" name="langitude" class="form-control form-control-solid" id="longitude" readonly />
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

                                            <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.branch_gallary')}}</label>
                                            <div id="input" style="display: flex; justify-content: center;">
                                                {{-- <input type="file" id="images" multiple name="branch_slider[]" class="form-control"/> --}}
                                                <div class="mb-5" style="display: inline-block; margin: 30px;">
                                                    @include('dashboard.components.image_upload_input', ['input_name' => 'branch_slider[0]', 'current_image' => $data->img])
                                                </div>
                                                <div class="mb-5" style="display: inline-block; margin: 30px;">
                                                    @include('dashboard.components.image_upload_input', ['input_name' => 'branch_slider[1]', 'current_image' => $data->img])
                                                </div>
                                                <div class="mb-5" style="display: inline-block; margin: 30px;">
                                                    @include('dashboard.components.image_upload_input', ['input_name' => 'branch_slider[2]', 'current_image' => $data->img])
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
@section('script')
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=AIzaSyDrWqGIyXBP98tkCX9jSRrtzCpVJ-jI6ck&libraries=places"></script>

<script>
    google.maps.event.addDomListener(window, 'load', initialize);
    function initialize() {
        var input = document.getElementById('address');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            // place variable will have all the information you are looking for.

            document.getElementById("latitude").value = place.geometry['location'].lat();
            document.getElementById("longitude").value = place.geometry['location'].lng();
    });
}

    // var processFiles = (input) => {

    //     var $input = document.getElementById("input");
    //     // If has files in this input.
    //     if(input.files) {
    //         for(var file in input.files) {
    //             if(isNaN(file)) return;
    //             var reader = new FileReader();
    //             let $img = document.createElement("img");
    //             $img.setAttribute("class", "review");
    //             reader.onload = function(e) {
    //                 console.log(e);
    //                 $img.setAttribute("src", e.target.result);
    //             }
    //             console.log(file)
    //             reader.readAsDataURL(input.files[file]);
    //             $input.appendChild($img);
    //         }
    //     }

    // }

    // var $input = document.getElementById("images");
    // $input.addEventListener("change", function() {processFiles(this)});

</script>
@endsection
