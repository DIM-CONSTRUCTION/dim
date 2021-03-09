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
use App\Form;
use App\FormDetail;
use App\Labor;
use App\Equip;
use App\FdLabor;
use App\FdEquip;
use App\LaborWorkType;
use App\Charts\SampleChart;
use App\Charts\MyFusion;
use App\Graph;
use App\GraphData;

class VoyagerChartsAnalysisController extends VoyagerBaseController
{
    use BreadRelationshipParser;

    public function index(Request $request)
    {


        // $chartActivity = new MyFusion;
        //
        // $labels = array();
        // $actualBudgetArray = array();
        // $plannedBudgetArray = array();
        // $forecastBudgetArray = array();
        //
        //
        // $activities = $myresultActivity['details'];
        //
        // foreach ($activities as $key => $activity) {
        //     $labels[$key] = $activity->date;
        //     $actualBudgetArray[$key] = $activity['data']['total']['totalActualBudgetDeviated'];
        //     $plannedBudgetArray[$key] = $activity['data']['total']['totalPlannedBugdet'];
        // }
        //
        // $chartActivity->labels($labels);
        // //$chart->category(['One', 'Two', 'Three', 'Four']);
        // $chartActivity->dataset('Planned Value', 'line', $plannedBudgetArray);
        // $chartActivity->dataset('Actual Value', 'line', $actualBudgetArray);
        // $chartActivity->title('Planned Budget vs Actual - per Activity (Marking & Support)');
        // $arrayMinMax = array_merge($plannedBudgetArray, $actualBudgetArray);
        // $optionsArray = array();
        // $optionsArray['yAxisMaxValue'] = max($arrayMinMax) * 1.01;
        // $optionsArray['yAxisMinValue'] = min($arrayMinMax) * 0.99;
        // $optionsArray['numDivLines'] = 10;
        // $optionsArray['adjustDiv'] = 0;
        //
        // $chartActivity->options($optionsArray, false);


        return view('voyager::chartsanalysis.index');
    }


    public function getBenchmarks(Request $request)
    {
      $benchmarks = Benchmark::where('project_id',$request->project_id)->get();

      return response(['benchmarks'=>$benchmarks]);

    }




    public function getChartAnalysis(Request $request)
    {


        $activityArray = array();
        $areasArray = array();

          $benchmark = Benchmark::where('id',$request->benchmark_id)->first();
        
          $activitiesBenchmarks = BenchmarkDetail::where('benchmark_id',$request->benchmark_id)->select('project_division_id','area_id')->get();
          $activitiesBenchmarks['project_division_id']  = $activitiesBenchmarks->pluck('project_division_id');
          $activitiesBenchmarks['area_id']  = $activitiesBenchmarks->pluck('area_id');
    
          $activities = FormDetail::whereIn('division_id',$activitiesBenchmarks['project_division_id'])->whereIn('area_id',$activitiesBenchmarks['area_id'])->select(DB::raw('DISTINCT division_id'))->get();
          $areas = FormDetail::whereIn('area_id',$activitiesBenchmarks['area_id']);
    
          if(isset($request->division_id))
          {
              $areas->where('division_id',$request->division_id);
          }
          $areas = $areas->select(DB::raw('DISTINCT area_id'))->get();
          
          $index =0 ;
          foreach ($activities as $key => $activity) {
              
              $division =  self::getFunction($benchmark->project_id,'project_divisions',$activity->division_id);
              if(isset($division))
               {
                  $activityArray[$index]['data'] = $activity;
                  $activityArray[$index]['division'] = $division;
                  $index++;
               }
               
               
               
            //   $activityArray[$key]['area'] = self::getFunction(1,'areas',$activity->area_id);
          }
            $index = 0 ;
            foreach ($areas as $key => $area) {
               $areaArr = self::getFunction($benchmark->project_id,'areas',$area->area_id);
               if(count($areaArr) > 0)
               {
                $areasArray[$index]= $areaArr;
                $index++;
               }
               
          }
          


      return response(['areas'=>$areasArray,'activities'=>$activityArray]);

    }

    public function getChartsAnalysisActivities(Request $request)
    {



      $charts = array();
      $charts[0]['title'] = 'Graph for the entire project budget';
      $charts[0]['value'] = 'ProjectBudget';
      $charts[0]['parent'] = 'Entire Budget';
      
      
      $charts[1]['title'] = 'Graph for the entire project material budget';
      $charts[1]['value'] = 'ProjectBudgetMaterial';
      $charts[1]['parent'] = 'Entire Budget';
      
      $charts[2]['title'] = 'Graph for the entire project labor budget';
      $charts[2]['value'] = 'ProjectBudgetLabor';
      $charts[2]['parent'] = 'Entire Budget';
      
      $charts[3]['title'] = 'Graph for the entire project budget per item ';
      $charts[3]['value'] = 'ProjectBudgetItem';
      $charts[3]['parent'] = 'Item Budget';
      $charts[3]['activity_id'] = true;
      
      $charts[4]['title'] = 'Graph for the entire project material budget per item';
      $charts[4]['value'] = 'ProjectBudgetMaterialItem';
      $charts[4]['parent'] = 'Item Budget';
      $charts[4]['activity_id'] = true;
      
      $charts[5]['title'] = 'Graph for the entire project labor budget per item';
      $charts[5]['value'] = 'ProjectBudgetLaborItem';
      $charts[5]['parent'] = 'Item Budget';
      $charts[5]['activity_id'] = true;
      
      $charts[6]['title'] = 'Graph for the item budget per area';
      $charts[6]['value'] = 'ProjectBudgetArea';
      $charts[6]['parent'] = 'Item Budget per area';
      $charts[6]['activity_id'] = true;
      $charts[6]['area_id'] = true;
      
      $charts[7]['title'] = 'Graph for the item material budget per area';
      $charts[7]['value'] = 'ProjectBudgetMaterialArea';
      $charts[7]['parent'] = 'Item Budget per area';
      $charts[7]['activity_id'] = true;
      $charts[7]['area_id'] = true;
      
      $charts[8]['title'] = 'Graph for the item labor budget per area';
      $charts[8]['value'] = 'ProjectBudgetLaborArea';
      $charts[8]['parent'] = 'Item Budget per area';
      $charts[8]['activity_id'] = true;
      $charts[8]['area_id'] = true;
      
      
    //   $charts[1]['title'] = 'Item Budget';
    //   $charts[1]['value'] = 'Graphlaborcost';
    //   $charts[2]['title'] = 'Item Budget per area';
    //   $charts[2]['value'] = 'Graphquantity';
    //   $charts[3]['title'] = 'Graph Schedule time';
    //   $charts[3]['value'] = 'Graphscheduletime';
 


      return response(['charts'=>$charts]);
    }


    function getTableData($activities,$chartArray,$title)
    {
        
        
        $arrayMinMax = array();
      $labels = array();
   

      foreach ($activities as $key => $activity) {
          

           
           foreach ($chartArray as  $keyChart=>$chart) {
                $labels['chart'][$key]['label'] = $activity->date;
                $labels['chart'][$key]['data'][$keyChart]['value'] =  $activity['data']['total'][$chartArray[$keyChart]->name];
                $labels['chart'][$key]['data'][$keyChart]['key'] =  $chartArray[$keyChart]->seriesname;
                
  
        //       $chartArray[$keyChart]['data'][$key]['value'] = $activity['data']['total'][$chartArray[$keyChart]['name']];
        //         array_push($arrayMinMax,$activity['data']['total'][$chartArray[$keyChart]['name']]);


           }
      }
      
      
      
      $labels['title'] =  $chartArray;

      $optionsArray = array();
    //  $optionsArray['yAxisMaxValue'] = max($arrayMinMax) * 1.01;

      $optionsArray['table'] =  $chartArray;
      $optionsArray['titles'] =  $chartArray;
 
      
        
        return $labels;
    }

    function getChartData($activities,$chartArray,$title)
    {



      $arrayMinMax = array();
      $labels = array();
      $desc = $title.' Description: <br/>';
      $chartArr = array();

      foreach ($activities as $key => $activity) {
          $labels[$key]['label'] = $activity->date;
   
          foreach ($chartArray as  $keyChart=>$chart) {
              
            
              $chartArr[$keyChart]['dashed'] = $chart->dashed;
              $chartArr[$keyChart]['color'] = $chart->color;
              $chartArr[$keyChart]['seriesname'] = $chart->seriesname;
              $chartArr[$keyChart]['data'][$key]['value'] = $activity['data']['total'][$chart->name];
                array_push($arrayMinMax,$activity['data']['total'][$chart->name]);


          }
      }
      
      foreach ($chartArray as  $keyChart=>$chart) {
              
              $desc .= '<span class="dot" style="background-color:'.$chart->color.';height: 10px;width: 10px;border-radius: 50%;display: inline-block;margin-right: 10px;"></span><span >'.$chart->seriesname.' : '.$chart->description.'</span> <br/>';
      }

      $optionsArray = array();
    //  $optionsArray['yAxisMaxValue'] = max($arrayMinMax) * 1.01;
      $optionsArray['yAxisMinValue'] = min($arrayMinMax) * 0.99;
      $optionsArray['numDivLines'] = 10;
      $optionsArray['adjustDiv'] = 0;
      $optionsArray['charts'] =  $chartArr;
      $optionsArray['title'] =  $title;
      $optionsArray['labels'] =  $labels;
      $optionsArray['description'] =  $desc;
      

      return $optionsArray;

    }


    public function getChartsActivityAnalysis(Request $request)
    {

        $area = null;
        $activityId = null;
        if(isset($request->chart['area_id']))
        {
            $area = $request->area;
        }
         if(isset($request->chart['activity_id']))
        {
            $activityId = $request->activity_id;
        }
        // return response(['chart'=?])
         if(isset($area))
         {
            $graph = new Graph($request->benchmark_id,$request->date,$activityId,$area['id'],$request->chart['parent']);
         }
         else{
            $graph = new Graph($request->benchmark_id,$request->date,$activityId,null,$request->chart['parent']);
         }
      


      $chartActivity = new MyFusion;

      $actualBudgetArray = array();
      $plannedBudgetArray = array();
      $forecastBudgetArray = array();


      $chartArray =  array();
      $arrayMinMax = array();
      $title = '';
     

      $division = ProjectDivision::where('id',$request->activity_id)->first();


      if($request->chart['value'] == 'ProjectBudget')
      {
        $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();
        
        $chartArray[0] = new GraphData(T38['name'],'totalT38','Activities (Labor + Material) executed till today including the actual deviation.','#AF0000','1',true);
        $chartArray[1] = new GraphData(B54['name'],'totalB54','The initial approved budget of the project.','#33D1FF','0',true);
        

        $title = 'Project Budget';
      }

      if($request->chart['value'] == 'ProjectBudgetMaterial')
      {
           $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();
           
           $chartArray[0] = new GraphData(M68['name'],'totalM68',' Material executed quantity (meaning the trade quantity) till today including the actual deviation.','#AF0000','1',true);
           $chartArray[1] = new GraphData(B53['name'],'totalB53',' The initial approved budget of all the material (meaning all the trades) in the project.','#33D1FF','0',true);
          
         
        
        $title = 'Project Budget Material';
      }
      if($request->chart['value'] == 'ProjectBudgetLabor')
      {
           $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();
           
           $chartArray[0] = new GraphData(H68['name'],'totalH68',' Cumulative of all labor hours performed cost including the deviation that already took place (actual deviation).','#AF0000','1',true);
           $chartArray[1] = new GraphData(B52['name'],'totalB52',' The latest revision of the approved labor hours budget for the entire project.','#33D1FF','0',true);
        //   $chartArray[2] = new GraphData(H55['name'],'totalH55','','#AF0000','1',false);
        //   $chartArray[3] = new GraphData('Forecasted Project Budget Material Trend','totalForecastedProjectBudgetLaborTrend','The forecasted trend of the remaining labour hours budget if action regarding deviation is not taken','#6D00AF','1',true);

           

        $title = 'Project Budget Labor';
      }
      
      
      
      
      
      if($request->chart['value'] == 'ProjectBudgetItem')
      {
        $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();


           $chartArray[0] = new GraphData(T17['name'],'totalT17',' Planned activity budget per area in the selected budget/benchmark revision.','#0000CD','0',true);
           $chartArray[1] = new GraphData(T16['name'],'totalT16','Actual budget per area including the deviation that already took place (actual deviation).','#9BFF00','0',true);
           $chartArray[2] = new GraphData(T36['name'],'totalT36','Forecasted activity trend per project if action regarding deviation is not taken.','#AF0000','1',true);
           $chartArray[3] = new GraphData(B50['name'],'totalB50',' Planned activity budget per area in the selected budget/benchmark revision.','#9400d3','0',true);

          


        $title = 'Project Budget per Activity ('.$division->title.')';
      }

      if($request->chart['value'] == 'ProjectBudgetMaterialItem')
      {
           $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();
           
           
           $chartArray[0] = new GraphData(M42['name'],'totalM42','Planned material budget per area in the selected budget/benchmark revision.','#0000CD','1',true);
           $chartArray[1] = new GraphData(M41['name'],'totalM41','Actual material budget per area including the deviation that already took place (actual deviation).','#9BFF00','0',true);
           $chartArray[2] = new GraphData(M66['name'],'totalM66','Forecasted material trend per project if action regarding deviation is not taken.','#AF0000','1',true);
           $chartArray[3] = new GraphData(B49['name'],'totalB49','Planned material budget per area in the selected budget/benchmark revision.','#9400d3','0',true); 


          
        
        $title = 'Project Budget Material per Activity ('.$division->title.')';
      }
      if($request->chart['value'] == 'ProjectBudgetLaborItem')
      {
           $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();
           
           $chartArray[0] = new GraphData(H42['name'],'totalH42','Planned labor hour budget per area in the selected budget/benchmark revision.','#0000CD','0',true);
           $chartArray[1] = new GraphData(H41['name'],'totalH41','Actual labor hour budget per area including the deviation that already took place (actual deviation).','#9BFF00','0',true);
           $chartArray[2] = new GraphData(H66['name'],'totalH66','Forecasted labor hour trend per project if action regarding deviation is not taken.','#AF0000','1',true);
           $chartArray[3] = new GraphData(B47['name'],'totalB47','Planned labor hour budget per area in the selected budget/benchmark revision.','#9400d3','0',true);


        $title = 'Project Budget Labor per Activity ('.$division->title.')';
      }
      
      
      
      if($request->chart['value'] == 'ProjectBudgetArea')
      {
        $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();
        
        
           $chartArray[0] = new GraphData(T05['name'],'totalCUMT05',T05['desc'],'#33D1FF','0',true);
           $chartArray[1] = new GraphData(T10['name'],'totalCUMT10','The cumulative planned cost per executed labor hours and quantities.','#9BFF00','0',true);
           $chartArray[2] = new GraphData(T35['name'],'totalT35','','#AF0000','1',true);
           $chartArray[3] = new GraphData(T30['name'],'totalT30','The forecasted trend of the remaining works budget if action regarding deviation is not taken.','#AF0000','1',false);
           $chartArray[4] = new GraphData(B40['name'],'totalB40','','#9400d3','0',true);

    

        $title = 'Project Budget per Area ('.$area['name'].') per Activity ('.$division->title.')';
      }

      if($request->chart['value'] == 'ProjectBudgetMaterialArea')
      {
           $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();
           
           
           $chartArray[0] = new GraphData(M20['name'],'totalCUMM20','Cumulative planned material-unit cost per executed material unit quantity percentage.','#33D1FF','0',true);
           $chartArray[1] = new GraphData(M25['name'],'totalCUMM25','Cumulative material performed cost todate.','#9BFF00','0',true);
           $chartArray[2] = new GraphData(M65['name'],'totalM65','Forecasted material trend per area if action regarding deviation is not taken.','#AF0000','1',true);
           $chartArray[3] = new GraphData(M55['name'],'totalM55','','#AF0000','1',false);
           $chartArray[4] = new GraphData(B10['name'],'totalB10','Planned material budget per area in the selected budget/benchmark revision.','#9400d3','0',true);

        
        
        $title = 'Project Budget Material per Area ('.$area['name'].') per Activity ('.$division->title.')';
      }
      if($request->chart['value'] == 'ProjectBudgetLaborArea')
      {
           $myresult =  $graph->getPlannedBudgetVsActualVsForecastedTrend();
           
           $chartArray[0] = new GraphData(H20['name'],'totalCUMH20','Cumulative planned labor hours cost todate per executed material quantity per unit.','#33D1FF','0',true);
           $chartArray[1] = new GraphData(H25['name'],'totalCUMH25','Cumulative labor hours performed cost todate.','#9BFF00','0',true);
           $chartArray[2] = new GraphData(H65['name'],'totalH65','Forecasted labor hours trend per area if action regarding deviation is not taken','#AF0000','1',true);
           $chartArray[3] = new GraphData(H55['name'],'totalH55','','#AF0000','1',false);
           $chartArray[4] = new GraphData(B30['name'],'totalB30','Planned labor budget per area in the selected budget/benchmark revision.','#9400d3','0',true);

            
  


        $title = 'Project Budget Labor per Area ('.$area['name'].')  per Activity ('.$division->title.')' ;
      }
      

      $activities = $myresult['details'];
      
      
      //return $activities;
      //echo $activities;
      $optionsArray = array();
      
       $tableArray = array();

      $tableArray = self::getTableData($activities,$chartArray,$title);
      
      $chartArray = array_filter($chartArray, function ($chart){ return $chart->is_displayed; }); 
      $chartArray=  array_values( $chartArray );
      

      $optionsArray = self::getChartData($activities,$chartArray,$title);
      
    





      return response(['chart_activity'=>$optionsArray,'tableArray'=>$tableArray]);
    }





    function calcTotalMaterialCost ($qty,$unit_material_rate)
    {
       return $unit_material_rate * $qty;
    }
    function calcTotalLaborHour ($totalqty,$unit_labor_hour)
    {
       return $unit_labor_hour * $totalqty;
    }

    function calcTotalUnitRate ($unit_labor_hour,$unit_material_rate)
    {
       return $unit_material_rate + $unit_labor_hour;
    }

    function calcTotalLaborCost ($unit_labor_hour,$totalQty)
    {
       return $totalQty * $unit_labor_hour;
    }
    function calcLaborUnitCost($unit_labor_hour,$hours_unit)
    {
        return $unit_labor_hour * $hours_unit;
    }





    function getFunction($project_id,$type,$id)
    {
      $areasArray = array();
      $dataTypeContent = DB::table($type)->where('project_id',$project_id)->whereNull('parent_id')->get();

      foreach ($dataTypeContent as $key => $area) {
        $areaList = DB::table($type)->where('project_id',$area->project_id)->where('parent_id',$area->id)->get();

          if(count($areaList) == 0)
          {
            $areaArray = array();
            if($type == 'areas')
            {
                $areaArray['name'] = $area->name;
            }
            else{
                $areaArray['title'] = $area->title;
            }
            $areaArray['id'] = $area->id;
            $areasArray[] = $areaArray;
          }
          else{
            if($type == 'areas')
            {
                  $areaName = $area->name;
            }
            else{
                  $areaName = $area->title;
            }

            $areasArray = self::functionList($areasArray,$areaList,$areaName,$type,$id);
          }
        // code...
      }

      return $areasArray;
    }


    function functionList($areasArray,$list,$name,$type,$id)
    {
      $areasName = '';
    //  $areasArray = array();
      foreach ($list as $key => $item) {
          $areaList = DB::table($type)->where('project_id',$item->project_id)->where('parent_id',$item->id)->get();

          if(count($areaList) == 0)
          {

            $areaArray = array();
            if($type == 'areas')
            {
                $areaArray['name'] = $item->name;
            }
            else{
                $areaArray['title'] = $item->reference." - ".$item->title;
            }


            $areaArray['parents'] = $name;
            $areaArray['id'] = $item->id;
            if(($id == $item->id) || ($id == 0))
            {
              if($id == $item->id)
              {
                $areasArray = $areaArray;
              }
              else{
                $areasArray[] = $areaArray;
              }

            }

          }
          else{
            if($type == 'areas')
            {
                  $areaName = $name.' » '.$item->name;
            }
            else{
                $areaName = $name.' » '.$item->title;
            }

            $areasArray = self::functionList($areasArray,$areaList,$areaName,$type,$id);
          }
        }
        return $areasArray;
    }





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


    public function getAreas(Request $request)
    {

      $form = Form::where('id',$request->params['form_id'])->first();

      $areasArray = array();
      $areasArray = self::getFunction($form->project_id,'areas',0);

      return response(['areas'=>$areasArray]);
    }


    public function addActivity(Request $request)
    {

      $form = Form::where('id',$request->params['form_id'])->first();

      $activityArray = array();

      $activityArray['division'] = $request->params['division'];

      $activity = FormDetail::where('division_id',$request->params['division']['id'])
                              ->where('form_id',$request->params['form_id'])
                              ->where('area_id',$request->params['area'])
                              ->first();
        if($activity == null)
        {
            $activity_id = FormDetail::insertGetId(['division_id'=>$request->params['division']['id'],'form_id'=>$request->params['form_id'],
            'area_id'=>$request->params['area'],'quantity'=>'0','percentage_completed'=>'0']);
              $activity = FormDetail::where('id',$activity_id)->first();
        }
        $activityArray['data'] = $activity;

      return response(['activity'=>$activityArray]);

    }

    public function getGangs(Request $request)
    {

        $form = Form::where('id',$request->params['form_id'])->first();
        $labor = FdLabor::where('form_details_id',$request->params['activity_id'])
                ->leftJoin('labors','labors.id','=','fd_labors.labor_id')
                ->leftJoin('labor_work_types','labor_work_types.id','=','fd_labors.labor_work_type_id')
                ->select('fd_labors.*','labors.full_name','labor_work_types.name')
                ->get();

      return response(['labor'=>$labor]);

    }
    public function addGang(Request $request)
    {

      $labor = array();
      if(FdLabor::where('form_details_id',$request->c_activity)->where('labor_id',$request->labor)->doesntExist())
      {
        $fdLabor_id = FdLabor::insertGetId(['labor_id'=>$request->labor,'form_details_id'=>$request->c_activity,
        'hours_of_work'=>$request->work_hrs,'extra_hours_of_work'=>$request->extra_hrs,'labor_work_type_id'=>$request->workType,'created_at'=>date('Y-m-d H:i:s')]);

        $labor = FdLabor::where('fd_labors.id',$fdLabor_id)
                        ->leftJoin('labors','labors.id','=','fd_labors.labor_id')
                        ->leftJoin('labor_work_types','labor_work_types.id','=','fd_labors.labor_work_type_id')
                        ->select('fd_labors.*','labors.full_name','labor_work_types.name')
                        ->first();

      }

              return response(['labor'=>$labor]);

    }


    public function updateGang(Request $request)
    {

      if($request['params']['type'] == 'workhrs')
      {
          FdLabor::where('id',$request['params']['labor_id'])->update(['hours_of_work'=>$request['params']['data']]);
      }
      if($request['params']['type'] == 'extrahrs')
      {
          FdLabor::where('id',$request['params']['labor_id'])->update(['extra_hours_of_work'=>$request['params']['data']]);
      }
      if($request['params']['type'] == 'worktype')
      {
          FdLabor::where('id',$request['params']['labor_id'])->update(['labor_work_type_id'=>$request['params']['data']]);
      }

        return $request;

    }

    public function updateActivity(Request $request)
    {
        FormDetail::where('id',$request->activity_id)->update(['quantity'=>$request->quantity,'percentage_completed'=>$request->percentage]);
    }

    public function seachGangs(Request $request)
    {

        $form = Form::where('id',$request->params['form_id'])->first();
        $labor = Labor::all();
        $workType = LaborWorkType::all();

      return response(['labor'=>$labor,'work'=>$workType]);

    }



    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  EQUIPMENTS
    //
    //****************************************

    public function updateEquipment(Request $request)
    {

      if($request['params']['type'] == 'usehrs')
      {
          FdEquip::where('id',$request['params']['equipment_id'])->update(['hours_of_use'=>$request['params']['data']]);
      }
      if($request['params']['type'] == 'extrahrs')
      {
          FdEquip::where('id',$request['params']['equipment_id'])->update(['extra_hours_of_work'=>$request['params']['data']]);
      }
      if($request['params']['type'] == 'worktype')
      {
          FdEquip::where('id',$request['params']['equipment_id'])->update(['labor_work_type_id'=>$request['params']['data']]);
      }

        return $request;

    }

    public function seachEquipments(Request $request)
    {

        $form = Form::where('id',$request->params['form_id'])->first();
        $equipment = Equip::all();


      return response(['equipment'=>$equipment]);

    }

    public function getEquipments(Request $request)
    {

        $form = Form::where('id',$request->params['form_id'])->first();
        $equipment = FdEquip::where('form_details_id',$request->params['activity_id'])
                            ->leftJoin('equips','equips.id','=','fd_equips.equip_id')
                            ->select('fd_equips.*','equips.name')
                            ->get();

      return response(['equipment'=>$equipment]);

    }

    public function addEquipment(Request $request)
    {

        $equipment = array();
        if(FdEquip::where('form_details_id',$request->c_activity)->where('equip_id',$request->equip)->doesntExist())
        {
          $FdEquip_id = FdEquip::insertGetId(['equip_id'=>$request->equip,'form_details_id'=>$request->c_activity,
          'hours_of_use'=>$request->hours_of_use,'created_at'=>date('Y-m-d H:i:s')]);

          $equipment = FdEquip::where('fd_equips.id',$FdEquip_id)
                                ->leftJoin('equips','equips.id','=','fd_equips.equip_id')
                                ->select('fd_equips.*','equips.name')
                                ->first();

        }

        return response(['equipment'=>$equipment]);

    }



    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

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
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
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

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
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
                $i = $model->withTrashed()->findOrFail($item->id);
            } else {
                $i = $model->findOrFail($item->id);
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
