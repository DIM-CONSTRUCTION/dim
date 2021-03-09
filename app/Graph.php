<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FormDetail;
use App\BenchmarkDetail;
use App\Benchmark;
use App\Form;
use App\Area;
use App\ProjectDivision;
use App\Project;

class Graph
{


  public $cumulativeQuantity;
  public $cumulativePercentage;
  public $cumulativeWorkingHours;

  public $formsArray;
  public $forms;
  public $date;
  public $benchmarkId;
  public $formId;
  public $activityId;
  public $activitiesId;
  public $areaId;
  public $is_percentage;
  public $areas;
  public $use_percentage;


  public $activityArray = array();
  public $laborActivityArray = array();
  public $quantityActivityArray = array();
  public $scheduleActivityArray = array();
  public $cumulativeArray = array();
  public $CUMulativeArray = array();
  

  public function __construct($benchmarkId,$date,$activityId,$areaId,$parent)
  {
    $this->project_id = Benchmark::find($benchmarkId)->project_id;
    
    if(strpos($date, " - ") !== false)
    {
      $dateArr = explode(" - ",$date);
      $fromDate = $dateArr[0];
      $toDate = $dateArr[1];
      $this->forms = Form::where('project_id',$this->project_id)->whereBetween('date', [$fromDate, $toDate])->orderBy('date','desc')->get();
    }
    else{
     $this->forms = Form::where('project_id',$this->project_id)->orderBy('date','desc')->get();
    }

    $this->use_percentage = Project::find($this->project_id)->use_percentage;
    



    $this->benchmarkId = $benchmarkId;
    $this->areas = Area::where('project_id',$this->project_id)->whereNotNull('parent_id')->pluck('parent_id')->toArray();
    $this->areas = array_unique( $this->areas );
    

    $cumulativeQuantity = 0; //values
    $cumulativePercentage = 0; //values
    $cumulativeWorkingHours = 0; //values

    
    $cumulativeActivityArray = array();

        
        
        
        
        $totalActivityPlannedBugdet = 0;
        
        $totalActivityActualBudgetDeviated = 0 ;
        $totalActivityForecastBudgetTrend = 0 ;

        $totalForecastedTotalLaborCost = 0;
        $totalActualLaborCost = 0;
        $totalPlannedLaborCostPerActualMaterial = 0;
        $totalPlannedTotalLaborCost = 0;



      
        $totalActualQuantityMaterial=0;
        $totalPlannedTotalMaterialCost = 0;
        $forecastedTotalLaborCost = 0;
        $totalForecastedPercentageBudgetDeviationTrend = 0;
        $B01 = 0;
        $B10 = 0;
        $B40 = 0;
        $B45 = 0;
        $B47 = 0;
        $B49 = 0;
        $B50 = 0;
        $B52 = 0;
        $B53 = 0;
        $B54 = 0;
        $B55 = 0;
        $B56 = 0;
        $B57 = 0;
        $B30 = 0;
        $T01 = 0;
        $T07 = 0;
        $T08 = 0;
        $T11 = 0;
        $T12 = 0;
        $T13 = 0;
        $T14 = 0;
        $T16 = 0;
        $T17 = 0;
        $T18 = 0;
        $T19 = 0;
        $T30 = 0;
        $T35 = 0;
        $T36 = 0;
        $T37 = 0;
        $T38 = 0;
        $T39 = 0;
        $T40 = 0;
        $T21 = 0;
        $T22 = 0;
        $T23 = 0;
        $T25 = 0;
        $totalT15 = 0;
        $T10 = 0;
        $T41 = 0;
        $T42 = 0;
        $M15 = 0;
        $M21 = 0;
        $M26 = 0;
        $M30 = 0;
        $M35 = 0;
        $M46 = 0;
        $M60 = 0;
        $M55 = 0;
        $totalM40 = 0;
        $M41 = 0;
        $M42 = 0;
        $H15 = 0;
        $H30 = 0;
        $H34 = 0;
        $totalH40 = 0;
        $H41 = 0;
        $H42 = 0;
        $H55 = 0;
        $H56 = 0;
        $H25 = 0;
        $T05 = 0;
        $T06 = 0;
        $M20 = 0;
        $M25 = 0;
        $M65 = 0;
        $M66 = 0;
        $M67 = 0;
        $M68 = 0;
        $M69 = 0;
        $H20 = 0;
        $H21 = 0;
        $H26 = 0;
        $H35 = 0;
        $H46 = 0;
        $H60 = 0;
        $H65 = 0;
        $H66 = 0;
        $H67 = 0;
        $H68 = 0;
        $H69 = 0;
        $I10 = 0;
        $I17 = 0;
        $I18 = 0;
        $I33 = 0;
        $I35 = 0;
        $I36 = 0;
        $I40 = 0;
        $I41 = 0;
        $I42 = 0;
        $I44 = 0;
        $I46 = 0;
        $I66 = 0;
        $I01 = 0;
        $TI06 = 0;
        $TI07 = 0;
        $TI08 = 0;
        $TI11 = 0;
        $TI17 = 0;
        $TI18 = 0;
        $TI19 = 0;
        $TI21 = 0;
        $TI22 = 0;
        $TI23 = 0;
        $TI24 = 0;
        $TI25 = 0;
        $TI35 = 0;
        $TI36 = 0;
        $TI37 = 0;
        $TI41 = 0;
        $TI42 = 0;

        $totalUnitMaterialRate = 0;
        $totalLaborRate = 0;
        $totalForecastedProjectBudgetTrend = 0;
        $totalForecastedProjectBudgetMaterialTrend = 0;
        $totalForecastedProjectBudgetLaborTrend = 0;
        
        $totalForecastedProjectBudgetAreaTrend = 0;
        $totalForecastedProjectBudgetMaterialAreaTrend = 0;
        $totalForecastedProjectBudgetLaborAreaTrend = 0;
        
        $CUMH15 = 0;
        $CUMH20 = 0;
        $CUMH25 = 0;
        $CUMH30 = 0;
        $CUMH34 = 0;
        $CUMH35 = 0;
        $CUMM15 = 0;
        $CUMM20 = 0;
        $CUMM25 = 0;
        $CUMM30 = 0;
        $CUMM35 = 0;
        $CUMI01 = 0;
        $CUMI10 = 0;
        $CUMT01 = 0;
        $CUMT05 = 0;
        $CUMT10 = 0;
        
        if(ProjectDivision::where('id',$activityId)->whereNull('parent_id')->exists())
        {
            $activitiesId =  ProjectDivision::where('parent_id',$activityId)->pluck('id')->toArray();
            $activitiesId =  self::getParentIds($activitiesId);
            
            $activityId  = $activitiesId;
           
        }
      
        $totalBenchamrkCost = self::calcTotalBenchmarkCost($this->benchmarkId,$activityId,$areaId);
                 
       
        
        
    foreach ($this->forms as $keyForm => $form) {

        //actual
        $activities = FormDetail::where('form_id',$form->id);
        
        
        if(isset($activitiesId)){
            $activities->whereIn('division_id',$activitiesId);
           
        }
        else{
            if(isset($activityId)){
                $activities->where('division_id',$activityId);
            }
        }
     
             if(isset($areaId)){
                  $activities->where('area_id',$areaId);
              }
        
       
        
        $activities = $activities->orderBy('division_id')->orderBy('area_id')->get();
        
        



        foreach ($activities as $key => $activity) {
            
            
                $parentId = self::getDivParentId($activity->division_id);
                
                $activitiesBoqId =  ProjectDivision::where('parent_id',$parentId)->pluck('id')->toArray();
                $activitiesBoqId =  self::getParentIds($activitiesBoqId);
                $benchamrkCost = self::calcTotalBenchmarkCost($this->benchmarkId,$activitiesBoqId,$areaId);
        
                
                $activeAreas = FormDetail::where('form_id',$form->id)->where('division_id', $activity->division_id)->pluck('area_id')->toArray();
            
                $benchmarkArr = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)->where('project_division_id', $activity->division_id);
                
                
                // if(isset($areaId)){
                //     $benchmarkArr->where('area_id',$areaId);
                // }
                // else{
                //     $benchmarkArr->where('area_id',$activity->area_id);
                // }
                
                  $benchmarkArr->where('area_id',$activity->area_id);
                
                $benchmarkArr = $benchmarkArr->first();
                $division = ProjectDivision::where('id',$activity->division_id)->first();


                
                if(isset($benchmarkArr))
                {
                    //BOQ Planned
                    
                   
                      $B01 = $benchmarkArr->quantity;
                    
                
                    
                    if(isset($areaId)){
                      $B48 = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)->where('project_division_id', $activity->division_id)->where('area_id',$areaId)->sum('quantity');
                    }
                    else{
                      $B48 = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)->where('project_division_id', $activity->division_id)->leftJoin('areas','areas.id','=','benchmark_details.area_id')
                                 ->whereNotNull('areas.id')->whereNotIn('areas.id',$this->areas)->sum('quantity');
                    }
                    
                    $B02 = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)->where('project_division_id', $activity->division_id)->whereIn('area_id',$activeAreas)->sum('quantity');
                    

                    
                    $unit_material_rate = 0; 
                    if(isset($benchmarkArr->unit_material_rate)) {$unit_material_rate = $benchmarkArr->unit_material_rate;}  
                    
                    $B05 = $unit_material_rate; 
                    
                    
                     
                    $B10 = $B01 * $B05; 
                    $AAB10 = $B02 * $B05;
                    
                    
                    $plannedTotalMaterialCost = $B48 * $B05;
                    
                    $hours_unit = 0;
                    if(isset($benchmarkArr->hours_unit)) { $hours_unit = $benchmarkArr->hours_unit;}
                    $B15 = $hours_unit; //rename $B15_B15 
                    
                    $B20 = $benchmarkArr->unit_labor_hour; 
                    
                    $plannedLaborCost = $B15 * $B20; //rename $plannedLaborUnitCost_B25
                    $B30 = $B01 * $plannedLaborCost; 
                    
                    
                  
                    $plannedTotalLaborCost = $B48 * $plannedLaborCost;
                    
                    $plannedUnitRate = $plannedLaborCost + $B05; //rename $plannedUnitRate_B35
                    
                    $B40 = $B10 + $B30; 
                    
                    
                    $B45 = $B15 * $B01; 
                  
                    
                    $benchmarkPerActivity = self::calcBenchmarkPerActivity($this->benchmarkId,$activity->division_id);     
                    $B46 = $benchmarkPerActivity['totalB46']; 
            
                    $B47 = $B46 * $B20;
                    
                    
                     
                    $B49 = $B48 * $B05 ;
                    $B50 = $B47 + $B49; 
                    $B51 = $totalBenchamrkCost['labor_hour'];
                    $B52 = $totalBenchamrkCost['labor'];
                    $B53 = $totalBenchamrkCost['material'] ;
                    $B54 = $totalBenchamrkCost['total'] ;
                    $B55 = $benchamrkCost['labor'];
                    $B56 = $benchamrkCost['material'] ;
                    $B57 = $benchamrkCost['total'] ;
                    
                    
                    
                 
                    
                    //Actual Executed Work / User Input Data
                    $quantity = 0; //rename $actualQuantityMaterial_I01
                    if(isset($activity->quantity)) {$quantity = $activity->quantity;}
                    
                    $I05 = 0; //rename $actualActivityPerentageCompleted_bI05
                    if(isset($activity->percentage_completed)) {$I05 = $activity->percentage_completed / 100;}
                    
                    $I10 = 0; 
                    if(isset($activity->working_hours)) {$I10 = $activity->working_hours;}
                    
                    $actualLaborOvertime = 0; //rename $actualLaborOvertime_I15
                    if(isset($activity->extra_hours)) {$actualLaborOvertime = $activity->extra_hours;}
                    
                    //Cumulative Actual Executed Work / User Input Data
                    $lastQty = ($keyForm!=0 && isset($this->cumulativeArray[$keyForm - 1][$key]['quantity']))? $this->cumulativeArray[$keyForm - 1][$key]['quantity'] :0;
                    $lastPerc = ($keyForm!=0  && isset($this->cumulativeArray[$keyForm - 1][$key]['percentage_completed']))? $this->cumulativeArray[$keyForm - 1][$key]['percentage_completed'] :0;
                    $lastWorkingHours = ($keyForm!=0  && isset($this->cumulativeArray[$keyForm - 1][$key]['working_hours']))? $this->cumulativeArray[$keyForm - 1][$key]['working_hours'] :0;
                    $lastLaborOvertime = ($keyForm!=0  && isset($this->cumulativeArray[$keyForm - 1][$key]['extra_hours']))? $this->cumulativeArray[$keyForm - 1][$key]['extra_hours'] :0;

                    $this->cumulativeArray[$keyForm][$key]['activity'] = $benchmarkArr->id;
                    $this->cumulativeArray[$keyForm][$key]['quantity'] = $lastQty + $quantity;
                    $this->cumulativeArray[$keyForm][$key]['percentage_completed'] =  $lastPerc  + $activity->percentage_completed / 100;
                    $this->cumulativeArray[$keyForm][$key]['working_hours'] =  $lastWorkingHours + $activity->working_hours;
                    $this->cumulativeArray[$keyForm][$key]['extra_hours'] =  $lastLaborOvertime + $activity->extra_hours;

                    $cumulativeQuantity =  $this->cumulativeArray[$keyForm][$key]['quantity'];
                    $cumulativePercentage = $this->cumulativeArray[$keyForm][$key]['percentage_completed'];
                    $cumulativeWorkingHours = $this->cumulativeArray[$keyForm][$key]['working_hours'];
                    $cumulativeLaborOvertime = $this->cumulativeArray[$keyForm][$key]['extra_hours'];

                    $actualQuantityPerUnit = $quantity;

                    
                    self::getCumulative(true,$activity,$keyForm,$I10,'I10'); 
                    $CUMI10 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['I10'][$activity->area_id];
                    
                   
                  
                    
                    
                    
           
                   

                    //Material Calculations
                    $I01 = $quantity;
                    
                    // $lastAAI01 = ($keyForm!=0 && isset($this->cumulativeArray[$keyForm - 1][$key]['AAI01']))? $this->cumulativeArray[$keyForm - 1][$key]['AAI01'] :0;
                    // $this->cumulativeArray[$keyForm][$key]['AAI01'] = $lastAAI01 + $quantity;
                    // $CUMI01 = $this->cumulativeArray[$keyForm][$key]['AAI01'];
                    
                    self::getCumulative(true,$activity,$keyForm,$I01,'I01'); 
                    $CUMI01 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['I01'][$activity->area_id];
                        
                        
                    $CUAAI01 = FormDetail::where('form_id',$form->id)->where('division_id', $activity->division_id)->sum('quantity');
                    
                    
     
                    
                    $this->is_percentage = ($this->use_percentage) ? $division->is_percentage : 0;
                    

                        
                        $M100 = $I01 / $B01;
                      
                        
                        //Newly Added
                        $actualPercentageCompletedMaterial = ($B01 != 0)? $CUMI01/$B01 : 0; //to rename $actualPercentageCompletedMaterial_M01
                        $M05 = 1 - $actualPercentageCompletedMaterial; //to rename $actualRemainingPercentageMaterial_M05
                        $M10 = $M05 * $B01; //to rename $actualRemainingMaterialQuantity_M10
                        $M11 = $M05 * $B48;//ActualremaningTotalMaterialQuantity
                        
                         if(!$this->is_percentage)
                        {
                            $M15 = $B01 * $M100; 
                            
                        }
                        else{
                            $M15 = $B01 * $I05; 
                           
                        }
                        
                        
                        
                        
                        
                        $AAM15 = $B02 * $I05;
                        
                    
                        
                        self::getCumulative(true,$activity,$keyForm,$M15,'M15'); 
                        $CUMM15 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['M15'][$activity->area_id];
                        
                       
                        
                        $M16 = $B01 - $CUMM15;
                        
                        $AAM20 = $AAM15 * $B05;
      
                        $M20 = $M15 * $B05; //to rename $plannedCostMaterial_M20
                         
                        self::getCumulative(true,$activity,$keyForm,$M20,'M20'); 
                        $CUMM20 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['M20'][$activity->area_id];
                        
                       
                        $M21 = self::getCumulative(true,$activity,$keyForm,$M20,'M20');
                        
                        $M22 =  $B49 - $M21;
                       
                        
                        
                        $M25 =  $B05 * $actualQuantityPerUnit; //to rename $actualCostMaterial_M25
                        
                        self::getCumulative(true,$activity,$keyForm,$M25,'M25');
                        $CUMM25 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['M25'][$activity->area_id];
                   
                        
                        
                        $CUMM29 = $CUMI01 - $B01;
                        $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['CUMM29'][$activity->area_id] = $CUMM29;
                
                           
                        
                         
                        
                        $M29 = ($keyForm > 0 && isset($this->cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['CUMM29'][$activity->area_id])) ? $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['CUMM29'][$activity->area_id] - $this->cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['CUMM29'][$activity->area_id] : $CUMM29 ;
                          
                            
                        if(!$this->is_percentage && $CUMM29 > 0 )
                        {
                            
                            $M30 = ($I01 -$M15) + $M29;
                            
                            
                           
                        }
                        else{
                            $M30 = $I01 - $M15;
                        }
                      //  
                      
                     //  var_dump($keyForm." ".$activity->division_id." ".$I01." ".$M15." ".$M29);
                         
                        
                        $AAM30 = $CUAAI01 - $AAM15;
                        $AAM35 = $AAM30 * $B05;
                        
                        $M35 = $M30 * $B05; 
                        
                       
             
                        
                         self::getCumulative(true,$activity,$keyForm,$M35,'M35'); 
                        $CUMM35 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['M35'][$activity->area_id];
                        
                        
                      
                        
                        $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['M40'][$activity->area_id] = $CUMM35 + $B10;
                        
                        
                        $AAM40 = array_sum($this->cumulativeArray[$keyForm]['div'.$activity->division_id]['M40']);
                        
                        
                        $M41 = $AAM40;
                        $M42 = $AAB10;
                        
                        
                        self::getCumulative(true,$activity,$keyForm,$M30,'M30'); 
                        $CUMM30 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['M30'][$activity->area_id];
                        
                       

                        
                        
                        $M40 = $M35 + $B10; //to rename $actualTotalMaterialCost_M40
                        $M45 = ($M15 != 0)? $M30 / $M15 : 0 ; //to rename $actualPercentageMaterialDeviation_M45
                        $M46 = array_sum($this->cumulativeArray[$keyForm]['div'.$activity->division_id]['M35']);
                        $M47 = ($M21 != 0)? $M46 / $M21 : 0 ;
                        
                        $M48 = $M16 * (1+$M45);
                        
                        if(!$this->is_percentage && $CUMM29 > 0 )
                        {
                            $M49 = $CUMI01;
                        }
                        else{
                            $M49 = $M48 + $CUMI01;
                        }
                        
                         
                        //added by souheil
                        $M50 = $M49 - $B01; //rename $forecastedDeviationMaterialQuantity_M50
                        // $M51 = ($M11 * $M45) + $CUM30;//ForecastedDeviationTotalMaterialQuantity
                       
                        
                        $M60 = $M50 * $B05; //rename $forecastedDeviationMaterialCost_M60
                        $M55 = ($B10 != 0)? $M60 / $B10 :0; 
                        // $M61 = $B05 * $M51;//forecastedDeviationTotalMaterialCost
                        $M65 = $M49 * $B05; 
                        
                       
                        $M70 = $M22 * (1+$M47);
                        
                        $M26 = self::getCumulative(true,$activity,$keyForm,$M25,'M25');
                        
                        $M66 = $M70 + $M26;
                         
                        $M67 = $M66 - $B49;
                        
                        
                        
                        
                        $M90 = self::getParentCumulative($parentId,$activity,$keyForm,$M67,'M67');
                        $M95 = $M90 + $B56;
                        
                        $M71 = self::getParentCumulative($parentId,$activity,$keyForm,$M21,'M21');
                        
                        $M23 =  self::getTotalCumulative($parentId,$keyForm,$M71,'M71');
                        
                        
                        
                        $M69 = self::getTotalCumulative($parentId,$keyForm,$M90,'M90');
                        //$M68 = self::getCumulative(true,$activity,$keyForm,$M95,'M95');
                        $M68 = self::getTotalCumulative($parentId,$keyForm,$M95,'M95');
                        
                        $M75 = self::getParentCumulative($parentId,$activity,$keyForm,$M26,'M26');
                        $M27 = self::getTotalCumulative($parentId,$keyForm,$M75,'M75');
                        
                        $M105 = $M26 / $B49;
                        $M110 = $M75 / $B56 ;
                        
                
                      
                                
                              //Hours Calculations

                            $H01 = ($B45 != 0)? $cumulativeWorkingHours/$B45 :0; //rename $actualPercentageCompletedLaborHours_H01
                            $H05 = 1 - $H01; //rename $actualRemainingPercentageLaborHours_H05
                            $H10 = $H05 * $B45; //rename $H10
                            
                        
                            $H15 = $actualQuantityPerUnit * $B15; //to rename $plannedLaborHoursPerActualMaterial_H15
                            
                         
                            
                            self::getCumulative(true,$activity,$keyForm,$H15,'H15'); 
                            $CUMH15 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['H15'][$activity->area_id];
                        
                            
                            
                            
                            
                            
                            $H16 = $B45 - $CUMH15;
                            
                            //added by souheil
                            $H20 = $H15 * $B20; //to rename $plannedLaborCostPerActualMaterial_H20
                            
                            $H21 = self::getCumulative(true,$activity,$keyForm,$H20,'H20');
                            
                            $CUMH20 = $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['H20'][$activity->area_id];
                            
                            $H22 = $B47 - $H21;
                         
                            $H71 = self::getParentCumulative($parentId,$activity,$keyForm,$H21,'H21');
                            
                           
                            $H25 = $B20 *  $I10; //to be renamed $actualLaborCost_H25
                            
                            
                         
                            $H26 = self::getCumulative(true,$activity,$keyForm,$H25,'H25');
                            
                           
                            
                            
                            
                
                            $H75 = self::getParentCumulative($parentId,$activity,$keyForm,$H26,'H26');
                             
                            $H30 = $I10 - $H15; //to be renamed $I10Deviation_H30
                   
                            
                            self::getCumulative(true,$activity,$keyForm,$H30,'H30'); 
                            $CUH30 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['H30'][$activity->area_id];
                        
                            
                            $H31 = $H30 * $B20;
                           
                            $H32 = $B15 * $M30;
                            $H33= $H32 * $B20;
                            
                             
                            $H34 = $H30 + $H32;
                            $H35 = $H34 * $B20;
                         
                          
                            $CUMH34 = self::getCumulative(true,$activity,$keyForm,$H34,'H34');
                         
                           
                            
                            self::getCumulative(true,$activity,$keyForm,$H35,'H35'); 
                            $CUMH35 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['H35'][$activity->area_id];
                            
                   
                            
                          // var_dump($CUMH35);
                            
                            $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['H40'][$activity->area_id] = $CUMH35 + $B30;
                             
                            //added by souheil
                            $H40 = $H35 + $B30; //to be renamed $actualTotalLaborCost_H40
                            
                            $H42 = $B02 * $plannedLaborCost;
                            
        
                            $H41 = array_sum($this->cumulativeArray[$keyForm]['div'.$activity->division_id]['H40']);
                               
        
                            
                            $H45 = ($H15 != 0)? $H30 / $H15 : 0; //to rename $actualPercentageLaborHourDeviation_H45
                        
                            
                            $H46 = self::getCumulative(true,$activity,$keyForm,$H31,'H31');
                            
                            
                           
                            
                            $H48 = $H16*(1+$H45);
                            
                            $H51 = $M50 *$B15;
                            $H52 = $H51 * (1+ $H45);
                            $H53 = $H52 + $H48;
                            
                            $H49 = $H53 + $CUMI10;
                            
                            $H50 = $H49 - $B45;
                           
                            
                            $H60 = $H50 * $B20; //to rename $forecastedDeviationLaborHoursCost_H60
                            
                          
                            $H54 = self::getCumulative(true,$activity,$keyForm,$H33,'H33');
                            $H56 = $H54 + $H46;
                            
                            $H47 = ($H21 != 0) ? $H56/$H21 : 0;
                           
                            $H55 = ($B30 != 0)? $H60 / $B30 : 0; 
                            
                            $H70 = $H22 * (1 + $H47);
             
                            $H65 = $H49 * $B20;
                           // var_dump($keyForm." ".$M49);
                            $H66 = $H54 + $H70 + $H26; 
                            $H67 = $H66 - $B47;
                           
                            $H80 = $H75 - $H70;
                            $H85 = ($H70 != 0)? $H80 / $H70 :0; 
                            
                            $H90 = self::getParentCumulative($parentId,$activity,$keyForm,$H67,'H67');
                            $H95 = $H90 + $B55;
                            
                            
                             
                            $H100 = $I10 / $B45;
                            $H105 = $H26 / $B47;
                            $H110 = $H75 / $B55;
                            
                            $cumulativeActivityArray['H71'][$keyForm]['div'.$activity->division_id] = $H71;
                            $cumulativeActivityArray['H75'][$keyForm]['div'.$activity->division_id] = $H75;
                            $cumulativeActivityArray['H90'][$keyForm]['div'.$activity->division_id] = $H90;
                            $cumulativeActivityArray['H95'][$keyForm]['div'.$activity->division_id] = $H95;
                            
                            $H23 = self::getTotalCumulative($parentId,$keyForm,$H71,'H71');
                            $H27 = self::getTotalCumulative($parentId,$keyForm,$H75,'H75');
                            $H69 = self::getTotalCumulative($parentId,$keyForm,$H90,'H90');
                            $H68 = self::getTotalCumulative($parentId,$keyForm,$H95,'H95');
                       
                       
                           
                        
            //Total Hours & Material
                        $T01 = $M35 + $H35; //rename $actualTotalDeviation_T01
       
                        
                        self::getCumulative(true,$activity,$keyForm,$T01,'T01'); 
                        $CUMT01 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['T01'][$activity->area_id];
                        
                        $T05 = $M20 + $H20; //rename $plannedProjectBudgetVsExecutedActivities_T05
                        
                        self::getCumulative(true,$activity,$keyForm,$T05,'T05');
                        $CUMT05  =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['T05'][$activity->area_id];
                        
                        $T06 = $M21 + $H21;
                   
                         
                        $T08 = self::getParentCumulative($parentId,$activity,$keyForm,$T06,'T06');
                       
                        
                        $T10 = $M25 + $H25; //rename $actualCostOfExecutedActivities_T10
                        
                        self::getCumulative(true,$activity,$keyForm,$T10,'T10');
                        $CUMT10  =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['T10'][$activity->area_id];
                        
                        $T11 = $M26 + $H26;
    
                        $T21 = self::getParentCumulative($parentId,$activity,$keyForm,$T11,'T11');
                        
                        $T22 = $T21 - $T08;
                        $T23 = ($T08 != 0)? $T22/$T08 : 0;
                        
                        $T15 = $M40 + $H40; //rename $actualBudgetDeviated_T15
                        $T16 = $M41 + $H41;
                        $T17 = $M42 + $H42;
                        $T18 = $T16 - $T17;
                        $T19 = ($T06 != 0)? $T18 / $T06 :0;
                        $T20 = ($B40 != 0)? $T01 / $B40 :0; //rename $actualPercentageBudgetDeviated_T20
                        $T25 = $M60 + $H60; //rename $forecastedDeviation_T25
                        $T30 = ($B40 != 0)? $T25 / $B40 :0; //$forecastedPercentageBudgetDeviationTrend_T30
                        $T35 = $M65 + $H65; //rename $forecastedBudgetTrend_T35
                        $T36 = $M66 + $H66; //forecastedProjectBudgetTrendPerActivity
                        
                        $T37 = $M67 + $H67;
                     
                        
                        $totalCost = $plannedTotalMaterialCost+$plannedTotalLaborCost;
                        
                        $T41 = self::getParentCumulative($parentId,$activity,$keyForm,$T37,'T37');
    
                        $totalActivityPlannedBugdet = $benchamrkCost['total'];
                        $totalPlannedTotalMaterialCost = $benchamrkCost['material'];
                        $totalPlannedTotalLaborCost = $benchamrkCost['labor'];
    
                        $totalActivityForecastBudgetTrend +=  $T35 * $B20;
    

                        $totalActualQuantityMaterial += $actualQuantityPerUnit;
                     
             
    
                       
                        $T42 = $T41 + $B57;
    
                       
                     
                        $T30 = ($B40 != 0 ) ? $T25/ $B40 : 0;
                        
                    
                        
                        $totalT15 += $T15;
                        
      
                        $M55 = ($B10 != 0)? $M60 / $B10 :0;

                  
                        
                  
                        $totalM40 += $M40;
                        $M41 = $M41;
                        $M42 = $M42;
                        
                      
                        $totalH40 += $H40;
                        $H41 = $H41;
                        $H42 = $H42;
                        $H46 = $H46;
                        $H60 = $H60;
                        
                       
                        $H55 = ($B30 != 0) ? $H60 / $B30 : 0;
                      
                        $H20 =  $H15 * $B20;
                        
                   
                        
                        self::getCumulative(true,$activity,$keyForm,$H25,'H25'); 
                        $CUMH25 =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['H25'][$activity->area_id];
                       
                      
                       
                        
                       
                        
                        $totalForecastedProjectBudgetTrend = (1+$T30) * $totalActivityPlannedBugdet;
                        $totalForecastedProjectBudgetMaterialTrend = (1+$M55) * $totalPlannedTotalMaterialCost;
                        $totalForecastedProjectBudgetLaborTrend = (1+$H55) * $totalPlannedTotalLaborCost;
                        
                        $totalForecastedProjectBudgetAreaTrend +=  (1+$T30) * $T05;
                        $totalForecastedProjectBudgetMaterialAreaTrend += (1+$M55) * $M20;
                        $totalForecastedProjectBudgetLaborAreaTrend += (1+$H55) * $H20;
                    
                    
                        $T07 = $M23 + $H23;
                        
                        
                        $T12 = $M27 + $H27;
                        
                        
                        $T13 = $T12 - $T07;
                        $T14 = ($T07 != 0)? $T13 / $T07 :0;
                    
                        
                       
                        $T38 = $H68 + $M68;
  
                        $T39 = $H69 + $M69;
                        
                       //  var_dump($activity->division_id." ".$keyForm." ".$activity->area_id." ".$M68);
                        
                        $T40 = ($B54 != 0)? $T39 / $B54 :0;
                        

                }

            }

            $this->activityArray[$keyForm]['total']['totalForecastedProjectBudgetTrend'] = $totalForecastedProjectBudgetTrend;
            $this->activityArray[$keyForm]['total']['totalForecastedProjectBudgetMaterialTrend'] = $totalForecastedProjectBudgetMaterialTrend;
            $this->activityArray[$keyForm]['total']['totalForecastedProjectBudgetLaborTrend'] = $totalForecastedProjectBudgetLaborTrend;
            
            
            $this->activityArray[$keyForm]['total']['totalForecastedProjectBudgetAreaTrend'] = $totalForecastedProjectBudgetAreaTrend;
            $this->activityArray[$keyForm]['total']['totalForecastedProjectBudgetMaterialAreaTrend'] = $totalForecastedProjectBudgetMaterialAreaTrend;
            $this->activityArray[$keyForm]['total']['totalForecastedProjectBudgetLaborAreaTrend'] = $totalForecastedProjectBudgetLaborAreaTrend;
            
            $this->activityArray[$keyForm]['total']['totalPlannedBugdet'] = $totalActivityPlannedBugdet;
            $this->activityArray[$keyForm]['total']['totalActualBudgetDeviated'] = $totalActivityActualBudgetDeviated;
            $this->activityArray[$keyForm]['total']['totalPlannedTotalMaterialCost'] = $totalPlannedTotalMaterialCost;
            $this->activityArray[$keyForm]['total']['totalPlannedTotalLaborCost'] = $totalPlannedTotalLaborCost;
            
     
   
            $this->activityArray[$keyForm]['total']['totalB01'] = $B01;
            $this->activityArray[$keyForm]['total']['totalB10'] = $B10;
            $this->activityArray[$keyForm]['total']['totalB30'] = $B30;
            $this->activityArray[$keyForm]['total']['totalB40'] = $B40;
            $this->activityArray[$keyForm]['total']['totalB45'] = $B45;
            $this->activityArray[$keyForm]['total']['totalB47'] = $B47;
            $this->activityArray[$keyForm]['total']['totalB49'] = $B49;
            $this->activityArray[$keyForm]['total']['totalB50'] = $B50;
            $this->activityArray[$keyForm]['total']['totalB52'] = $B52;
            $this->activityArray[$keyForm]['total']['totalB53'] = $B53;
            $this->activityArray[$keyForm]['total']['totalB54'] = $B54;
            $this->activityArray[$keyForm]['total']['totalB55'] = $B55;
            $this->activityArray[$keyForm]['total']['totalB56'] = $B56;
            $this->activityArray[$keyForm]['total']['totalB57'] = $B57;
            
            $this->activityArray[$keyForm]['total']['totalM55'] = $M55 * 100;
            $this->activityArray[$keyForm]['total']['totalM40'] = $totalM40;
            $this->activityArray[$keyForm]['total']['totalM41'] = $M41;
            $this->activityArray[$keyForm]['total']['totalM42'] = $M42;
            
            $this->activityArray[$keyForm]['total']['totalH15'] = $H15;
            $this->activityArray[$keyForm]['total']['totalH30'] = $H30;
            $this->activityArray[$keyForm]['total']['totalH34'] = $H34;
            $this->activityArray[$keyForm]['total']['totalH35'] = $H35;
            $this->activityArray[$keyForm]['total']['totalH46'] = $H46;
            $this->activityArray[$keyForm]['total']['totalH40'] = $totalH40;
            $this->activityArray[$keyForm]['total']['totalH41'] = $H41;
            $this->activityArray[$keyForm]['total']['totalH42'] = $H42;
            $this->activityArray[$keyForm]['total']['totalH55'] = $H55 * 100;
            $this->activityArray[$keyForm]['total']['totalH25'] = $H25;
            $this->activityArray[$keyForm]['total']['totalH68'] = $H68;
            $this->activityArray[$keyForm]['total']['totalH69'] = $H69;
            
            $this->activityArray[$keyForm]['total']['totalT10'] = $T10;
            $this->activityArray[$keyForm]['total']['totalT15'] = $totalT15;
            $this->activityArray[$keyForm]['total']['totalT16'] = $T16;
            $this->activityArray[$keyForm]['total']['totalT17'] = $T17;
            $this->activityArray[$keyForm]['total']['totalT21'] = $T21;
            $this->activityArray[$keyForm]['total']['totalT22'] = $T22;
            $this->activityArray[$keyForm]['total']['totalT23'] = $T23 * 100;
            $this->activityArray[$keyForm]['total']['totalT25'] = $T25;
            $this->activityArray[$keyForm]['total']['totalT30'] = $T30 * 100;
            $this->activityArray[$keyForm]['total']['totalT35'] = $T35;
            $this->activityArray[$keyForm]['total']['totalT36'] = $T36;
            $this->activityArray[$keyForm]['total']['totalT37'] = $T37;
            $this->activityArray[$keyForm]['total']['totalT38'] = $T38;
            $this->activityArray[$keyForm]['total']['totalT39'] = $T39;
            $this->activityArray[$keyForm]['total']['totalT01'] = $T01;
            $this->activityArray[$keyForm]['total']['totalT05'] = $T05;
            $this->activityArray[$keyForm]['total']['totalT06'] = $T06;
            $this->activityArray[$keyForm]['total']['totalT07'] = $T07;
            $this->activityArray[$keyForm]['total']['totalT08'] = $T08;
            $this->activityArray[$keyForm]['total']['totalT11'] = $T11;
            $this->activityArray[$keyForm]['total']['totalT12'] = $T12;
            $this->activityArray[$keyForm]['total']['totalT13'] = $T13;
            $this->activityArray[$keyForm]['total']['totalT14'] = $T14 * 100;
            $this->activityArray[$keyForm]['total']['totalT18'] = $T18;
            $this->activityArray[$keyForm]['total']['totalT19'] = $T19 * 100;
            $this->activityArray[$keyForm]['total']['totalT40'] = $T40 * 100;
            $this->activityArray[$keyForm]['total']['totalT41'] = $T41;
            $this->activityArray[$keyForm]['total']['totalT42'] = $T42;
            
            $this->activityArray[$keyForm]['total']['totalM15'] = $M15;
            $this->activityArray[$keyForm]['total']['totalM20'] = $M20;
            $this->activityArray[$keyForm]['total']['totalM21'] = $M21;
            $this->activityArray[$keyForm]['total']['totalM26'] = $M26;
            $this->activityArray[$keyForm]['total']['totalM30'] = $M30;
            $this->activityArray[$keyForm]['total']['totalM35'] = $M35;
            $this->activityArray[$keyForm]['total']['totalM46'] = $M46;
            $this->activityArray[$keyForm]['total']['totalM60'] = $M60;
            $this->activityArray[$keyForm]['total']['totalM65'] = $M65;
            $this->activityArray[$keyForm]['total']['totalM66'] = $M66;
            $this->activityArray[$keyForm]['total']['totalM67'] = $M67;
            $this->activityArray[$keyForm]['total']['totalM68'] = $M68;
            $this->activityArray[$keyForm]['total']['totalM69'] = $M69;
            $this->activityArray[$keyForm]['total']['totalM25'] = $M25;
            
            $this->activityArray[$keyForm]['total']['totalH20'] = $H20;
            $this->activityArray[$keyForm]['total']['totalH21'] = $H21;
            $this->activityArray[$keyForm]['total']['totalH26'] = $H26;
            $this->activityArray[$keyForm]['total']['totalH56'] = $H56;
            $this->activityArray[$keyForm]['total']['totalH60'] = $H60;
            $this->activityArray[$keyForm]['total']['totalH65'] = $H65;
            $this->activityArray[$keyForm]['total']['totalH66'] = $H66;
            $this->activityArray[$keyForm]['total']['totalH67'] = $H67;
            
            $this->activityArray[$keyForm]['total']['totalI10'] = $I10;
            $this->activityArray[$keyForm]['total']['totalI01'] = $I01;
            $this->activityArray[$keyForm]['total']['totalI17'] = $I17;
            $this->activityArray[$keyForm]['total']['totalI18'] = $I18;
            $this->activityArray[$keyForm]['total']['totalI33'] = $I33;
            $this->activityArray[$keyForm]['total']['totalI35'] = $I35;
            $this->activityArray[$keyForm]['total']['totalI36'] = $I36;
            $this->activityArray[$keyForm]['total']['totalI40'] = $I40;
            $this->activityArray[$keyForm]['total']['totalI41'] = $I41;
            $this->activityArray[$keyForm]['total']['totalI42'] = $I42;
            $this->activityArray[$keyForm]['total']['totalI44'] = $I44;
            $this->activityArray[$keyForm]['total']['totalI46'] = $I46;
            $this->activityArray[$keyForm]['total']['totalI66'] = $I66;
            
            $this->activityArray[$keyForm]['total']['totalTI06'] = $TI06;
            $this->activityArray[$keyForm]['total']['totalTI07'] = $TI07;
            $this->activityArray[$keyForm]['total']['totalTI08'] = $TI08;
            $this->activityArray[$keyForm]['total']['totalTI11'] = $TI11;
            $this->activityArray[$keyForm]['total']['totalTI17'] = $TI17;
            $this->activityArray[$keyForm]['total']['totalTI18'] = $TI18;
            $this->activityArray[$keyForm]['total']['totalTI19'] = $TI19;
            $this->activityArray[$keyForm]['total']['totalTI21'] = $TI21;
            $this->activityArray[$keyForm]['total']['totalTI22'] = $TI22;
            $this->activityArray[$keyForm]['total']['totalTI23'] = $TI23;
            $this->activityArray[$keyForm]['total']['totalTI24'] = $TI24;
            $this->activityArray[$keyForm]['total']['totalTI25'] = $TI25;
            $this->activityArray[$keyForm]['total']['totalTI35'] = $TI35;
            $this->activityArray[$keyForm]['total']['totalTI36'] = $TI36;
            $this->activityArray[$keyForm]['total']['totalTI37'] = $TI37;
            $this->activityArray[$keyForm]['total']['totalTI41'] = $TI41;
            
            
            $this->activityArray[$keyForm]['total']['totalTI42'] = $TI42;
            
            $this->activityArray[$keyForm]['total']['totalCUMH15'] = $CUMH15;
            $this->activityArray[$keyForm]['total']['totalCUMH20'] = $CUMH20;
            $this->activityArray[$keyForm]['total']['totalCUMH25'] = $CUMH25;
            $this->activityArray[$keyForm]['total']['totalCUMH30'] = $CUMH30;
            $this->activityArray[$keyForm]['total']['totalCUMH34'] = $CUMH34;
            $this->activityArray[$keyForm]['total']['totalCUMH35'] = $CUMH35;
            
            $this->activityArray[$keyForm]['total']['totalCUMM15'] = $CUMM15;
            $this->activityArray[$keyForm]['total']['totalCUMM20'] = $CUMM20;
            $this->activityArray[$keyForm]['total']['totalCUMM25'] = $CUMM25;
            $this->activityArray[$keyForm]['total']['totalCUMM30'] = $CUMM30;
            $this->activityArray[$keyForm]['total']['totalCUMM35'] = $CUMM35;
            
            $this->activityArray[$keyForm]['total']['totalCUMI01'] = $CUMI01;
            $this->activityArray[$keyForm]['total']['totalCUMI10'] = $CUMI10;
            
            $this->activityArray[$keyForm]['total']['totalCUMT01'] = $CUMT01;
            $this->activityArray[$keyForm]['total']['totalCUMT05'] = $CUMT05;
            $this->activityArray[$keyForm]['total']['totalCUMT10'] = $CUMT10;
            
            
            $this->activityArray[$keyForm]['is_percentage'] =  $this->is_percentage;
            
            
            
   





        }

  }



  public function getPlannedBudgetVsActualVsForecastedTrend()
  {

    $formsArray = array();

    foreach ($this->forms as $keyForm => $form) {
      $formsArray['details'][$keyForm] = $form;
      $formsArray['details'][$keyForm]['data'] = $this->activityArray[$keyForm];
    }
    
    return $formsArray;
  }

 public function getParentIds($ids)
  {
      

    $ids = ProjectDivision::whereIn('parent_id',$ids)->pluck('id')->toArray();
     if(count(ProjectDivision::whereIn('parent_id',$ids)->pluck('id')->toArray()) > 0)
     {
         $ids = self::getParentIds($ids);
        
     }

    
    return $ids;
  }
  
   public function getDivParentId($id)
  {
      

    $parent_id = ProjectDivision::where('id',$id)->first()->parent_id;
   
     if(isset($parent_id))
     {
         $parent_id = self::getDivParentId($parent_id);
        
     }
     
     if($parent_id == NULL)
     {
         $parent_id = $id;
     }

    
    return $parent_id;
  }
  
  

  public function getLastDay()
  {

    $formsArray = array();

      $formsArray= $this->activityArray[count($this->activityArray) - 1];
    
     
    return $formsArray;
  }

    public function getCumulative($is_cum,$activity,$keyForm,$data,$name)
    {
        
         $lastData = ($keyForm!=0 && isset($this->cumulativeArray[$keyForm - 1]['div'.$activity->division_id][$name][$activity->area_id]))? $this->cumulativeArray[$keyForm - 1]['div'.$activity->division_id][$name][$activity->area_id] :0;
         $this->cumulativeArray[$keyForm]['div'.$activity->division_id][$name][$activity->area_id] = ($is_cum) ? $lastData  + $data : $data;
         
         $sum = array_sum($this->cumulativeArray[$keyForm]['div'.$activity->division_id][$name]);
       
         
        // $CUMData  =  $this->cumulativeArray[$keyForm]['div'.$activity->division_id]['T06'][$activity->area_id];
        
         return $sum;
                    

    }
    
    public function getParentCumulative($parent,$activity,$keyForm,$data,$name)
    {
        
       
         $this->parentCumulativeArray[$keyForm][$parent][$name]['div'.$activity->division_id] =  $data ;
         
        
         return array_sum($this->parentCumulativeArray[$keyForm][$parent][$name]);
         
         
                    

    }
    
    
      
    public function getTotalCumulative($parent,$keyForm,$data,$name)
    {
        
        
        $this->totalCumulativeArray[$name][$keyForm]["div".$parent] =  $data ;
         
         $sum = array_sum($this->totalCumulativeArray[$name][$keyForm]);
       

         
         return $sum;
                    

    }
    
    



  public function getActivityAnalysis(Request $request)
  {

    $activityArray = array();


    $activityBenchmark = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)
                                            ->where('project_division_id',$this->activityId)
                                            ->where('area_id',$this->areaId)
                                            ->first();



      $labor_unit_cost = $activityBenchmark->unit_labor_hour *  $activityBenchmark->hours_unit;
      $quantity = $activityBenchmark->quantity;
      $TotalLaborHour =  self::calcTotalLaborHour($quantity,$activityBenchmark->hours_unit);
      $MaterialCost =  self::calcTotalMaterialCost($quantity,$activityBenchmark->unit_material_rate);

      $laborUnitCost =  self::calcLaborUnitCost($activityBenchmark->unit_labor_hour,$activityBenchmark->hours_unit);

      $TotalLaborCost =  self::calcTotalLaborCost($quantity,$laborUnitCost);

        $TotalUnitRate =  self::calcTotalUnitRate($activityBenchmark->unit_labor_hour,$activityBenchmark->unit_material_rate);
        $Budget = $MaterialCost + $TotalLaborCost;

      $activityArray['benchmark'] =  $activityBenchmark;
      $activityArray['benchmark']['unit'] = 'm';

      $activityArray['benchmark']['laborUnitCost'] = number_format($laborUnitCost, 2);
      $activityArray['benchmark']['UnitRate'] =  number_format($activityBenchmark->unit_material_rate + $labor_unit_cost, 2);
      $activityArray['benchmark']['TotalBudget'] = number_format($Budget, 2);

      $activityArray['benchmark']['TotalLaborCost'] = number_format($TotalLaborCost, 2);
      $activityArray['benchmark']['TotalMaterialCost'] =  number_format($MaterialCost, 2);
      $activityArray['benchmark']['TotalUnitRate'] =  number_format($TotalUnitRate, 2);

      $activityArray['benchmark']['TotalLaborHour'] = number_format($TotalLaborHour);


      $activityForm = FormDetail::where('form_id',$this->formId)
                                              ->where('division_id',$this->activityId)
                                              ->where('area_id',$this->areaId)
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
       $trendDeviationUnits = $percentDeviationUnits * $MaterialCost;
       $trendQuantityUnits = $trendDeviationUnits + $MaterialCost;

       $activityArray['general']['plannedCompletedUnitsPerBOQ'] = number_format($plannedCompletedUnitsPerBOQ);
       $activityArray['general']['unitsQtyDeviationCompToBOQ'] = number_format($unitsQtyDeviationCompToBOQ);
       $activityArray['general']['actualUnitsDeviationCost'] = number_format($actualUnitsDeviationCost,2);
       $activityArray['general']['percentDeviationUnits'] = number_format($percentDeviationUnits * 100, 2);
       $activityArray['general']['trendDeviationUnits'] = number_format($trendDeviationUnits,2);
       $activityArray['general']['trendQuantityUnits'] = number_format($trendQuantityUnits,2);

       $totalDeviation = $hourDeviationCost + $actualUnitsDeviationCost;
       $BudgetDeviated = $Budget + $totalDeviation;
       $trendBudget = $trendQuantityUnits + $trendHours;
       $trendDeviationHU = $trendBudget - $Budget;


       $activityArray['general']['totalDeviation'] = number_format($totalDeviation,2);
       $activityArray['general']['totalBudgetDeviated'] = number_format($BudgetDeviated,2);
       $activityArray['general']['trendBudget'] = number_format($trendBudget,2);
       $activityArray['general']['trendDeviationHU'] = number_format($trendDeviationHU,2);


       $percentUnitCompletedCalc = ($activityBenchmark->quantity <> 0) ? $activityForm->quantity / $activityBenchmark->quantity : 0 ;
       $percentGangCompletedCalc = ($TotalLaborHour <> 0) ? $gangHours / $TotalLaborHour : 0 ;

       $activityArray['general']['percentUnitCompletedCalc'] = number_format($percentUnitCompletedCalc * 100,2);
       $activityArray['general']['percentGangCompletedCalc'] = number_format($percentGangCompletedCalc * 100,2);



      return $activityArray;
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
      
      
      public function calcBenchmarkPerActivity($benchmark_id,$division_id)
      {
          $benchmarks = BenchmarkDetail::where('benchmark_id',$benchmark_id)->leftJoin('areas','areas.id','=','benchmark_details.area_id')
                  ->whereNotNull('areas.id')->whereNotIn('areas.id',$this->areas)->where('project_division_id', $division_id)->get();
            
          $B46 = 0;
   
          
          $benchmarkPerActivityArr = array();
          
          foreach($benchmarks as $benchmark)
          {
              
              $hours_unit = 0;
                    if(isset($benchmark->hours_unit)) { $hours_unit = $benchmark->hours_unit;}
                    $B15 = $hours_unit; //rename $B15_B15 
                    
             $B46 += $benchmark->quantity * $B15;
             
          }
          
          $benchmarkPerActivityArr['totalB46'] = $B46;
        
          
          return $benchmarkPerActivityArr;
      }
      
      
      public function calcTotalBenchmarkCost($benchmark_id,$activityId,$areaId)
      {
          
          $cost = array();
          $benchmarks = BenchmarkDetail::where('benchmark_details.benchmark_id',$this->benchmarkId);
          
           
                if(isset($activityId)){
                    if(is_array($activityId))
                    {
                         $benchmarks->whereIn('benchmark_details.project_division_id',$activityId);
                        
                    }
                    else{
                        $benchmarks->where('benchmark_details.project_division_id',$activityId);
                    }
                    
                }
                else{
                    $benchmarks->leftJoin('project_divisions','project_divisions.id','=','benchmark_details.project_division_id')->whereNotNull('project_divisions.id');
                }
                if(isset($areaId)){
                    $benchmarks->where('benchmark_details.area_id',$areaId);
                }
        
          $benchmarks = $benchmarks->leftJoin('areas','areas.id','=','benchmark_details.area_id')
                  ->whereNotNull('areas.id')->whereNotIn('areas.id',$this->areas)->get();
                  
                 
          $plannedTotalMaterialCost = 0;
          $plannedTotalLaborCost = 0;
          $plannedTotalLaborHour = 0;
  
          
          
          foreach($benchmarks as $benchmark)
          {
              
                   
                   
                    
                    $unit_material_rate = 0; 
                    if(isset($benchmark->unit_material_rate)) {$unit_material_rate = $benchmark->unit_material_rate;}  
                    
                    $B05 = $unit_material_rate; //rename $plannedMaterialUnitRate_B05
                    
                    $plannedTotalMaterialCost+= $benchmark->quantity * $B05;
                    
                     $hours_unit = 0;
                    if(isset($benchmark->hours_unit)) { $hours_unit = $benchmark->hours_unit;}
                    $B15 = $hours_unit; //rename $B15_B15 
                    
                    $B20 = $benchmark->unit_labor_hour; //rename $plannedLaborCostPerHour_B20
                    
                    $plannedLaborCost = $B15 * $B20; //rename $plannedLaborUnitCost_B25
                    $plannedTotalLaborCost += $benchmark->quantity * $plannedLaborCost;
                    $plannedTotalLaborHour += $hours_unit * $benchmark->quantity;
                    
                    
                    
                    
          }
          
         
       
                     
                    
          $total = $plannedTotalMaterialCost + $plannedTotalLaborCost;
          

          $cost['total'] = $total;
          $cost['material'] = $plannedTotalMaterialCost;
          $cost['labor'] = $plannedTotalLaborCost;
          $cost['labor_hour'] = $plannedTotalLaborHour;
          
          
          return $cost;
      }
}
