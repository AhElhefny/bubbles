<div class="card card-flush">
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table id="kt_project_users_table" class="table align-middle table-row-dashed fs-6 gy-5 no-footer">
                <thead class="fs-7 text-gray-400 text-uppercase">
                    <tr>
                      <th class="min-w-10px">
                         @if(!is_null(request()->get('status')))
                           <input id="parent-checkbox-table" type="checkbox">
                         @endif
                         &nbsp;ID
                    </th>
                    <th class="min-w-10px">{{__('admin.created_at')}}</th>
                    <th class="min-w-10px">{{__('admin.deliverydate')}}</th>
                    <th class="min-w-10px">{{__('admin.customer')}}</th>
                    <th class="min-w-10px">{{__('admin.total')}}</th>
                    <th class="min-w-10px">{{__('admin.city')}}</th>
                    <th class="min-w-10px">{{__('admin.payment_method')}}</th>
                    <th class="min-w-10px">{{__('admin.status')}}</th>
                    <th class="text-end min-w-100px">{{__('admin.actions')}}</th>

                 </tr>
                  </thead>
                 <tbody class="fs-6">
                    @foreach($orders as $order)
                    @php
                        $user = $order->user;
                        $driver = $order->driver;
                        $address= $order->address;
                        $customer = $order->user;
                        $customerCityName = null;
                        if($orderCity = $order->city){

                         $customerCityName = $orderCity->name;

                          }else{
                             if($customer){
                                 $customerCity = $customer->city;
                                    if($customerCity){
                                       $customerCityName = $customerCity->name;
                                   }
                                }
                             }

                           @endphp
                           <tr>
                              <td>
                                 @if(!is_null(request()->get('status')))
                                   <input class="child-checkbox-row" type="checkbox" name="selected_orders[]" value="{{$order->id}}">
                                 @endif
                                     &nbsp; {{$order->order_number}}
                                 </td>

                                <td>{{$order->created_at->format('Y-m-d')}}
                                    <div class="fw-bold fs-6 text-gray-400"><i class="fa fa-clock"></i> {{$order->created_at->format('h:i A')}}</div>
                                </td>
                                <td>{{$order->delivery_date }}
                                <div class="fw-bold fs-6 text-gray-400" style="font-size: 12px !important; font-weight: bold !important;"><i class="fa fa-clock"></i> {{implode(' - ', [date('H:i',strtotime($order->delivery_time)) ])}}</div>
                                </td>
                                <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{$user?$user->name:'--'}}">{{$user?\Str::limit($user->name, 15):'--'}}
                                    <div class="fw-bold fs-6 text-gray-400"> {{$address?$address->mobile:'---'}}</div>
                                </td>
                                <td>{{$order->total}}</td>
                                <td>{{ !$customerCityName?:$order->city->name }} </td>
                                <td>{{$order->payment_method}}</td>
                                <td><a href="javascript:void(0);" class="change-order-status-btn" @if(in_array($order->status, [\App\Models\Order::STATUS_CANCELED]) ) data-bs-toggle="tooltip" data-bs-placement="top" title="{{$order->cancelled_reason?:'لم يتم إضافة سبب للإلغاء'}}" @endif data-url="{{route('orders.statuses-list', ['id' => $order->id])}}">{!! orderStatusLabel($order->status) !!}</a></td>
                                 <td class="text-end">
                                    @php
                                     $actions = [

                                           ['label' => __('admin.show'), 'url' => route('orders.show', $order->id)],
//                                           ['label' => __('admin.change_status'), 'url' => route('orders.statuses-list', $order->id), 'ajax' => true, 'class' => 'change-order-status-btn'],

                                          ];
                                     @endphp
                                     @include('dashboard.components.table_actions', ['actions', $actions])
                                 </td>
                             </tr>
                            @endforeach
                       </tbody>
                  </table>
              </div>
                  {!! $orders->links('dashboard.partials.paginator', ['disableJS' => true]) !!}
         </div>
     </div>

