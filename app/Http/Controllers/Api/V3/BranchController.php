<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\WorkTime;
use Validator;
use DB;


class BranchController extends Controller
{
    
    public function index(Request $request)
    {
       
         if (isset($request->pagination)) {
             
               $pagination=$request->pagination;
          }

          $query = Branch::where('id','>',0)->with(['user','category','city']);
        
         if (isset($request['user_id'])) {

              $query=$query->where('user_id',$request['user_id']);
         }

        if(isset($request['name'])) {
            
              $query=$query->where('name','LIKE', '%' . $request['name'] . '%');
         }

         if(isset($request->city_id) && $request->city_id != null) {

             $query=$query->where('city_id', $request->city_id);
         }

         if(isset($request->category_id) && $request->category_id != null) {

              $query=$query->where('category_id', $request->category_id);
         }

            $latitude = $request->headers->get('lat') ? $request->headers->get('lat') : 0 ;
            $langitude = $request->headers->get('long')?$request->headers->get('long') : 0 ;
          if(!empty($request->headers->get('lat')) && !empty($request->headers->get('long')))
         {
            $query = $query->select('*',DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                * cos(radians(latitude)) * cos(radians(langitude) - radians(" . $langitude . "))
                + sin(radians(" .$latitude. ")) * sin(radians(latitude))) AS distance"))->orderBy('distance','ASC');
            }else{
              
                $query = $query->select('*');
            }

        $query = $query->paginate($request->pagination);

        foreach($query as $row){

            $row['img'] = url($row->img);
            $sliders = [];
            if($row->getMedia('branch_slider')->count() > 0){
                // dd($sliders->count());
                foreach($row->getMedia('branch_slider') as $key=>$slide){
                    $sliders[]  = url($slide->disk.'/'.$slide->id.'/'.$slide->file_name);
                }
            }
            if($row->distance){
                    $row->distance = round($row->distance,2);
                }
            $row['sliders'] = $sliders;
       }
       
        return response()->json([

           'status' => 200,
           'success' => true,
           'message' => trans('messages.list_of_branches'),
           'data' => $query
      ]);
           //return $this->setCode(200)->setSuccess('list of branches')->setData($query)->send();
     }

     public function show($id)
     {

            $branch=Branch::where('id',$id)->with(['user','category','city'])->first();

                $branch['img'] = url($branch->img);
                $sliders = [];
                if($branch->getMedia('branch_slider')->count() > 0){
                    foreach($branch->getMedia('branch_slider') as $key=>$slide){
                        $sliders[]  = url($slide->disk.'/'.$slide->id.'/'.$slide->file_name);
                    }
           }
           $branch['sliders'] = $sliders;
            $branch->workTimes = WorkTime::where('seller_id',$branch->seller_id)->get();

            if($branch) {

              return response()->json([

                  'status' => 200,
                  'success' => true,
                  'message' => trans('messages.Branch_Data'),
                  'data' => $branch
             ]);
          }
 
          return response()->json([

            'status' => 404,
            'success' => false,
            'message' => trans('messages.not_found_data'),
            'data' => null
        ]);         
    }

    public function openBranch(Request $request)
    {
        
        $validator=Validator::make($request->all(),[

            'open'=>'required|boolean',
            'branch_id'=>'required|exists:branches,id'
        ]);

        if ($validator->fails()) {

            return response()->json([

               'status' => 422,
               'success' => false,
               'message' => trans('messages.validation_error'),
               'data' => null

            ]);     
        }
        
        $branch=Branch::find($request->branch_id);
        $branch->is_open=$request->open;
        $branch->save();
        $bookingFire = app('firebase.firestore')
            ->database()
            ->collection('orders')
            ->Document('branch'.$request->branch_id)
            ->collection('branch status')
            ->Document('status');
        $bookingFire->set(['status'=>$request->open]);
        return response()->json([

            'status' => 200,
            'success' => true,
            'message' =>  trans('messages.Branch_Data'),
            'data' => $branch

        ]);
    }
//     public function checkOpenBranch(Request $request)
//     {
//         $validator=Validator::make($request->all(),[
//             'branch_id'=>'required|exists:branches,id'
//         ]);
//         if ($validator->fails()) {
//             return response()->json([
//                'status' => 422,
//                'success' => false,
//                'message' => 'validation error',
//                'data' => null
//             ]);     
//         }
//         $branch=Branch::find($request->branch_id);
//         $branch->is_open=$request->open;
//         $branch->save();
//     }
 }