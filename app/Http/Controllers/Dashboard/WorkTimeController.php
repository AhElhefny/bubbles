<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\WorkTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class  WorkTimeController extends Controller
{
   /* public function __construct()
    {
        $this->middleware('permission:read_worktime', ['only' => ['index','show']]);
        $this->middleware('permission:add_worktime', ['only' => ['create', 'store']]);
        $this->middleware('permission:update_worktime', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_worktime', ['only' => ['destroy']]);
    }*/

    public function index(){
     
        $data['breadcrumb'] = [

             ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
        ];

        $data['page_title'] = trans('admin.worktimes');
        $data['times'] = WorkTime::where('seller_id',Auth::user()->seller->id)->orderBy('created_at','desc')->paginate(20);

        return view('dashboard.worktime.index', $data);
    }

    public function create()
    {
        
        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.worktimes'), 'url' => route('worktimes.index')],
            ['name' => trans('admin.create'), 'url' => null],

        ];
    
        $data['page_title'] = trans('admin.worktimes');
        $data['data'] = new WorkTime ;

         return view('dashboard.worktime.create', $data);
    }

    public function store(Request $request)
    {
       
         $validator = Validator::make($request->all(), (new WorkTime)->rules());

         if($validator->fails()) {

             return redirect()->back()->withErrors($validator)->withInput();
         }

         $data = $request->all();
         $data['seller_id']= Auth::user()->seller->id;
         $worktime= WorkTime::create($data);

         return  redirect()->route('worktimes.index')->with('success','تم لانشاء بنجاج');

    }

     public function edit($id)
     {
         
          $data['breadcrumb'] = [

              ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
              ['name' => trans('admin.worktimes'), 'url' => route('worktimes.index')],
              ['name' => trans('admin.edit'), 'url' => null]

         ];

        $data['page_title'] = trans('admin.worktimes');
        $data['data'] = WorkTime::findOrFail($id);

         return view('dashboard.worktime.edit', $data);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), (new WorkTime)->rules());

        if($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['seller_id']= Auth::user()->seller->id;
        $worktime  = WorkTime::findOrFail($id);
        $worktime->update($data);

        return  redirect()->route('worktimes.index')->with('success','تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $delete = WorkTime::destroy($id);
        
        return  redirect()->route('worktimes.index')->with('success','تم التعديل بنجاح');
    }

  }
