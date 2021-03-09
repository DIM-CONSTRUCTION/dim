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
use App\Graph;
use Illuminate\Support\Arr;

class VoyagerInstantAnalysisController extends VoyagerBaseController
{
    use BreadRelationshipParser;
    var $array = array();
    var $divisions = array();

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
        return view('voyager::instantanalysis.index');
    }


    public function getBenchmarks(Request $request)
    {
      $benchmarks = Benchmark::where('project_id',$request->project_id)->get();
      
      $currency = Project::where('projects.id',$request->project_id)->leftJoin('currencies','currencies.id','=','projects.currency_id')->select('currencies.*')->first()->symbol;
      
      $start_date = Project::where('projects.id',$request->project_id)->first()->start_date;


      return response(['benchmarks'=>$benchmarks,'currency'=>$currency,'start_date'=>$start_date]);

    }

    public function getForms(Request $request)
    {
      $forms = Form::where('project_id',$request->project_id)->get();
      $currency = Project::where('projects.id',$request->project_id)->leftJoin('currencies','currencies.id','=','projects.currency_id')->select('currencies.*')->first()->symbol;
      $start_date = Project::where('projects.id',$request->project_id)->first()->start_date;

      return response(['forms'=>$forms,'currency'=>$currency,'start_date'=>$start_date]);

    }

    public function getAnalysisActivities(Request $request)
    {
      $form = Form::where('id',$request->form_id)->first();
    //  $benchmark = Benchmark::where('id',$request->benchmark_id)->first();

      $activityArray = array();

      $activitiesBenchmarks = BenchmarkDetail::where('benchmark_id',$request->benchmark_id)->select('project_division_id','area_id')->get();
      $activitiesBenchmarks['project_division_id']  = $activitiesBenchmarks->pluck('project_division_id');
      $activitiesBenchmarks['area_id']  = $activitiesBenchmarks->pluck('area_id');

      $activities = FormDetail::where('form_id',$request->form_id)->whereIn('division_id',$activitiesBenchmarks['project_division_id'])->whereIn('area_id',$activitiesBenchmarks['area_id'])->get();



      foreach ($activities as $key => $activity) {

          $activityArray[$key]['data'] = $activity;
          $activityArray[$key]['division'] = self::getFunction($form->project_id,'project_divisions',$activity->division_id);
          $activityArray[$key]['area'] = self::getFunction($form->project_id,'areas',$activity->area_id);

      }


      return response(['activities'=>$activityArray]);

    }
    
    public function getActivities(Request $request)
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
    
    
     public function getInstant()
    {



      $charts = array();
      $charts[0]['title'] = '0.9:  All activities in the entire project (Cumulative Budget)';
      $charts[0]['value'] = '90';
      $charts[0]['total'] = true;
      
      
      $charts[1]['title'] = '0.3: per selected activity in the entire project (Cumulative Budget)';
      $charts[1]['value'] = '80';
      $charts[1]['activity_id'] = true;
      $charts[1]['total'] = true;
      
      $charts[2]['title'] = '0.1.0.3: per selected activity progress in all active areas (Cumulative Budget)';
      $charts[2]['value'] = '70';
      $charts[2]['activity_id'] = true;
      $charts[2]['total'] = true;
      
      $charts[3]['title'] = '0.1.0.3: per selected activity progress in all active areas (Cumulative Cost)';
      $charts[3]['value'] = '60';
      $charts[3]['activity_id'] = true;
      $charts[3]['total'] = true;
      
      $charts[4]['title'] = '0.2: per selected activity per selected area (Cumulative Cost)';
      $charts[4]['value'] = '50';
      $charts[4]['activity_id'] = true;
      $charts[4]['area_id'] = true;
      $charts[4]['total'] = true;
      
      $charts[5]['title'] = '0.1: per progress of specific activity per executed or planned quantity (Cumulative Cost)';
      $charts[5]['value'] = '40';
      $charts[5]['activity_id'] = true;
      $charts[5]['area_id'] = true;
      $charts[5]['total'] = true;
      
      $charts[6]['title'] = '0.1: per progress of specific activity per executed or planned quantity (Cost per day )';
      $charts[6]['value'] = '30';
      $charts[6]['activity_id'] = true;
      $charts[6]['area_id'] = true;
      $charts[6]['total'] = true;
      
      $charts[7]['title'] = '0.1: per progress of specific activity per executed or planned quantity (Cumulative Quantity)';
      $charts[7]['value'] = '20';
      $charts[7]['activity_id'] = true;
      $charts[7]['area_id'] = true;
      $charts[7]['total'] = false;
      
      
      $charts[8]['title'] = '0.1: per progress of specific activity per executed or planned quantity (Quantity per day )';
      $charts[8]['value'] = '10';
      $charts[8]['activity_id'] = true;
      $charts[8]['area_id'] = true;
      $charts[8]['total'] = false;
   



     return response(['charts'=>$charts]);
    }
    
    
    public function getData(Request $request)
    {
        
        $areaId = null;
        $activityId = null;
        if(isset($request->area_id))
        {
            $areaId = $request->area_id;
        }
         if(isset($request->activity_id))
        {
            $activityId = $request->activity_id;
        }
        
        
        $data = new Graph($request->benchmark_id,$request->date,$activityId,$areaId,null);
        
        $data = $data->getPlannedBudgetVsActualVsForecastedTrend();
        $dataArr = array();
        
        

        foreach($data['details'] as $key=>$item)
        {
           
             $total = $item['data']['total'];
             $dataArr[$key]['date'] = $item['date'];
             $is_percentage = $item['data']['is_percentage'];
             
             //Planned Budget
            if($request->chart == '90')
            {
                
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';
                $dataArr[$key]['row']['header'][2] = 'Total';
                
                
               
                $dataArr[$key]['row'][0]['abr'] = 'B';
                $dataArr[$key]['row'][0]['title'] = 'Benchmark Budget';
                
                
                $dataArr[$key]['row'][0][0]['data'] = $request->currency." ".number_format($total['totalB52'],1);
                $dataArr[$key]['row'][0][1]['data']  = $request->currency." ".number_format($total['totalB53'],1);
                $dataArr[$key]['row'][0][2]['data']  = $request->currency." ".number_format($total['totalB54'],1);
                
                
                $dataArr[$key]['row'][1]['abr']  = 'FT';
                $dataArr[$key]['row'][1]['title'] = 'Forecasted Trend';
                
                $dataArr[$key]['row'][1][0]['data'] = $request->currency." ".number_format($total['totalH68'],1);
                $dataArr[$key]['row'][1][1]['data'] = $request->currency." ".number_format($total['totalM68'],1);
                $dataArr[$key]['row'][1][2]['data'] = $request->currency." ".number_format($total['totalT38'],1);
                
                $dataArr[$key]['row'][2]['abr'] = 'FD';
                $dataArr[$key]['row'][2]['title'] = 'Forecasted Deviation';
                
                $dataArr[$key]['row'][2][0]['data'] = $request->currency." ".number_format($total['totalH69'],1);
                $dataArr[$key]['row'][2][0]['class'] = $total['totalH69'] > 0 ? 'pos':'neg';
                $dataArr[$key]['row'][2][1]['data'] = $request->currency." ".number_format($total['totalM69'],1);
                $dataArr[$key]['row'][2][1]['class'] = $total['totalM69'] > 0 ? 'pos':'neg';
                $dataArr[$key]['row'][2][2]['data'] = $request->currency." ".number_format($total['totalT39'],1);
                $dataArr[$key]['row'][2][2]['class'] = $total['totalT39'] > 0 ? 'pos':'neg';
            }
            
            if($request->chart == '80')
            {
             
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';
                $dataArr[$key]['row']['header'][2] = 'Total';
                
                $dataArr[$key]['row'][0]['abr'] = 'B';
                $dataArr[$key]['row'][0]['title'] = 'Benchmark Budget';
                
                $dataArr[$key]['row'][0][0]['data']  = $request->currency." ".number_format($total['totalB47'],1);
                $dataArr[$key]['row'][0][1]['data'] = $request->currency." ".number_format($total['totalB49'],1);
                $dataArr[$key]['row'][0][2]['data'] = $request->currency." ".number_format($total['totalB50'],1);
               
                $dataArr[$key]['row'][1]['abr'] = 'FT';
                $dataArr[$key]['row'][1]['title'] = 'Forecasted Trend';
                
                
                $dataArr[$key]['row'][1][0]['data'] = $request->currency." ".number_format($total['totalH66'],1);
                
                $dataArr[$key]['row'][2]['abr'] = 'FD';
                $dataArr[$key]['row'][2][0]['data'] = $request->currency." ".number_format($total['totalH67'],1);
                $dataArr[$key]['row'][2][0]['class'] = $total['totalH67'] > 0 ? 'pos':'neg';
               
   
                     
                 $dataArr[$key]['row'][1][1]['data'] = $request->currency." ".number_format($total['totalM66'],1);
                 $dataArr[$key]['row'][1][2]['data'] = $request->currency." ".number_format($total['totalT36'],1);
                 
                 $dataArr[$key]['row'][2][1]['data'] = $request->currency." ".number_format($total['totalM67'],1);
                 $dataArr[$key]['row'][2][1]['class'] = $total['totalM67'] > 0 ? 'pos':'neg';
                 $dataArr[$key]['row'][2][2]['data'] = $request->currency." ".number_format($total['totalT37'],1);
                 $dataArr[$key]['row'][2][2]['class'] = $total['totalT37'] > 0 ? 'pos':'neg';
                
               
                
                
            }
            
            
            if($request->chart == '70')
            {
             
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';
                $dataArr[$key]['row']['header'][2] = 'Total';
                
                
                $dataArr[$key]['row'][0]['abr'] = 'B';
                $dataArr[$key]['row'][0]['title'] = 'Benchmark Budget';
                
                $dataArr[$key]['row'][0][0]['data']  = $request->currency." ".number_format($total['totalH42'],1);
                
                
                
                $dataArr[$key]['row'][1]['abr'] = 'A';
                $dataArr[$key]['row'][1]['title'] = 'Actual Budget';
                
                $dataArr[$key]['row'][1][0]['data'] = $request->currency." ".number_format($total['totalH41'],1);
              
                
                $dataArr[$key]['row'][2]['abr'] = 'D';
                $dataArr[$key]['row'][2]['title'] = 'Deviated Budget';
                
                $dataArr[$key]['row'][2][0]['data'] = $request->currency." ".number_format($total['totalH56'],1);
                $dataArr[$key]['row'][2][0]['class'] = $total['totalH56'] > 0 ? 'pos':'neg';
                

                $dataArr[$key]['row'][0][1]['data'] = $request->currency." ".number_format($total['totalM42'],1);
                $dataArr[$key]['row'][0][2]['data'] = $request->currency." ".number_format($total['totalT17'],1);
                $dataArr[$key]['row'][1][1]['data'] = $request->currency." ".number_format($total['totalM41'],1);
                $dataArr[$key]['row'][1][2]['data'] = $request->currency." ".number_format($total['totalT16'],1);
                $dataArr[$key]['row'][2][1]['data'] = $request->currency." ".number_format($total['totalM46'],1);
                $dataArr[$key]['row'][2][1]['class'] = $total['totalM46'] > 0 ? 'pos':'neg';
                $dataArr[$key]['row'][2][2]['data'] = $request->currency." ".number_format($total['totalT18'],1);
                $dataArr[$key]['row'][2][2]['class'] = $total['totalT18'] > 0 ? 'pos':'neg';
                
            }
            
            
            
            if($request->chart == '60')
            {
                
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';
                $dataArr[$key]['row']['header'][2] = 'Total';
                
                $dataArr[$key]['row'][0]['abr']= 'B';
                $dataArr[$key]['row'][0]['title'] = 'Benchmark Budget';
                
                $dataArr[$key]['row'][0][0]['data'] = $request->currency." ".number_format($total['totalH42'],1);
                $dataArr[$key]['row'][0][1]['data']= $request->currency." ".number_format($total['totalM42'],1);
                $dataArr[$key]['row'][0][2]['data'] = $request->currency." ".number_format($total['totalT17'],1);
                
             
                $dataArr[$key]['row'][1]['abr'] = 'P';
                $dataArr[$key]['row'][2]['title'] = 'Planned Budget';
                
                $dataArr[$key]['row'][1][0]['data']  = $request->currency." ".number_format($total['totalH21'],1);
                $dataArr[$key]['row'][1][1]['data'] = $request->currency." ".number_format($total['totalM21'],1);
                $dataArr[$key]['row'][1][2]['data'] = $request->currency." ".number_format($total['totalT06'],1);
                
                
                $dataArr[$key]['row'][2]['abr'] = 'A';
                $dataArr[$key]['row'][2]['title'] = 'Actual Budget';
                
                $dataArr[$key]['row'][2][0]['data'] = $request->currency." ".number_format($total['totalH26'],1);
                $dataArr[$key]['row'][2][1]['data'] = $request->currency." ".number_format($total['totalM26'],1);
                $dataArr[$key]['row'][2][2]['data'] = $request->currency." ".number_format($total['totalT11'],1);
              
                
                $dataArr[$key]['row'][3]['abr'] = 'D';
                $dataArr[$key]['row'][3]['title'] = 'Deviated Budget';
                
                $dataArr[$key]['row'][3][0]['data'] = $request->currency." ".number_format($total['totalH56'],1);
                $dataArr[$key]['row'][3][0]['class'] = $total['totalH56'] > 0 ? 'pos':'neg';
                $dataArr[$key]['row'][3][1]['data'] = $request->currency." ".number_format($total['totalM46'],1);
                $dataArr[$key]['row'][3][1]['class'] = $total['totalM46'] > 0 ? 'pos':'neg';
                $dataArr[$key]['row'][3][2]['data'] = $request->currency." ".number_format($total['totalT18'],1);
                $dataArr[$key]['row'][3][2]['class'] = $total['totalT18'] > 0 ? 'pos':'neg';
                
         
                
 
                
            }
            
            
            
            if($request->chart == '50')
            {
             
             
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';
                $dataArr[$key]['row']['header'][2] = 'Total';
                
                
                $dataArr[$key]['row'][0]['abr'] = 'B';
                $dataArr[$key]['row'][0]['title'] = 'Benchmark Budget';
                
                $dataArr[$key]['row'][0][0]['data']  = $request->currency." ".number_format($total['totalB30'],1);
                $dataArr[$key]['row'][0][1]['data'] = $request->currency." ".number_format($total['totalB10'],1);
                $dataArr[$key]['row'][0][2]['data'] = $request->currency." ".number_format($total['totalB40'],1);
                
                
                $dataArr[$key]['row'][1]['abr'] = 'FT';
                $dataArr[$key]['row'][1]['title'] = 'Forecasted Trend';
                
                $dataArr[$key]['row'][1][0]['data'] = $request->currency." ".number_format($total['totalH65'],1);
               
                
                $dataArr[$key]['row'][2]['abr'] = 'FD';
                $dataArr[$key]['row'][2]['title'] = 'Benchmark Budget';
                $dataArr[$key]['row'][2][0]['data'] = $request->currency." ".number_format($total['totalH60'],1);
                $dataArr[$key]['row'][2][0]['class'] = $total['totalH60'] > 0 ? 'pos':'neg';
               

                 $dataArr[$key]['row'][1][1]['data'] = $request->currency." ".number_format($total['totalM65'],1);
                 $dataArr[$key]['row'][1][2]['data'] = $request->currency." ".number_format($total['totalT35'],1);
                 $dataArr[$key]['row'][2][1]['data'] = $request->currency." ".number_format($total['totalM60'],1);
                 $dataArr[$key]['row'][2][1]['class'] = $total['totalM60'] > 0 ? 'pos':'neg';
                 $dataArr[$key]['row'][2][2]['data'] = $request->currency." ".number_format($total['totalT25'],1);
                 $dataArr[$key]['row'][2][2]['class'] = $total['totalT25'] > 0 ? 'pos':'neg';
            }
            
            
            if($request->chart == '40')
            {
                
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';
                $dataArr[$key]['row']['header'][2] = 'Total';
                
             
                $dataArr[$key]['row'][0]['abr'] = 'B';
                $dataArr[$key]['row'][0]['title'] = 'Benchmark Budget';
                
                $dataArr[$key]['row'][0][0]['data']  = $request->currency." ".number_format($total['totalB30'],1);
                $dataArr[$key]['row'][0][1]['data'] = $request->currency." ".number_format($total['totalB10'],1);
                $dataArr[$key]['row'][0][2]['data'] = $request->currency." ".number_format($total['totalB40'],1);
                
                
                
                $dataArr[$key]['row'][1]['abr'] = 'P';
                $dataArr[$key]['row'][1]['title'] = 'Planned Budget';
                
                $dataArr[$key]['row'][1][0]['data']  = $request->currency." ".number_format($total['totalCUMH20'],1);
                $dataArr[$key]['row'][1][1]['data'] = $request->currency." ".number_format($total['totalCUMM20'],1);
                $dataArr[$key]['row'][1][2]['data'] = $request->currency." ".number_format($total['totalCUMT05'],1);
                
                
                
                $dataArr[$key]['row'][2]['abr'] = 'A';
                $dataArr[$key]['row'][2]['title'] = 'Actual Budget';
                
                $dataArr[$key]['row'][2][0]['data'] = $request->currency." ".number_format($total['totalCUMH25'],1);
                $dataArr[$key]['row'][2][1]['data'] = $request->currency." ".number_format($total['totalCUMM25'],1);
                $dataArr[$key]['row'][2][2]['data'] = $request->currency." ".number_format($total['totalCUMT10'],1);
              
                
                $dataArr[$key]['row'][3]['abr'] = 'D';
                $dataArr[$key]['row'][3]['title'] = 'Deviated Budget';
                
                $dataArr[$key]['row'][3][0]['data'] = $request->currency." ".number_format($total['totalCUMH35'],1);
                $dataArr[$key]['row'][3][0]['class'] = $total['totalCUMH35'] > 0 ? 'pos':'neg';
                $dataArr[$key]['row'][3][1]['data'] = $request->currency." ".number_format($total['totalCUMM35'],1);
                $dataArr[$key]['row'][3][1]['class'] = $total['totalCUMM35'] > 0 ? 'pos':'neg';
                $dataArr[$key]['row'][3][2]['data'] = $request->currency." ".number_format($total['totalCUMT01'],1);
                $dataArr[$key]['row'][3][2]['class'] = $total['totalCUMT01'] > 0 ? 'pos':'neg';
               
                
                
                 
            
                 
                 
                 
                
            }
            
            
            if($request->chart == '30')
            {
             
             
             
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';
                $dataArr[$key]['row']['header'][2] = 'Total';
                
                
                $dataArr[$key]['row'][0]['abr'] = 'P';
                $dataArr[$key]['row'][0]['title'] = 'Planned Budget';
                $dataArr[$key]['row'][0][0]['data']  = $request->currency." ".number_format($total['totalH20'],1);
                
                
                
                $dataArr[$key]['row'][1]['abr'] = 'A';
                $dataArr[$key]['row'][1]['title'] = 'Actual Budget';
                
                $dataArr[$key]['row'][1][0]['data'] = $request->currency." ".number_format($total['totalH25'],1);
               
                
                $dataArr[$key]['row'][2]['abr'] = 'D';
                $dataArr[$key]['row'][2]['title'] = 'Deviated Budget';
                
                $dataArr[$key]['row'][2][0]['data'] = $request->currency." ".number_format($total['totalH35'],1);
                $dataArr[$key]['row'][2][0]['class'] = $total['totalH35'] > 0 ? 'pos':'neg';
               
                
          
             $dataArr[$key]['row'][0][1]['data'] = $request->currency." ".number_format($total['totalM20'],1);
             $dataArr[$key]['row'][0][2]['data'] = $request->currency." ".number_format($total['totalT05'],1);
        
             $dataArr[$key]['row'][1][1]['data'] = $request->currency." ".number_format($total['totalM25'],1);
             $dataArr[$key]['row'][1][2]['data'] = $request->currency." ".number_format($total['totalT10'],1);
             
             $dataArr[$key]['row'][2][1]['data'] = $request->currency." ".number_format($total['totalM35'],1);
             $dataArr[$key]['row'][2][1]['class'] = $total['totalM35'] > 0 ? 'pos':'neg';
             $dataArr[$key]['row'][2][2]['data'] = $request->currency." ".number_format($total['totalT01'],1);
             $dataArr[$key]['row'][2][2]['class'] = $total['totalT01'] > 0 ? 'pos':'neg';
                
             
            }
            
            
            
             if($request->chart == '20')
            {
             
             
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';

                
                $dataArr[$key]['row'][0]['abr'] = 'B';
                $dataArr[$key]['row'][0]['title'] = 'Benchmark Budget';
                
                $dataArr[$key]['row'][0][0]['data']  = number_format($total['totalB45'],1);
                $dataArr[$key]['row'][0][1]['data'] =  number_format($total['totalB01'],1);
                
                
                $dataArr[$key]['row'][1]['abr'] = 'P';
                $dataArr[$key]['row'][1]['title'] = 'Planned Budget';
                
                $dataArr[$key]['row'][1][0]['data']  = number_format($total['totalCUMH15'],1);
                $dataArr[$key]['row'][1][1]['data'] = number_format($total['totalCUMM15'],1);
               
               
                
                
                $dataArr[$key]['row'][2]['abr'] = 'A';
                $dataArr[$key]['row'][2]['title'] = 'Actual Budget';
                
                $dataArr[$key]['row'][2][0]['data'] = number_format($total['totalCUMI10'],1);
                $dataArr[$key]['row'][2][1]['data'] = number_format($total['totalCUMI01'],1);
              
               
                
                $dataArr[$key]['row'][3]['abr'] = 'D';
                $dataArr[$key]['row'][3]['title'] = 'Deviated Budget';
                
                $dataArr[$key]['row'][3][0]['data'] = number_format($total['totalCUMH34'],1);
                $dataArr[$key]['row'][3][0]['class'] = $total['totalCUMH34'] > 0 ? 'pos':'neg';
                $dataArr[$key]['row'][3][1]['data'] = number_format($total['totalCUMM30'],1);
                $dataArr[$key]['row'][3][1]['class'] = $total['totalCUMM30'] > 0 ? 'pos':'neg';
              
    
                
                
                
                
             
            }
            
             if($request->chart == '10')
            {
                
                
                $dataArr[$key]['row']['header'][0] = 'Labor Hour';
                $dataArr[$key]['row']['header'][1] = 'Material';

             
                $dataArr[$key]['row'][0]['abr'] = 'P';
                $dataArr[$key]['row'][0]['title'] = 'Planned Budget';
                
                $dataArr[$key]['row'][0][0]['data']  = number_format($total['totalH15'],1);
                
               
                
                
                $dataArr[$key]['row'][1]['abr'] = 'A';
                $dataArr[$key]['row'][1]['title'] = 'Actual Budget';
                
                $dataArr[$key]['row'][1][0]['data'] = number_format($total['totalI10'],1);
                
               
                
                $dataArr[$key]['row'][2]['abr'] = 'D';
                $dataArr[$key]['row'][2]['title'] = 'Deviated Budget';
                
                $dataArr[$key]['row'][2][0]['data'] = number_format($total['totalH34'],1);
                $dataArr[$key]['row'][2][0]['class'] = $total['totalH34'] > 0 ? 'pos':'neg';
                
                

                $dataArr[$key]['row'][0][1]['data'] = number_format($total['totalM15'],1);
                $dataArr[$key]['row'][1][1]['data'] = number_format($total['totalI01'],1);
                $dataArr[$key]['row'][2][1]['data'] = number_format($total['totalM30'],1);
                $dataArr[$key]['row'][2][1]['class'] = $total['totalM30'] > 0 ? 'pos':'neg';
                
             
            }
        
        }
        
       
        return response(['data'=>$dataArr]);
        
    }


    public function getActivityAnalysis(Request $request)
    {

      $activityArray = array();


      $activityBenchmark = BenchmarkDetail::where('benchmark_id',$request->benchmark_id)
                                              ->where('project_division_id',$request->activity_id)
                                              ->where('area_id',$request->area_id)
                                              ->first();


      $labor_unit_cost = $activityBenchmark->unit_labor_hour *  $activityBenchmark->hours_unit;
      $quantity = $activityBenchmark->quantity;
      $TotalLaborHour =  self::calcTotalLaborHour($quantity,$activityBenchmark->hours_unit);
      $TotalMaterialCost =  self::calcTotalMaterialCost($quantity,$activityBenchmark->unit_material_rate);

      $laborUnitCost =  self::calcLaborUnitCost($activityBenchmark->unit_labor_hour,$activityBenchmark->hours_unit);

      $TotalLaborCost =  self::calcTotalLaborCost($quantity,$laborUnitCost);

        $TotalUnitRate =  self::calcTotalUnitRate($activityBenchmark->unit_labor_hour,$activityBenchmark->unit_material_rate);
        $TotalBudget = $TotalMaterialCost + $TotalLaborCost;

      $activityArray['benchmark'] =  $activityBenchmark;
      $activityArray['benchmark']['unit'] = 'm';

      $activityArray['benchmark']['laborUnitCost'] = number_format($laborUnitCost, 2);
      $activityArray['benchmark']['UnitRate'] =  number_format($activityBenchmark->unit_material_rate + $labor_unit_cost, 2);
      $activityArray['benchmark']['TotalBudget'] = number_format($TotalBudget, 2);

      $activityArray['benchmark']['TotalLaborCost'] = number_format($TotalLaborCost, 2);
      $activityArray['benchmark']['TotalMaterialCost'] =  number_format($TotalMaterialCost, 2);
      $activityArray['benchmark']['TotalUnitRate'] =  number_format($TotalUnitRate, 2);

      $activityArray['benchmark']['TotalLaborHour'] = number_format($TotalLaborHour);


      $activityForm = FormDetail::where('form_id',$request->form_id)
                                              ->where('division_id',$request->activity_id)
                                              ->where('area_id',$request->area_id)
                                              ->first();




       $activityArray['form'] =  $activityForm;

      // $gangHours = FdLabor::where('form_details_id',$activityForm->id)->sum('hours_of_work');
       $gangHours = $activityForm->working_hours;
      // $gangExtraHours = FdLabor::where('form_details_id',$activityForm->id)->sum('extra_hours_of_work');
       $gangExtraHours = $activityForm->extra_hours;

       $activityArray['form']['gangHours'] = number_format($gangHours);
       $activityArray['form']['gangExtraHours'] = number_format($gangExtraHours);

       $gangQty = $activityBenchmark->hours_unit * $activityForm->quantity;
       //$actualHourDeviation = $gangQty * $gangHours;
       $actualHourDeviation = $gangHours - $gangQty;
       $hourDeviationCost = $actualHourDeviation * $activityBenchmark->unit_labor_hour;
       //$percentDeviation = $hourDeviationCost * $gangQty;
       $percentDeviation = $actualHourDeviation / $gangQty;
       $trendDeviation = $TotalLaborCost * $percentDeviation;
       $trendHours = $TotalLaborCost + $trendDeviation;


       $activityArray['general']['gangQty'] = number_format($gangQty);
       $activityArray['general']['actualHourDeviation'] = number_format($actualHourDeviation);
       $activityArray['general']['hourDeviationCost'] = number_format($hourDeviationCost,2);
       $activityArray['general']['percentDeviation'] = number_format($percentDeviation * 100, 2);
       $activityArray['general']['trendDeviation'] = number_format($trendDeviation,2);
       $activityArray['general']['trendHours'] = number_format($trendHours,2);


       //$plannedQtyperActualGangHour = $gangHours * $activityBenchmark->hours_unit;
       $plannedQtyperActualGangHour = $gangHours / $activityBenchmark->hours_unit;
       $qtyDeviationCompToHour = $activityForm->quantity - $plannedQtyperActualGangHour;
       $percentDeviationSchedule = ($plannedQtyperActualGangHour != 0) ?$qtyDeviationCompToHour / $plannedQtyperActualGangHour : 0;
       $trendDeviationH = $percentDeviationSchedule * $TotalLaborHour;
       $trendHoursH = $TotalLaborHour - $trendDeviationH;

       $activityArray['general']['plannedQtyperActualGangHour'] = number_format($plannedQtyperActualGangHour);
       $activityArray['general']['qtyDeviationCompToHour'] = number_format($qtyDeviationCompToHour);
       $activityArray['general']['percentDeviationSchedule'] = number_format($percentDeviationSchedule * 100,2);
       $activityArray['general']['plannedQtyperActualGangHour'] = number_format($plannedQtyperActualGangHour);
       $activityArray['general']['trendDeviationH'] = number_format($trendDeviationH,2);
       $activityArray['general']['trendHoursH'] = number_format($trendHoursH,2);


       $plannedCompletedUnitsPerBOQ = $quantity * $activityForm->percentage_completed / 100;
       $unitsQtyDeviationCompToBOQ = $activityForm->quantity - $plannedCompletedUnitsPerBOQ;
       $actualUnitsDeviationCost = $activityBenchmark->unit_material_rate * $unitsQtyDeviationCompToBOQ;
       $percentDeviationUnits = $unitsQtyDeviationCompToBOQ / $plannedCompletedUnitsPerBOQ;
       $trendDeviationUnits = $percentDeviationUnits * $TotalMaterialCost;
       $trendQuantityUnits = $trendDeviationUnits + $TotalMaterialCost;

       $activityArray['general']['plannedCompletedUnitsPerBOQ'] = number_format($plannedCompletedUnitsPerBOQ);
       $activityArray['general']['unitsQtyDeviationCompToBOQ'] = number_format($unitsQtyDeviationCompToBOQ);
       $activityArray['general']['actualUnitsDeviationCost'] = number_format($actualUnitsDeviationCost,2);
       $activityArray['general']['percentDeviationUnits'] = number_format($percentDeviationUnits * 100, 2);
       $activityArray['general']['trendDeviationUnits'] = number_format($trendDeviationUnits,2);
       $activityArray['general']['trendQuantityUnits'] = number_format($trendQuantityUnits,2);

       $totalDeviation = $hourDeviationCost + $actualUnitsDeviationCost;
       $totalBudgetDeviated = $TotalBudget + $totalDeviation;
       $trendBudget = $trendQuantityUnits + $trendHours;
       $trendDeviationHU = $trendBudget - $TotalBudget;


       $activityArray['general']['totalDeviation'] = number_format($totalDeviation,2);
       $activityArray['general']['totalBudgetDeviated'] = number_format($totalBudgetDeviated,2);
       $activityArray['general']['trendBudget'] = number_format($trendBudget,2);
       $activityArray['general']['trendDeviationHU'] = number_format($trendDeviationHU,2);


       $percentUnitCompletedCalc = ($activityBenchmark->quantity <> 0) ? $activityForm->quantity / $activityBenchmark->quantity : 0 ;
       $percentGangCompletedCalc = ($TotalLaborHour <> 0) ? $gangHours / $TotalLaborHour : 0 ;

       $activityArray['general']['percentUnitCompletedCalc'] = number_format($percentUnitCompletedCalc * 100,2);
       $activityArray['general']['percentGangCompletedCalc'] = number_format($percentGangCompletedCalc * 100,2);



      return response(['activityData'=>$activityArray]);
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

          $this->array = DB::table($type)->where('project_id',$project_id)->get()->toArray();
   
     

  //    $dataTypeContent = DB::table($type)->where('project_id',$project_id)->whereNull('parent_id')->get();
       $dataTypeContent = Arr::where($this->array, function ($value, $key)  {
                     return $value->parent_id == null;
         });

      foreach ($dataTypeContent as $key => $area) {
        //$areaList = DB::table($type)->where('project_id',$area->project_id)->where('parent_id',$area->id)->get();
        $areaList = Arr::where($this->array, function ($value, $key) use($area) {
                    return $value->parent_id == $area->id;
        });

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
            if(FormDetail::where('form_details.'.($type == 'areas'?'area':'division').'_id',$item->id)->leftJoin('forms','forms.id','=','form_details.form_id')->where('forms.project_id',$item->project_id)->exists())
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
