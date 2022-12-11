@extends('dashboard.customers.layout')
@section('sub_content')
    <!--begin::details View-->
    <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
        <!--begin::Card header-->
        <div class="card-header cursor-pointer">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">الطلبات</h3>
            </div>
            <!--end::Card title-->
            <!--begin::Action-->
        {{--            <a href="{{route('customers.edit', $customer->id)}}" class="btn btn-primary align-self-center">Edit Profile</a>--}}
        <!--end::Action-->
        </div>
        <!--begin::Card header-->
        <!--begin::Card body-->
        <div class="card-body p-9 pagination-content">
             @include('dashboard.customers.partials.orders_table')
        </div>
        <!--end::Card body-->
    </div>
    <!--end::details View-->
@endsection

