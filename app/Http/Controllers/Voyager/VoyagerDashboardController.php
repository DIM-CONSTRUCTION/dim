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
use Session;
use App\Graph;
use Illuminate\Support\Arr;

class VoyagerDashboardController extends VoyagerBaseController
{
    use BreadRelationshipParser;
    var $array = array();



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


    function getDashboard(Request $request)
    {
        $benchmark = Benchmark::where('project_id',$request->project_id)->first();
       
        $currency = Project::where('projects.id',$request->project_id)->leftJoin('currencies','currencies.id','=','projects.currency_id')->select('currencies.*')->first()->symbol;
        $graph = new Graph($benchmark->id,null,null,null,null);
        $data = $graph->getLastDay();
        $budget = $data['total'];
      
        return response(['currency'=>$currency,'budget'=>$budget,'benchmark'=>$benchmark]);
        
    }
    function getDashboardData(Request $request)
    {
      $benchmark = Benchmark::where('project_id',$request->project_id)->first();
      $divisions = self::getDivision($benchmark,$request->project_id);
      return response(['divisions'=>$divisions]);
    }

    
    
    
     function getDivision($benchmark,$project_id)
    {

      $this->array = DB::table('project_divisions')->get()->toArray();
      $areas = Arr::where($this->array, function ($value, $key)  {
              return $value->parent_id == null;
      });

      $divisionsArray = array();
      //$areas = ProjectDivision::where('project_id',$project_id)->whereNull('parent_id')->get();
      foreach ($areas as $key => $area) {
          //$divisionList = ProjectDivision::where('project_id',$area->project_id)->where('parent_id',$area->id)->get();
          $divisionList = Arr::where($this->array, function ($value, $key) use($area) {
                  return $value->parent_id == $area->id;
          });
          $benchmarkArray = array();
          $benchmarkArray['title'] = $area->title;
          $benchmarkArray['id'] = $area->id;
          $benchmarkArray['isLast'] = false;

           $data = new Graph($benchmark->id,null,$area->id,null,null);
           
              $data = $data->getLastDay();
             $total = $data['total'];
             
            
            $benchmarkData['budget'] = $total['totalB57'];
            $benchmarkData['planned'] = $total['totalT08'];
            $benchmarkData['actual'] = $total['totalT21'];
            $benchmarkData['dev'] = $total['totalT22'];
            $benchmarkData['perc_dev'] = $total['totalT23'];
            $benchmarkData['f_c'] = $total['totalT42'];
            $benchmarkData['f_t'] = $total['totalT41'];

             
        
            
            $benchmarkArray['isFirst'] = true;
           
            
            $benchmarkArray['data'] = $benchmarkData;
        
          
          if(count($divisionList) == 0)
          {
              
          
            // $benchmark = BenchmarkDetail::where('benchmark_id',$dataTypeContent->id)->where('project_division_id',$project_division_id)->where('area_id',$area->id)->first();
            // $benchmarkArray['details'] = $benchmark;
            $divisionsArray[] = $benchmarkArray;
           
          }
          else{
       
            $benchmarkArray['class'] = 'treegrid-alfa'.$area->id;
            $divisionsArray[] = $benchmarkArray;
            
           //$divisionsArray = self::DivisionList($benchmark,$divisionsArray,$divisionList,$area->title);
          }
        // code...
      }

      return $divisionsArray;
    }

    function getChildDashboard(Request $request)
    {
      $areasName = '';
      $division_id = $request->division_id;
      $divisionsArray = $request->divisions;
      $benchmark = $request->benchmark;
      $title = $request->title;
      $insertKey =0;
      foreach($divisionsArray as $key=>$value){
        if($value['id'] == $division_id){
            $divisionsArray[$key]['isClicked'] = true;
            $insertKey = $key+1;
            break; // Stop the loop after we've found the item
        }
    }
      $subDivisionsArray = array();
      //  $areasArray = array();
      $list = ProjectDivision::where('project_id',$request->project_id)->where('parent_id',$division_id)->get();
        foreach ($list as $key => $item) {
            $divisionList = ProjectDivision::where('project_id',$item->project_id)->where('parent_id',$item->id)->get();
            // $divisionList = Arr::where($this->array, function ($value, $key) use($item) {
            //         return $value->parent_id == $item->id;
            // });
  
            $divisionTitle = $title.' &raquo; '.$item->title;
          $benchmarkArray = array();
          $benchmarkArray['title'] = $item->title;
          $benchmarkArray['id'] = $item->id;
          $benchmarkArray['isFirst'] = false;
          $benchmarkArray['class'] = 'treegrid-alfa'.$item->id.' treegrid-parent-alfa'.$item->parent_id;
  
            if(count($divisionList) == 0)
            {
                 $data = new Graph($benchmark['id'],null,
                 $item->id,null,null);
             
                $data = $data->getLastDay();
              // $benchmark = BenchmarkDetail::where('benchmark_id',$dataTypeContent->id)->where('project_division_id',$project_division_id)->where('area_id',$item->id)->first();
              // $benchmarkArray['details'] = $benchmark;
              $benchmarkArray['isLast'] = true;
              $total = $data['total'];
             
              $benchmarkData['budget'] = $total['totalB50'];
              $benchmarkData['planned'] = $total['totalT06'];
              $benchmarkData['actual'] = $total['totalT11'];
              $benchmarkData['dev'] = $total['totalT18'];
              $benchmarkData['perc_dev'] = $total['totalT19'];
              $benchmarkData['f_c'] = $total['totalT37'];
              $benchmarkData['f_t'] = $total['totalT36'];
                  
           
          
             
              
              $benchmarkArray['data'] = $benchmarkData;
              $subDivisionsArray[] = $benchmarkArray;
            }
            else{
              $benchmarkArray['isLast'] = false;
              $subDivisionsArray[] = $benchmarkArray;
             // $divisionsArray = self::DivisionList($benchmark,$divisionsArray,$divisionList,$divisionTitle);
            }
  
  
          }

          array_splice( $divisionsArray,$insertKey, 0, $subDivisionsArray );
  
  
          return $divisionsArray;
    }
    function DivisionList($benchmark,$divisionsArray,$list,$title)
    {
      $areasName = '';
    //  $areasArray = array();
      foreach ($list as $key => $item) {
      //    $divisionList = ProjectDivision::where('project_id',$item->project_id)->where('parent_id',$item->id)->get();
          $divisionList = Arr::where($this->array, function ($value, $key) use($item) {
                  return $value->parent_id == $item->id;
          });

          $divisionTitle = $title.' &raquo; '.$item->title;
        $benchmarkArray = array();
        $benchmarkArray['title'] = $item->title;
        $benchmarkArray['id'] = $item->id;
        $benchmarkArray['isFirst'] = false;
        $benchmarkArray['class'] = 'treegrid-alfa'.$item->id.' treegrid-parent-alfa'.$item->parent_id;

          if(count($divisionList) == 0)
          {
               $data = new Graph($benchmark->id,null,$item->id,null,null);
           
              $data = $data->getLastDay();
            // $benchmark = BenchmarkDetail::where('benchmark_id',$dataTypeContent->id)->where('project_division_id',$project_division_id)->where('area_id',$item->id)->first();
            // $benchmarkArray['details'] = $benchmark;
            $benchmarkArray['isLast'] = true;
            $total = $data['total'];
           
            $benchmarkData['budget'] = $total['totalB50'];
            $benchmarkData['planned'] = $total['totalT06'];
            $benchmarkData['actual'] = $total['totalT11'];
            $benchmarkData['dev'] = $total['totalT18'];
            $benchmarkData['perc_dev'] = $total['totalT19'];
            $benchmarkData['f_c'] = $total['totalT37'];
            $benchmarkData['f_t'] = $total['totalT36'];
                
         
        
           
            
            $benchmarkArray['data'] = $benchmarkData;
            $divisionsArray[] = $benchmarkArray;
          }
          else{
            $benchmarkArray['isLast'] = false;
            $divisionsArray[] = $benchmarkArray;
            $divisionsArray = self::DivisionList($benchmark,$divisionsArray,$divisionList,$divisionTitle);
          }


        }


        return $divisionsArray;
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

       $benchmark = Benchmark::where('project_id',$request->project_id)->first();   
      $graph = new Graph($benchmark->id,null,null,null,$request->chart['parent']);


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

        $title = 'Project Budget Labor';
      }

      

      $activities = $myresult['details'];
      
      
      //return $activities;
      //echo $activities;
      $optionsArray = array();
      
       
      $chartArray = array_filter($chartArray, function ($chart){ return $chart->is_displayed; }); 
      $chartArray=  array_values( $chartArray );
      

      $optionsArray = self::getChartData($activities,$chartArray,$title);
      
    





      return response(['chart_activity'=>$optionsArray,'tableArray'=>$tableArray]);
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
                $areaArray['title'] = $area->reference." ".$area->title;
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
                  $areaName = $area->reference." ".$area->title;
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
                $areaArray['title'] = $item->reference." ".$item->title;
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
                $areaName = $name.' » '.$item->reference." ".$item->title;
            }

            $areasArray = self::functionList($areasArray,$areaList,$areaName,$type,$id);
          }
        }
        return $areasArray;
    }




   
}
