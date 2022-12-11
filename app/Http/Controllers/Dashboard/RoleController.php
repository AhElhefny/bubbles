<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Module;


class RoleController extends Controller
{

    public function __construct(Role $model)
    {
        $this->model = $model;
        $this->view = 'dashboard/roles/';
    }

    public function index()
    {
        $data['breadcrumb'] = [
            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.roles'), 'url' => null],
        ];
        $modules=Module::all();
        $rows = $this->model->orderBy('id','DESC')->get();

        return View($this->view . 'index', compact('rows','modules'));
    }

    public function create()
    {
        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.roles'), 'url' => route('roles.index')],
            ['name' => trans('admin.create'), 'url' => null],
        ];
        $modules = Module::all();

        return view($this->view.'create',compact('modules'));
    }

    public function store(Request $request)
    {
       
        $data=$request->all();
        $request->validate( [
            'name' => 'required|unique:roles,name',
        ]);

        $insert = $this->model->create(['name'=>$request->name,'guard_name'=>'web']);
        $insert->syncPermissions($request->permissions);

        if ($insert) {

           return redirect()->to('admin/roles');

        }else {

            return redirect()->to('admin/roles');
        }
    }

    public function edit($id)
    {
        $data['breadcrumb'] = [

            ['name' => trans('admin.dashboard'), 'url' => route('dashboard')],
            ['name' => trans('admin.roles'), 'url' => route('roles.index')],
            ['name' => trans('admin.update'), 'url' => null],
        ];
        $modules = Module::all();
        $row = $this->model->find($id);
        $permissions=$row->permissions->pluck('id')->toArray();
        if (!$row) {

            abort(404);
        }
        return view($this->view . 'edit',compact('row','modules','permissions'));
    }

    public function update(Request $request, $id)
    {
        
        $row = $this->model->find($id);
        $data = $request->all();

        if (!$row) {

            return redirect()->back();
        }

        $row->update(['name' => $data['name']]);
        // dd($request->permissions);
        $row->syncPermissions($request->permissions);
        
        return redirect()->route('roles.index', $row->id);
    }

    public function destroy($id)
    {
       
        $row = $this->model->find($id);

        if (!$row) {
           Flashy::error('Error');
            return redirect()->back();
        }
        $row->delete();
        return redirect()->back();
    }

}