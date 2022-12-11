<html lang="en">
<head>
<base href="">
<meta name="description" content="The most advanced Bootstrap Admin Theme on Themeforest trusted by 94,000 beginners and professionals. Multi-demo, Dark Mode, RTL support and complete React, Angular, Vue &amp; Laravel versions. Grab your copy now and get life-time updates for free." />
<meta charset="utf-8" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<link href="{{asset('dashboard/dist/assets/plugins/global/plugins.bundle.rtl.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('dashboard/dist/assets/css/style.bundle.rtl.css')}}" rel="stylesheet" type="text/css" />
<title>  {{__('admin.customers')}}</title>
</head>
<body>

    <h4  style="text-align:right; margin:20px;">{{__('admin.customers')}}</h4>
     <div class="d-flex flex-wrap flex-stack pb-7">

          <div class="d-flex flex-wrap my-1">
       </div>
      </div>
      <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">

            </div>
         </div>

            <div class="card-body pt-0" style="direction:rtl;">
               <table class="table align-middle table-row-dashed fs-6 gy-5" id="db_table">
               <thead class="fs-7 text-gray-400 text-uppercase">
                <tr>
                    <th class="min-w-10px">ID</th>
                    <th class="min-w-150px">Name</th>
                    <th class="min-w-150px">Mobile</th>
                    <th class="min-w-90px">Orders</th>
                </tr>
                </thead>
                    <tbody class="fs-6">
                        @foreach($customers as $customer)
                         <tr>
                             <td>{{$customer->id}}</td>
                             <td>{{$customer->name}}</td>
                             <td>{{$customer->mobile}}</td>
                             <td>{{$customer->orders_count}}</td>
                         </tr>
                      </tbody>
                      @endforeach
                  </table>
            </div>
       </div>

<script src="{{asset('dashboard/dist/assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{asset('dashboard/dist/assets/js/scripts.bundle.js')}}"></script>
<script>

$(document).ready(function(){
    window.onafterprint = window.close;
    window.print();

});

</script>
</body>
</html>

