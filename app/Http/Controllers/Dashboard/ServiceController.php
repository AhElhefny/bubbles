<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\WorkTime;
use Illuminate\Support\Facades\Validator;
use Auth ;

class ServiceController extends Controller
{
    
    /*public function __construct()
     {
         $this->middleware('permission:read_service', ['only' => ['index','show']]);
         $this->middleware('permission:add_service', ['only' => ['create', 'store']]);
         $this->middleware('permission:update_service', ['only' => ['edit', 'update']]);
         $this->middleware('permission:delete_service', ['only' => ['destroy']]);
     }*/

    public function index()
    {
        
        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
        ];

        $data['page_title'] = trans('admin.services');
        $data['services'] = Service::where('type','=','service')->where('seller_id',Auth::user()->seller->id)->orderBy('created_at','desc')->get();

        return view('dashboard.services.index', $data);
    }

    public function create()
    { 
         
         $data['breadcrumb'] = [

             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
             ['name' => trans('admin.services'), 'url' => route('services.index')],
             ['name' => trans('admin.create'), 'url' => null],
         ];

         $data['page_title'] = trans('admin.services');
         $data['data'] = new Service;
         $data['worktimes'] = WorkTime::all();

         return view('dashboard.services.create', $data);
    }

    public function store(Request $request)
    {
      
        $validator = Validator::make($request->all(), (new Service)->rules());

        if ($validator->fails()) {

             return redirect()->back()->withErrors($validator)->withInput();
        }
        
         $data = $request->except('img');
         $data['type']="service";
         $data['seller_id']= Auth::user()->seller->id;
         $data['available'] =$request->available?:0;
        $services = Service::create($data);
        $services->work_times()->sync($request->worktimes);
        if ($request->hasFile('img')) {
            $services->addMedia($request->file('img'))
                ->withCustomProperties(['root' => 'user_prr'.uniqid()])
                ->toMediaCollection($services->mediaImageCollectionName);
          }
       
          return  redirect()->route('services.index')->with('success','تم الانشاء بنجاح');
    }

    public function edit($id)
    {
        
        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.services'), 'url' => route('services.index')],
            ['name' => trans('admin.edit'), 'url' => null]
        ];

        $data['page_title'] = trans('admin.services');
        $data['data'] = Service::findOrFail($id);
        $data['selected_worktimes']=$data['data']->work_times->pluck('id')->toArray();
        $data['worktimes'] = WorkTime::all();
        return view('dashboard.services.edit', $data);
    
    }

    public function update(Request $request, $id)
    {
        $service =Service::find($id);
        $validator = Validator::make($request->all(), $service->rules());

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['seller_id']= Auth::user()->seller->id;
        $update = Service::find($id);
        $update['available'] = $request->available ?:0;

        $update->update($data);

        if ($request->get('img_remove') || $request->hasFile('img')) {

             $update->clearMediaCollection($update->mediaImageCollectionName);
        }

        if ($request->hasFile('img')) {

            $update->addMedia($request->file('img'))
                ->withCustomProperties(['root' => 'user_prr'.uniqid()])
                ->toMediaCollection($update->mediaImageCollectionName);
         }

         return  redirect()->route('services.index')->with('success','تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        
        $delete = Service::destroy($id);

        return redirect()->route('services.index');
    }
}
