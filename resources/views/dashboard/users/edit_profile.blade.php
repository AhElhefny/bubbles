@extends('dashboard.layouts.master')
@section('toolbar')
@endsection
@section('content')
 <div class="card card-xl-stretch mb-5 mb-xl-10">
     <div class="card-header border-0 pt-5">
         <h3 class="card-title align-items-start flex-column">
             <span class="card-label fw-bolder fs-3 mb-1">{{__('admin.editprofile')}}</span>
         </h3>
           <div class="card-toolbar">
           </div>
      </div>

      {!! Form::open(['url' => route('userprofile.update', $data->id), 'method' => 'put', 'files'=>true ,'class'=>'update-ajax-request']) !!}
                
      <div class="card-body py-3">
         <div class="row">
             <div class="row">
                <div class="col-md-6 fv-row">
                   <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.name')}}</label>
                     <div class="mb-5 {{ $errors->has('name') ? 'has-error' : '' }}">
                         <input type="text" name="name" class="form-control form-control-solid" value="{{old('name',$data->name)}}"/>
                         <span class="text-danger">{{ $errors->first('name') }}</span>
                     </div>
               </div>
         </div>
        
         <div class="row">
            <div class="col-md-6 fv-row">
                 <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.phone')}}</label>
                    <div class="mb-5 {{ $errors->has('email') ? 'has-error' : '' }}">
                       <input type="number" name="mobile" class="form-control form-control-solid" value="{{old('mobile',$data->mobile)}}" />
                       <span class="text-danger">{{ $errors->first('phone') }}</span>
                 </div>
             </div>
        </div> 

          <div class="row">
             <div class="col-md-6 fv-row">
                  <label class="form-label fs-6 fw-bolder text-gray-700 mb-3">{{__('admin.email')}}</label>
                    <div class="mb-5 {{ $errors->has('email') ? 'has-error' : '' }}">
                        <input type="email" name="email" class="form-control form-control-solid" value="{{old('email',$data->email)}}" />
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                   </div>
              </div>
         </div>

          <div class="row">
               <div class="col-md-6 fv-row">
                   <label class="required fw-bold fs-6 mb-2">{{__('admin.password')}}</label>
                      <div class="input-group input-group-solid mb-5" id="show_hide_password" >
                           <input type="password" class="form-control" name="password" aria-label="Paaword"/>
                           <span class="input-group-text"><a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a></span>
                      </div>
                  </div>
               </div>
         </div>
     </div>

      <div class="card-footer d-flex justify-content-end py-6 px-9">
           <button type="reset" class="btn btn-light btn-active-light-primary me-2"  onClick="window.location.href=window.location.href">Discard</button>
           <button type="submit" class="btn btn-primary" id="submit">Save </button>
       </div>
           {!! Form::close() !!}
     </div>


@endsection
@section('script')
<script src="{{asset('dashboard/dist/assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script>
 $(function (){

      $('#db_table').dataTable({
          "ordering": false
     });
  })

  $(document).ready(function(){
 
     $("#modal").click(function(){
     $("#kt_modal_add_user").modal('hide');

});

$("#show_hide_password a").on('click', function(event) {

	event.preventDefault();
	if($('#show_hide_password input').attr("type") == "text"){
		$('#show_hide_password input').attr('type', 'password');
		$('#show_hide_password i').addClass( "fa-eye-slash" );
		$('#show_hide_password i').removeClass( "fa-eye" );
	}else if($('#show_hide_password input').attr("type") == "password"){
		$('#show_hide_password input').attr('type', 'text');
		$('#show_hide_password i').removeClass( "fa-eye-slash" );
		$('#show_hide_password i').addClass( "fa-eye" );
	 
	}
     });
  });
 

</script>
@endsection