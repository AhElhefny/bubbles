<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Module;
use Flashy;

class ModuleController extends Controller
{

    public function __construct(Module $model)
    {
        $this->model = $model;
        $this->view = 'dashboard/modules/';
    }

    public function index()
    {
        $rows = $this->model->orderBy('id','DESC')->get();
        return View($this->view . 'index', compact('rows'));
    }

    public function create()
    {
        return view($this->view.'create');
    }

    public function store(Request $request)
    {

        $data=$request->all();
        $request->validate( [
            'name' => 'required|unique:modules,name',]);
        $insert = $this->model->create(['name'=>$request->name,'title'=>$request->title]);
        Permission::create(['name' => 'add_'.$insert->name,'module_id'=>$insert->id]);
        Permission::create(['name'=>'update_'.$insert->name,'module_id'=>$insert->id]);
        Permission::create(['name'=>'read_'.$insert->name,'module_id'=>$insert->id]);
        Permission::create(['name'=>'delete_'.$insert->name,'module_id'=>$insert->id]);
        if ($insert) {
            // Flashy::message('module Added Successfully');

              return redirect()->to('admin/modules');
        }else {
            // Flashy::error('Error');
             return redirect()->to('admin/modules');

        }
    }

    public function edit($id)
    {
        $row = $this->model->find($id);
        if (!$row) {

            abort(404);
        }
        
        return view($this->view . 'edit',compact('row'));
    }


    public function update(Request $request, $id)
    {
        $row = $this->model->find($id);

        $data = $request->all();

        if (!$row) {
            // Flashy::error('Error');
            return redirect()->back();
        }

        $row->update(['name'=>$data['name'],'title'=>$data['title']]);

        // Flashy::message('module Updated Successfully');
        return redirect()->route('modules.index', $row->id);
    }

    public function destroy($id)
    {
        $row = $this->model->find($id);
        if (!$row) {
           // Flashy::error('Error');
            return redirect()->back();
        }
        $row->delete();
        // Flashy::message('Successfully Deleted');
        return redirect()->back();
    }

}
