<?php

namespace App\Http\Controllers\Voyager;

use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataRestored;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use Redirect;
use App\ProjectDivision;
use App\BenchmarkDetail;
use App\Benchmark;
use App\Area;
use App\Project;
use App\Graph;
use Illuminate\Support\Arr;

class VoyagerBenchmarkController extends VoyagerBaseController
{
    use BreadRelationshipParser;
    var $aread = array();
    private $count = 0;
    var $global_areas = array();
    var $global_benchmark_details = array();
    var $global_project_divisions = array();

    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $displayName = $dataRow->where('field', $value)->first()->getTranslatedAttribute('display_name');
                $searchNames[$value] = $displayName ?: ucwords(str_replace('_', ' ', $value));
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {
                $query = $model::select('*');
            }
            
            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');
            $query->where('project_id', \Session::get('project')->id); 

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn'
        ));
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |__) |
    //               |  _  /
    //               | | \ \
    //               |_|  \_\
    //
    //  Read an item of our Data Type B(R)EAD
    //
    //****************************************

    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $isSoftDeleted = false;

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
            if ($dataTypeContent->deleted_at) {
                $isSoftDeleted = true;
            }
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'read', $isModelTranslatable);

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted'));
    }

    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $dataTypeDetails = Voyager::model('DataType')->where('slug', '=', 'benchmark-details')->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
            $dataTypeContentDetails = DB::table($dataTypeDetails->name)->where('benchmark_id', $id)->first();
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
            $dataTypeContentDetails = DB::table($dataTypeDetails->name)->where('benchmark_id', $id)->get();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        $projectdivisionsArray = array();
        $graph = new Graph($id,null,null,null,null);
        $benchmarkTotalArr = $graph->calcTotalBenchmarkCost($id,null,null);
        $currency = Project::where('projects.id',$dataTypeContent->project_id)->leftJoin('currencies','currencies.id','=','projects.currency_id')->select('currencies.*')->first();
        $projectDivisions = ProjectDivision::where('project_id',$dataTypeContent->project_id)->whereNull('parent_id')->distinct()->get();
        $this->areas = Area::whereNotNull('parent_id')->pluck('parent_id')->toArray();
        $this->areas = array_unique( $this->areas );
        self::getAreaData($dataTypeContent->id,$dataTypeContent->project_id);
        $menuBuilder=' 
        <input id="currency" value="'.$currency->symbol.'" type="hidden"/>
        <input id="project_id" value="'.$dataTypeContent->project_id.'" type="hidden"/>
        <input id="benchmark_id" value="'.$dataTypeContent->id.'" type="hidden"/>

        <div class="panel-footer" style="padding:0; border:0;">
                                  <table width="100%">
                                      <tbody><tr>
                                          <td width="25%" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div style="font-size:12px; display: block">Total Labor Cost</div>
                                            <h5 style="margin: 0 0 5px 0" class="text-primary"> '.$currency->symbol.' '.number_format($benchmarkTotalArr['labor'],2).'</h5>
                                          </td>
                                          <td width="25%" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div style="font-size:12px; display: block">Total Material Cost</div>
                                            <h5 style="margin: 0 0 5px 0" class="text-primary"> '.$currency->symbol.' '.number_format($benchmarkTotalArr['material'],2).'</h5>
                                          </td>
                                          <td width="25%" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div style="font-size:12px; display: block">Total Labor Hour</div>
                                            <h5 style="margin: 0 0 5px 0" class="text-primary">'.number_format($benchmarkTotalArr['labor_hour'],2).' hours</h5>
                                          </td>
                                          <td width="25%" class="text-right" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div style="font-size:12px; display: block; font-weight: bold;">Total Cost</div>
                                              <h5 class="text-danger" style="margin: 0 0 5px 0" >'.$currency->symbol.' '.number_format($benchmarkTotalArr['total'],2).'</h5>
                                          </td>
                                      </tr>
                                  </tbody></table>
                      </div>
        <ol class="dd-list">';
        foreach ($projectDivisions as $key => $projectDivision) {
             $subCategories = ProjectDivision::where('project_id',$projectDivision->project_id)->where('parent_id',$projectDivision->id)->distinct()->get();
             $projectdivisionsArray[$key]['project'] = $projectDivision;
             $show = ($key == 0)? 'in' : '';
             $menuBuilder.='
             <li class="dd-item" data-id="'.$projectDivision->getKey() .'">
                 <div class="panel panel panel-bordered panel-dark">
                     <div class="panel-heading">
                        <a class="panel-collapsed " data-target="#openIn'.$key.'" data-toggle="collapse" aria-hidden="true">
                         <h3 class="panel-title" style="font-size: 14px; padding: 10px 15px"><i class="icon wb-clipboard"></i> '.$projectDivision->reference.' - '.$projectDivision->title.'</h3>
                         <div class="panel-actions">
                             <i class="panel-action panel-collapsed voyager-angle-down"   aria-hidden="true"></i>
                         </div>
                        </a>

                     </div>
                     <div class="collapse '.$show.'" id="openIn'.$key.'" style="padding-top:0px;padding-right:0px" >';

                  $sub = self::listFunction($dataTypeContent,$currency,$subCategories,$dataTypeContent->project_id);


                $menuBuilder.= $sub['menuBuilder'];
                $projectdivisionsArray[$key]['child'] = $sub['array'];
               $menuBuilder.='</div></div></li>';
            }

          $menuBuilder.='</ol>';
        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }
        

        return Voyager::view($view, compact('dataType','menuBuilder','projectdivisionsArray', 'dataTypeContent', 'isModelTranslatable'));
    }
    function updatedetails(Request $request,$id)
    {
       
       
        
        foreach($request->project_division_ids as $keyDiv=>$project_division_id){
            if(isset($request->benchmarkdetails_ids[$project_division_id]))
            {
                 foreach($request->benchmarkdetails_ids[$project_division_id] as $keyBench=>$benchmarkdetails_id){
                    
                     if($benchmarkdetails_id == 0)
                     {
                       // return $request->unit_material_rate[$project_division_id];
                       if(isset($request->quantity[$project_division_id][$keyBench]))
                       {
                         BenchmarkDetail::insert(['benchmark_id'=>$id,'project_division_id'=>$request->project_division_ids[$keyDiv],
                         'quantity'=>$request->quantity[$project_division_id][$keyBench],'unit_labor_hour'=>$request->unit_labor_hour[$project_division_id],
                         'hours_unit'=>$request->hours_unit[$project_division_id],'area_id'=>$request->area_ids[$project_division_id][$keyBench],
                         'unit_material_rate'=>$request->unit_material_rate[$project_division_id],'created_at'=>date('Y-m-d H:i:s')]);
                       }
                     }
                     else{
                       BenchmarkDetail::where('id',$benchmarkdetails_id)->update(['benchmark_id'=>$id,'project_division_id'=>$request->project_division_ids[$keyDiv],
                       'quantity'=>$request->quantity[$project_division_id][$keyBench],'unit_labor_hour'=>$request->unit_labor_hour[$project_division_id],
                       'hours_unit'=>$request->hours_unit[$project_division_id],'area_id'=>$request->area_ids[$project_division_id][$keyBench],
                       'unit_material_rate'=>$request->unit_material_rate[$project_division_id]]);
                     }
                   }
                
            }
         
        }
      //  return $request;
        return  Redirect::back();
    }

    function listFunction($dataTypeContent,$currency,$list,$project_id)
    {
        $menuBuilder = '';
        $projectArray = array();
        if(count($list) > 0)
        {
          $menuBuilder = '<ol class="dd-list">';
        }
            foreach ($list as $key => $item) {
                $subCategories = ProjectDivision::where('project_id',$project_id)->where('parent_id',$item['id'])->distinct()->get();
                // $subCategories = Arr::where($this->global_project_divisions, function ($value, $key) use($item) {
                //     return $value['parent_id'] == $item['id'];
                // });

                $isLast = false;
                foreach ($subCategories as $key => $subCategory) {
                    $child = Arr::where($this->global_project_divisions, function ($value, $key) use($subCategory) {
                        return $value['parent_id'] == $subCategory['id'];
                    });
                    if(count($child) == 0)
                    {
                        $isLast = true;
                    }
                }

                $projectArray[$key]['project'] = $item;
                $sub = self::listFunction($dataTypeContent,$currency,$subCategories,$project_id);


                $show = ($key == 0 && count($subCategories) <> 0)? 'in' : '';
                $theme = (count($subCategories) == 0) ?'panel-primary':'panel-dark';
                if(count($subCategories) != 0){
                        $menuBuilder.='
                        <li class="dd-item" data-id="'.$item->getKey() .'"';
                        if($isLast){$menuBuilder.=' parent="'.$item['id'].'"';}
                        $menuBuilder.='><div class="panel panel panel-bordered '.$theme.'">
                            <div class="panel-heading">
                                <a class="panel-collapsed " data-target="#open'.$item->getKey().'" data-toggle="collapse" aria-hidden="true">
                                 <h3 class="panel-title" style="font-size: 14px; padding: 10px 15px"><i class="icon wb-clipboard"></i> '.$item->reference.' - '.$item->title.'</h3>
                                 <div class="panel-actions">
                                     <div class="loader" id="loader'.$item['id'].'"></div>
                                     <i class="panel-action panel-collapsed voyager-angle-down"  aria-hidden="true"></i>
                                 </div>
                                </a>
  
                            </div>
                            <div class="collapse '.$show.'" id="open'.$item->getKey().'" style="padding-top:0px;padding-right:0px">';
                                
                               
                           
                }

                $menuBuilder.= $sub['menuBuilder'];
                $projectdivisionsArray[$key]['child'] = $sub['array'];
                if(count($subCategories) != 0){
                  $menuBuilder.='</div></div></li>';
                }

           


        }

        if(count($list) > 0)
        {
          $menuBuilder .= '</ol>';
        }


        $respone = array();
        $respone['array'] = $projectArray;
        $respone['menuBuilder'] = $menuBuilder;
        return $respone;

    }

    function getChildData(Request $request)
    {
        $menuBuilder = '';
        $projectArray = array();
        $currency = $request->currency;
        $project_id = $request->project_id;
        $this->areas = Area::whereNotNull('parent_id')->pluck('parent_id')->toArray();
        $this->areas = array_unique( $this->areas );

        self::getAreaData($request->benchmark_id,$request->project_id);

        $list = ProjectDivision::where('project_id',$project_id)->where('parent_id',$request->id)->get();

        if(count($list) > 0)
        {
          $menuBuilder = '<ol class="dd-list">';
        }
            foreach ($list as $key => $item) {
                $projectArray[$key]['project'] = $item;

                $menuBuilder.='
                <li class="dd-item" data-id="'.$item->getKey() .'">
                    <div class="panel panel panel-bordered panel-primary">
                        <div class="panel-heading">
                            <a class="panel-collapsed " data-target="#open'.$item->getKey().'" data-toggle="collapse" aria-hidden="true">
                             <h3 class="panel-title" style="font-size: 14px; padding: 10px 15px"><i class="icon wb-clipboard"></i> '.$item->reference.' - '.$item->title.'</h3>
                             <div class="panel-actions">
                                 
                                 <i class="panel-action panel-collapsed voyager-angle-down"  aria-hidden="true"></i>
                             </div>
                            </a>

                        </div>
                        <div class="collapse" id="open'.$item->getKey().'" style="padding-top:0px;padding-right:0px">

                    ';


                  $benchmarks = BenchmarkDetail::where('benchmark_details.benchmark_id',$request->benchmark_id)->leftJoin('areas','areas.id','=','benchmark_details.area_id')
                  ->whereNotNull('areas.id')->whereNotIn('areas.id',$this->areas)->leftJoin('project_divisions','project_divisions.id','=','benchmark_details.project_division_id')->whereNotNull('project_divisions.id')->where('benchmark_details.project_division_id',$item['id'])
                  ->select('benchmark_details.*');
                  $benchmarksArr = $benchmarks->pluck('quantity')->toArray();
                  $benchmarks = $benchmarks->get();
                 

                  $hours_unit         = 0;
                  $unit_labor_rate    = 0;
                  $unit_labor_hour    = 0;
                  $unit_material_rate = 0;
                  $totalQuantity      = 0 ;
                  $totallabelCost     = 0 ;
                  $totalmaterialCost  = 0 ;
                  $totalunitRate      = 0 ;
                  $totalmaterialHour  = 0 ;
                  $totalCost          = 0 ;
                  $totallabelHour = 0;
                  $unit_rate = 0;
                   
                  if(isset($benchmarks[0]))
                  {
                    $hours_unit = $benchmarks[0]->hours_unit;
                    $unit_labor_hour = $benchmarks[0]->unit_labor_hour;
                    $unit_material_rate = $benchmarks[0]->unit_material_rate;
                    $unit_labor_rate = ($hours_unit == 0)?0:$unit_labor_hour * $hours_unit;
                    $unit_rate = $unit_labor_rate + $unit_material_rate; 
                     $totalQuantity = array_sum($benchmarksArr);
                     //       $totalQuantity         += intval($benchmarks[0]->quantity);
                    $totallabelHour = $totalQuantity * $hours_unit;
                    $totallabelCost = $totalQuantity * $unit_labor_rate;
                    $totalmaterialCost = $totalQuantity * $unit_material_rate;
                    $totalunitRate = $unit_labor_rate + $unit_material_rate;
                    $totalCost = $totallabelCost + $totalmaterialCost;

                  }




                  $menuBuilder.='
                  <div class="form-group col-md-12"></div>
                  <div class="form-group col-md-3">
                      <label for="unit">Unit</label>
                      <h5>'.$item->unit_of_measure.'</h5>
                  </div>
                  <div class="form-group col-md-2">
                      <label for="hours_unit">Hours / Unit</label>
                      <input type="number" step="any" class="form-control" name="hours_unit['.$item->id.']"  id="hours_unit'.$item->getKey().'" onkeyup="changeUnitHours'.$item->getKey().'(this)" placeholder="Hours / Unit"  value="'.$hours_unit.'" >
                  </div>
                  <div class="form-group col-md-2">
                      <label for="unit_material_rate">Material / Unit Rate</label>
                      <input type="number" step="any" class="form-control" name="unit_material_rate['.$item->id.']" id="unit_material_rate'.$item->getKey().'" onkeyup="changeUnitMaterial'.$item->getKey().'(this)" placeholder="Material / Unit Rate" value="'.$unit_material_rate.'" >
                  </div>
                  <div class="form-group col-md-2">
                      <label for="unit_labor_hour">Labor / Hour Rate</label>
                      <input type="number" step="any" class="form-control" name="unit_labor_hour['.$item->id.']" id="unit_labor_hour'.$item->getKey().'" onkeyup="changeHourLabor'.$item->getKey().'(this)" placeholder="Labor / Unit Hour" value="'.$unit_labor_hour.'">
                  </div>
                  <div class="form-group col-md-2">
                      <label for="unit_labor_rate">Labor / Unit Rate</label>
                      <h5 id="unit_labor_rate'.$item->getKey().'">'.$unit_labor_rate.'</h5>
                  </div>
                  <div class="form-group col-md-2">
                      <label for="unit_rate">Unit Rate</label>
                      <h5 id="unit_rate'.$item->getKey().'">'.$unit_rate.'</h5>
                  </div>

                  <input type="hidden" class="form-control" name="project_division_ids[]"  value="'.$item->id.'">


                          <div class="form-group col-md-12">
                            <table width="100%" class="table table-striped table-hover tree-'.$request['id'].'">
                              <thead  class="bg-primary">
                                   <th>Area of Work</th>
                                   <th>Quantity</th>
                                   <th>Total Labor Hour</th>
                                   <th>Total Labor Cost</th>
                                   <th>Total Material Cost</th>
                                   <th>Total Cost</th>
                               </thead>
                               <tbody>
                           ';
                           
                           
                           $areas = self::getArea($request->benchmark_id,$item['id'],$project_id);
                            $indexArea = 0;
                            

                            foreach ($areas as $area) {

                              $quantity = '';
                              $benchmarkdetails_id = 0;
                              $menuBuilder.='  <tr class="'.$area['class'].'">
                                                   <td>'.$area['name'].'</td>';
                             if(isset($area['details']))
                             {
                               $quantity = $area['details']['quantity'];
                               $benchmarkdetails_id = $area['details']['id'];
                             }

                                     if($area['isLast'])
                                     {
                                        $menuBuilder.='     <td><input type="text" class="form-control" id="quantity'.$item->getKey().''.$indexArea.'" onkeyup="changeQuanity'.$item->getKey().'(this,'.$indexArea.')" name="quantity['.$item->id.'][]" placeholder="Quantity" value="'.$quantity.'"></td>
                                                            <td><h5 id="totalLaborHour'.$item->getKey().''.$indexArea.'">'  .number_format(($hours_unit * intval($quantity))).' Hours</h5></td>
                                                            <td><h5 id="totalLaborCost'.$item->getKey().''.$indexArea.'">'.$currency.' '.number_format(( intval($quantity) * $unit_labor_rate),2).'</h5></td>
                                                            <td><h5 id="totalMaterialCost'.$item->getKey().''.$indexArea.'">'.$currency.' '.number_format(($unit_material_rate * intval($quantity)),2).'</h5></td>
                                                            <td><h5 id="totalCost'.$item->getKey().''.$indexArea.'">'.$currency.' '.number_format(($unit_material_rate * intval($quantity))+( intval($quantity) * $unit_labor_rate),2).'</h5></td>
                                                            <input type="hidden"class="form-control" name="benchmarkdetails_ids['.$item->id.'][]"  value="'.$benchmarkdetails_id.'">
                                                            <input type="hidden" class="form-control" name="area_ids['.$item->id.'][]"  value="'.$area['id'].'">
                                                        ';
                                             $indexArea++;
                                     }
                                     else{
                                       $menuBuilder.='   <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                          ';
                                     }



                                        $menuBuilder.='</tr>';
                                 }

                        $this->count++;


                          $menuBuilder.='</tbody>
                                </table>
                            </div>
                      </div>
                      <div class="panel-footer" style="padding:0; border:0;">
                                  <table width="100%">
                                      <tr>
                                          <td width="16%" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div  style="font-size:12px; display: block;">Total Quantity</div>
                                            <h5 style="margin: 0 0 5px 0" id="totalqt'.$item->getKey().'" class="text-primary">'.$totalQuantity.' '.$item['unit_of_measure'].'</h5>
                                          </td>
                                          <td width="16%" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div  style="font-size:12px; display: block">Total Labor Hours</div>
                                            <h5 style="margin: 0 0 5px 0" id="totallabelHour'.$item->getKey().'" class="text-primary">'.$totallabelHour.' Hours</h5>
                                          </td>
                                          <td width="16%" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div  style="font-size:12px; display: block">Total Labor Cost</div>
                                            <h5 style="margin: 0 0 5px 0" id="totallabelCost'.$item->getKey().'" class="text-primary">'.$currency.' '.number_format($totallabelCost,2).'</h5>
                                          </td>
                                          <td width="16%" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div  style="font-size:12px; display: block">Total Material Cost</div>
                                            <h5 style="margin: 0 0 5px 0" id="totalmaterialCost'.$item->getKey().'" class="text-primary">'.$currency.' '.number_format($totalmaterialCost,2).'</h5>
                                          </td>
                                          <td width="16%" style="border:2px solid #62a8ea; padding: 5px;">
                                              <div  style="font-size:12px; display: block">Total Unit Rate</div>
                                              <h5 style="margin: 0 0 5px 0" id="totalunitRate'.$item->getKey().'" class="text-primary">'.$currency.' '.number_format($totalunitRate,2).'</h5>
                                          </td>
                                          <td width="20%" class="text-right" style="border:2px solid #62a8ea; padding: 5px;">
                                            <div  style="font-size:12px; display: block; font-weight: bold;">Total Cost</div>
                                              <h5 class="text-danger" style="margin: 0 0 5px 0" id="totalCost'.$item->getKey().'">'.$currency.' '.number_format($totalCost,2).'</h5>
                                          </td>
                                      </tr>
                                  </table>
                      </div>
                  </div>
                  <script>
                        var hours_unit'.$item->getKey().' = document.getElementById("hours_unit'.$item->getKey().'").value;
                        var unit_labor_rate'.$item->getKey().' = document.getElementById("unit_labor_rate'.$item->getKey().'").value;
                        var unit_rate'.$item->getKey().' = document.getElementById("unit_rate'.$item->getKey().'").value;
                        var unit_material_rate'.$item->getKey().' = document.getElementById("unit_material_rate'.$item->getKey().'").value;
                        var unit_labor_hour'.$item->getKey().' = document.getElementById("unit_labor_hour'.$item->getKey().'").value;

                        var quantityArray'.$item->getKey().' = new Array();
                        var totalLaborHourArray'.$item->getKey().' = new Array();
                        var totalLaborCostArray'.$item->getKey().' = new Array();
                        var totalMaterialCostArray'.$item->getKey().' = new Array();
                        var totalCostArray'.$item->getKey().' = new Array();
                    ';
                    $indexArea = 0;
                    foreach ($areas as $area) {
                      if($area['isLast'])
                      {
                        $menuBuilder.='quantityArray'.$item->getKey().'['.$indexArea.'] = document.getElementById("quantity'.$item->getKey().''.$indexArea.'").value;
                        totalLaborHourArray'.$item->getKey().'['.$indexArea.'] = document.getElementById("totalLaborHour'.$item->getKey().''.$indexArea.'").value;
                        totalLaborCostArray'.$item->getKey().'['.$indexArea.'] = document.getElementById("totalLaborCost'.$item->getKey().''.$indexArea.'").value;
                        totalMaterialCostArray'.$item->getKey().'['.$indexArea.'] = document.getElementById("totalMaterialCost'.$item->getKey().''.$indexArea.'").value;
                        totalCostArray'.$item->getKey().'['.$indexArea.'] = document.getElementById("totalCost'.$item->getKey().''.$indexArea.'").value;
                        ';
                          $indexArea++;
                      }
                    }

                   $menuBuilder.='

                   var totalqt = '.$totalQuantity.';
                   var totallabelHour = '.$totallabelHour.';
                   var totallabelCost = '.$totallabelCost.';
                   var totalmaterialCost = '.$totalmaterialCost.';
                   var totalunitRate = '.$totalunitRate.'
                   var totalCost= '.$totalCost.';
                   var totallaborHour = '.$unit_labor_hour.';

                   function formatNumber(num) {
                      return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
                    }

                    function changeUnitHours'.$item->getKey().'(event)
                    {
                       hours_unit'.$item->getKey().' = event.value;
                       var input = document.getElementById("hours_unit'.$item->getKey().'");
                       if(!input.value.includes("."))
                        input.value = Number(input.value).toString();
                   
                       calculation'.$item->getKey().'();

                    }
                    function changeHourLabor'.$item->getKey().'(event)
                    {
                      unit_labor_hour'.$item->getKey().' = event.value;
                       var input = document.getElementById("unit_labor_hour'.$item->getKey().'");
                       if(!input.value.includes("."))
                        input.value = Number(input.value).toString();
                       
                       calculation'.$item->getKey().'();
                    }
                    function changeUnitMaterial'.$item->getKey().'(event)
                    {
                      unit_material_rate'.$item->getKey().' = event.value;
                      var input = document.getElementById("unit_material_rate'.$item->getKey().'");
                      if(!input.value.includes("."))
                       input.value = Number(input.value).toString();
                      calculation'.$item->getKey().'();
                    }

                    function changeQuanity'.$item->getKey().'(event,key)
                    {
                     
                      quantityArray'.$item->getKey().'[key] = event.value;
                      
                      
                    //   var input = document.getElementById("quantityArray'.$item->getKey().'[key]");
                       
                    //   console.log(input);
                    //   input.value = Number(input.value).toString();
                      
                      calculation'.$item->getKey().'();
                    }

                    function calculation'.$item->getKey().'()
                    {

                      unit_labor_rate'.$item->getKey().' = parseFloat(unit_labor_hour'.$item->getKey().') * parseFloat(hours_unit'.$item->getKey().');
                      document.getElementById("unit_labor_rate'.$item->getKey().'").innerHTML = unit_labor_rate'.$item->getKey().';
                      
                      unit_rate'.$item->getKey().' = parseFloat(unit_labor_rate'.$item->getKey().') + parseFloat(unit_material_rate'.$item->getKey().');
                      document.getElementById("unit_rate'.$item->getKey().'").innerHTML = unit_rate'.$item->getKey().';

                      var total = 0;
                      for (var i = 0; i < quantityArray'.$item->getKey().'.length; i++)
                      {
                        if(quantityArray'.$item->getKey().'[i] > 0)
                        {
                          var totalLaborHourKey = quantityArray'.$item->getKey().'[i] * parseFloat(hours_unit'.$item->getKey().');
                          var totallaborCostKey = quantityArray'.$item->getKey().'[i] * parseFloat(unit_labor_rate'.$item->getKey().');
                          var totalmaterialCostKey = quantityArray'.$item->getKey().'[i] * parseFloat(unit_material_rate'.$item->getKey().');
                          var totalCostKey = totallaborCostKey + totalmaterialCostKey;

                          document.getElementById("totalLaborHour'.$item->getKey().'"+i).innerHTML = totalLaborHourKey+" Hours";
                          document.getElementById("totalLaborCost'.$item->getKey().'"+i).innerHTML = "'.$currency.' "+totallaborCostKey;
                          document.getElementById("totalMaterialCost'.$item->getKey().'"+i).innerHTML = "'.$currency.' "+totalmaterialCostKey;
                          document.getElementById("totalCost'.$item->getKey().'"+i).innerHTML = "'.$currency.' "+totalCostKey;

                          total += parseFloat(quantityArray'.$item->getKey().'[i]);
                        }

                      }

                       totalqt = total;


                       document.getElementById("totalqt'.$item->getKey().'").innerHTML = totalqt + " '.$item->unit_of_measure.'";

                       totallabelHour = (totalqt * parseFloat(hours_unit'.$item->getKey().'));
                       document.getElementById("totallabelHour'.$item->getKey().'").innerHTML = totallabelHour+" Hours";


                      // totallabelCost = totallabelHour * parseFloat(unit_labor_rate'.$item->getKey().');
                       totallabelCost = totalqt * parseFloat(unit_labor_rate'.$item->getKey().');
                       document.getElementById("totallabelCost'.$item->getKey().'").innerHTML = "'.$currency.' "+formatNumber(totallabelCost);

                       totalmaterialCost = totalqt * parseFloat(unit_material_rate'.$item->getKey().');
                       document.getElementById("totalmaterialCost'.$item->getKey().'").innerHTML = "'.$currency.' "+formatNumber(totalmaterialCost);

                       totalunitRate = parseFloat(unit_labor_rate'.$item->getKey().') + parseFloat(unit_material_rate'.$item->getKey().');
                       document.getElementById("totalunitRate'.$item->getKey().'").innerHTML = "'.$currency.' "+formatNumber(totalunitRate);

                       totalCost = totallabelCost + totalmaterialCost;
                       document.getElementById("totalCost'.$item->getKey().'").innerHTML = "'.$currency.' "+formatNumber(totalCost);


                    }
                    </script>
                      ';
                






        }

        if(count($list) > 0)
        {
          $menuBuilder .= '</ol>';
        }


        return $menuBuilder;

    }

    function getAreaData($benchmark_id,$project_id)
    {
        $this->global_areas = Area::where('project_id',$project_id)->get()->toArray();
        $this->global_benchmark_details = BenchmarkDetail::where('benchmark_id',$benchmark_id)->get()->toArray();
        $this->global_project_divisions = ProjectDivision::where('project_id',$project_id)->get()->toArray();

    }

    function getArea($benchmark_id,$project_division_id,$project_id)
    {
      $areasArray = array();
     // $areas = Area::where('project_id',$project_id)->whereNull('parent_id')->get();
     $areas = Arr::where($this->global_areas, function ($value, $key) {
        return $value['parent_id'] == null;
    });
       
      foreach ($areas as $key => $area) {
         // $areaList = Area::where('project_id',$area->project_id)->where('parent_id',$area->id)->get();
         $areaList = Arr::where($this->global_areas, function ($value, $key) use($area) {
            return $value['parent_id'] == $area['id'];
        });
          $benchmarkArray = array();
          $benchmarkArray['name'] = $area['name'];
          $benchmarkArray['id'] = $area['id'];
          $benchmarkArray['isLast'] = false;
          $benchmarkArray['class'] = 'treegrid-alfa'.$area['id'];

           if(count($areaList) == 0)
          {
             $benchmark = BenchmarkDetail::where('benchmark_id',$benchmark_id)->where('project_division_id',$project_division_id)->where('area_id',$area->id)->first();
            // $benchmark = Arr::where($this->global_benchmark_details, function ($value, $key) use($project_division_id,$area) {
            //         return ($value['project_division_id'] == $project_division_id && $value['area_id'] == $area['id']);
            //     });
             
                    $benchmarkArray['details'] = $benchmark;
                
            
             $areasArray[] = $benchmarkArray;
           }
           else{
             
             $areasArray[] = $benchmarkArray;
             $areasArray = self::areaList($benchmark_id,$areasArray,$project_division_id,$areaList,$area['name']);
           }
        // code...
      }

      return $areasArray;
    }
    function areaList($benchmark_id,$areasArray,$project_division_id,$list,$name)
    {
      $areasName = '';
    //  $areasArray = array();
      foreach ($list as $key => $item) {
         // $areaList = Area::where('project_id',$item->project_id)->where('parent_id',$item['id'])->get();'
         $areaList = Arr::where($this->global_areas, function ($value, $key) use($item) {
            return $value['parent_id'] == $item['id'];
        });
          $areaName = $name.' &raquo; '.$item['name'];
        $benchmarkArray = array();
        $benchmarkArray['name'] = $item['name'];
        $benchmarkArray['id'] = $item['id'];
        $benchmarkArray['class'] = 'treegrid-alfa'.$item['id'].' treegrid-parent-alfa'.$item['parent_id'];

          if(count($areaList) == 0)
          {

             $benchmark = BenchmarkDetail::where('benchmark_id',$benchmark_id)->where('project_division_id',$project_division_id)->where('area_id',$item['id'])->first();
            //    $benchmark = Arr::where($this->global_benchmark_details, function ($value, $key) use($project_division_id,$item) {
            //         return ($value['project_division_id'] == $project_division_id && $value['area_id'] == $item['id']);
            //     });
                // if(isset($benchmark[0]))
                // {
                    $benchmarkArray['details'] = $benchmark;
                //}
                
               
               $benchmarkArray['isLast'] = true;
               $areasArray[] = $benchmarkArray;
           }
           else{
               $benchmarkArray['isLast'] = false;
               $areasArray[] = $benchmarkArray;
               $areasArray = self::areaList($benchmark_id,$areasArray,$project_division_id,$areaList,$areaName);
           }


        }


        return $areasArray;
    }
    public function duplicate($id)
    {
      Benchmark::where('id',$id)->update(['locked'=>1]);
      $old_benchmark = Benchmark::find($id);
      $new_benchmarkId = Benchmark::insertGetId(['name'=>$old_benchmark->name,'project_id'=>$old_benchmark->project_id
      ,'revision'=>($old_benchmark->revision + 1),'start_date'=>$old_benchmark->start_date,'created_at'=>date('Y-m-d H:i:s')]);

      $old_benchmarkDetails = BenchmarkDetail::where('benchmark_id',$id)->get();
      foreach ($old_benchmarkDetails as $key => $old_benchmarkDetail) {
        $new_benchmarkDetail = $old_benchmarkDetail->replicate();
        $new_benchmarkDetail->benchmark_id = $new_benchmarkId;
        $new_benchmarkDetail->save();
        // code...
      }

      return  Redirect::back();
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        }

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', $model)) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
                            ? new $dataType->model_name()
                            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'add', $isModelTranslatable);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

            // Check permission
            $this->authorize('delete', $data);

            $model = app($dataType->model_name);
            if (!($model && in_array(SoftDeletes::class, class_uses_recursive($model)))) {
                $this->cleanup($dataType, $data);
            }
        }

        $displayName = count($ids) > 1 ? $dataType->getTranslatedAttribute('display_name_plural') : $dataType->getTranslatedAttribute('display_name_singular');

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    public function restore(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('delete', app($dataType->model_name));

        // Get record
        $model = call_user_func([$dataType->model_name, 'withTrashed']);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        $data = $model->findOrFail($id);

        $displayName = $dataType->getTranslatedAttribute('display_name_singular');

        $res = $data->restore($id);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_restored')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_restoring')." {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataRestored($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    //***************************************
    //
    //  Delete uploaded file
    //
    //****************************************

    public function remove_media(Request $request)
    {
        try {
            // GET THE SLUG, ex. 'posts', 'pages', etc.
            $slug = $request->get('slug');

            // GET file name
            $filename = $request->get('filename');

            // GET record id
            $id = $request->get('id');

            // GET field name
            $field = $request->get('field');

            // GET multi value
            $multi = $request->get('multi');

            $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

            // Load model and find record
            $model = app($dataType->model_name);
            $data = $model::find([$id])->first();

            // Check if field exists
            if (!isset($data->{$field})) {
                throw new Exception(__('voyager::generic.field_does_not_exist'), 400);
            }

            // Check permission
            $this->authorize('edit', $data);

            if (@json_decode($multi)) {
                // Check if valid json
                if (is_null(@json_decode($data->{$field}))) {
                    throw new Exception(__('voyager::json.invalid'), 500);
                }

                // Decode field value
                $fieldData = @json_decode($data->{$field}, true);
                $key = null;

                // Check if we're dealing with a nested array for the case of multiple files
                if (is_array($fieldData[0])) {
                    foreach ($fieldData as $index=>$file) {
                        // file type has a different structure than images
                        if (!empty($file['original_name'])) {
                            if ($file['original_name'] == $filename) {
                                $key = $index;
                                break;
                            }
                        } else {
                            $file = array_flip($file);
                            if (array_key_exists($filename, $file)) {
                                $key = $index;
                                break;
                            }
                        }
                    }
                } else {
                    $key = array_search($filename, $fieldData);
                }

                // Check if file was found in array
                if (is_null($key) || $key === false) {
                    throw new Exception(__('voyager::media.file_does_not_exist'), 400);
                }

                $fileToRemove = $fieldData[$key]['download_link'] ?? $fieldData[$key];

                // Remove file from array
                unset($fieldData[$key]);

                // Generate json and update field
                $data->{$field} = empty($fieldData) ? null : json_encode(array_values($fieldData));
            } else {
                if ($filename == $data->{$field}) {
                    $fileToRemove = $data->{$field};

                    $data->{$field} = null;
                } else {
                    throw new Exception(__('voyager::media.file_does_not_exist'), 400);
                }
            }

            $row = $dataType->rows->where('field', $field)->first();

            // Remove file from filesystem
            if (in_array($row->type, ['image', 'multiple_images'])) {
                $this->deleteBreadImages($data, [$row], $fileToRemove);
            } else {
                $this->deleteFileIfExists($fileToRemove);
            }

            $data->save();

            return response()->json([
                'data' => [
                    'status'  => 200,
                    'message' => __('voyager::media.file_removed'),
                ],
            ]);
        } catch (Exception $e) {
            $code = 500;
            $message = __('voyager::generic.internal_error');

            if ($e->getCode()) {
                $code = $e->getCode();
            }

            if ($e->getMessage()) {
                $message = $e->getMessage();
            }

            return response()->json([
                'data' => [
                    'status'  => $code,
                    'message' => $message,
                ],
            ], $code);
        }
    }

    /**
     * Remove translations, images and files related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $dataType
     * @param \Illuminate\Database\Eloquent\Model $data
     *
     * @return void
     */
    protected function cleanup($dataType, $data)
    {
        // Delete Translations, if present
        if (is_bread_translatable($data)) {
            $data->deleteAttributeTranslations($data->getTranslatableAttributes());
        }

        // Delete Images
        $this->deleteBreadImages($data, $dataType->deleteRows->whereIn('type', ['image', 'multiple_images']));

        // Delete Files
        foreach ($dataType->deleteRows->where('type', 'file') as $row) {
            if (isset($data->{$row->field})) {
                foreach (json_decode($data->{$row->field}) as $file) {
                    $this->deleteFileIfExists($file->download_link);
                }
            }
        }

        // Delete media-picker files
        $dataType->rows->where('type', 'media_picker')->where('details.delete_files', true)->each(function ($row) use ($data) {
            $content = $data->{$row->field};
            if (isset($content)) {
                if (!is_array($content)) {
                    $content = json_decode($content);
                }
                if (is_array($content)) {
                    foreach ($content as $file) {
                        $this->deleteFileIfExists($file);
                    }
                } else {
                    $this->deleteFileIfExists($content);
                }
            }
        });
    }

    /**
     * Delete all images related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Database\Eloquent\Model $rows
     *
     * @return void
     */
    public function deleteBreadImages($data, $rows, $single_image = null)
    {
        $imagesDeleted = false;

        foreach ($rows as $row) {
            if ($row->type == 'multiple_images') {
                $images_to_remove = json_decode($data->getOriginal($row->field), true) ?? [];
            } else {
                $images_to_remove = [$data->getOriginal($row->field)];
            }

            foreach ($images_to_remove as $image) {
                // Remove only $single_image if we are removing from bread edit
                if ($image != config('voyager.user.default_avatar') && (is_null($single_image) || $single_image == $image)) {
                    $this->deleteFileIfExists($image);
                    $imagesDeleted = true;

                    if (isset($row->details->thumbnails)) {
                        foreach ($row->details->thumbnails as $thumbnail) {
                            $ext = explode('.', $image);
                            $extension = '.'.$ext[count($ext) - 1];

                            $path = str_replace($extension, '', $image);

                            $thumb_name = $thumbnail->name;

                            $this->deleteFileIfExists($path.'-'.$thumb_name.$extension);
                        }
                    }
                }
            }
        }

        if ($imagesDeleted) {
            event(new BreadImagesDeleted($data, $rows));
        }
    }

    /**
     * Order BREAD items.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        if (!isset($dataType->order_column) || !isset($dataType->order_display_column)) {
            return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager::bread.ordering_not_set'),
                'alert-type' => 'error',
            ]);
        }

        $model = app($dataType->model_name);
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $model = $model->withTrashed();
        }
        $results = $model->orderBy($dataType->order_column, $dataType->order_direction)->get();

        $display_column = $dataType->order_display_column;

        $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->whereField($display_column)->first();

        $view = 'voyager::bread.order';

        if (view()->exists("voyager::$slug.order")) {
            $view = "voyager::$slug.order";
        }

        return Voyager::view($view, compact(
            'dataType',
            'display_column',
            'dataRow',
            'results'
        ));
    }

    public function update_order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        $model = app($dataType->model_name);

        $order = json_decode($request->input('order'));
        $column = $dataType->order_column;
        foreach ($order as $key => $item) {
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $i = $model->withTrashed()->findOrFail($item['id']);
            } else {
                $i = $model->findOrFail($item['id']);
            }
            $i->$column = ($key + 1);
            $i->save();
        }
    }

    public function action(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $action = new $request->action($dataType, null);

        return $action->massAction(explode(',', $request->ids), $request->headers->get('referer'));
    }

    /**
     * Get BREAD relations data.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function relation(Request $request)
    {
        $slug = $this->getSlug($request);
        $page = $request->input('page');
        $on_page = 50;
        $search = $request->input('search', false);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $method = $request->input('method', 'add');

        $model = app($dataType->model_name);
        if ($method != 'add') {
            $model = $model->find($request->input('id'));
        }

        $this->authorize($method, $model);

        $rows = $dataType->{$method.'Rows'};
        foreach ($rows as $key => $row) {
            if ($row->field === $request->input('type')) {
                $options = $row->details;
                $model = app($options->model);
                $skip = $on_page * ($page - 1);

                // If search query, use LIKE to filter results depending on field label
                if ($search) {
                    // If we are using additional_attribute as label
                    if (in_array($options->label, $model->additional_attributes ?? [])) {
                        $relationshipOptions = $model->all();
                        $relationshipOptions = $relationshipOptions->filter(function ($model) use ($search, $options) {
                            return stripos($model->{$options->label}, $search) !== false;
                        });
                        $total_count = $relationshipOptions->count();
                        $relationshipOptions = $relationshipOptions->forPage($page, $on_page);
                    } else {
                        $total_count = $model->where($options->label, 'LIKE', '%'.$search.'%')->count();
                        $relationshipOptions = $model->take($on_page)->skip($skip)
                            ->where($options->label, 'LIKE', '%'.$search.'%')
                            ->get();
                    }
                } else {
                    $total_count = $model->count();
                    $relationshipOptions = $model->take($on_page)->skip($skip)->get();
                }

                $results = [];

                if (!$row->required && !$search) {
                    $results[] = [
                        'id'   => '',
                        'text' => __('voyager::generic.none'),
                    ];
                }

                foreach ($relationshipOptions as $relationshipOption) {
                    $results[] = [
                        'id'   => $relationshipOption->{$options->key},
                        'text' => $relationshipOption->{$options->label},
                    ];
                }

                return response()->json([
                    'results'    => $results,
                    'pagination' => [
                        'more' => ($total_count > ($skip + $on_page)),
                    ],
                ]);
            }
        }

        // No result found, return empty array
        return response()->json([], 404);
    }
}
