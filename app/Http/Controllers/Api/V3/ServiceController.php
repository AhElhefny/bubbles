<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\ServiceResource;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Validator;


class ServiceController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'=>'exists:categories,id',
            'branch_id'=>'exists:branches,id'
        ]);

        if($validator->fails()) {

            return $this->sendError(error_processor($validator), trans('messages.validation_error'), 442);
        }
        $pagination=10;
        if(isset($request->pagination)){
            $pagination=$request->pagination;
        }
        $services=Service::where('id','>',0);
        if (isset($request->category_id)) {
            $services=$services->where('category_id',$request->category_id);
        }
        if (isset($request->branch_id)) {
            $branch=Branch::find($request->branch_id);
            $services=$services->where('seller_id',$branch->seller_id);
        }

        $services=$services->paginate($pagination);
        foreach($services as $service){
            $service['img'] = url($service->img);
        }

        return response()->json([

            'status' => 200,
            'success' => true,
            'message' => trans('messages.get_data_success'),
            "services" => $services
        ]);
    }

    public function show($id)
    {
        $service=Service::where('id',$id)->first();
        if (!$service) {
            return $this->sendError([], trans('messages.not_found_data'), 404);
        }
        $service['img'] = url($service->img);
        return response()->json([

            'status' => 200,
            'success' => true,
            'message' => trans('messages.get_data_success'),
            "service" => new ServiceResource($service)
        ]);
    }

    public function search(Request $request){

        $latitude = $request->headers->get('lat') ? $request->headers->get('lat') : 0 ;
        $langitude = $request->headers->get('long')?$request->headers->get('long') : 0 ;
        $services = DB::table('branches')
        ->join('services','branches.seller_id','=','services.seller_id')
        ->select('services.*','branches.id AS b_id', 'branches.name', 'branches.address',
        'branches.latitude', 'branches.langitude', 'branches.range_price', 'branches.is_open',
        DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                * cos(radians(branches.latitude)) * cos(radians(branches.langitude) - radians(" . $langitude . "))
                + sin(radians(" . $latitude . ")) * sin(radians(branches.latitude))) AS distance"))
                ->where('available','1')
            ->where('title', 'like', '%' . $request->filter . '%')
            ->orWhere('description','like','%'. $request->filter . '%')
            ->orWhere('name', 'like', '%' . $request->filter . '%')
            ->orWhere('address', 'like', '%' . $request->filter . '%')
            ->orWhere('type','like','%'. $request->filter . '%')
            ->orWhere('price','like','%'. $request->filter . '%')
            ->orderBy('distance', 'ASC')->get();

        return response() ->json([
            'message' => count($services) ?trans('messages.get_data_success'):trans('messages.not_found_data'),
            'success' => true,
            'data' => ServiceResource::collection($services)
        ],200);
    }
}
