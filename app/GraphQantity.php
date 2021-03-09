<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FormDetail;
use App\BenchmarkDetail;
use App\Form;


class GraphQuantity
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
  public $areaId;


  public $activityArray = array();
  public $laborActivityArray = array();
  public $quantityActivityArray = array();
  public $scheduleActivityArray = array();


  public function __construct($benchmarkId,$date,$activityId,$areaId,$parent)
  {

    if(strpos($date, " - ") !== false)
    {
      $dateArr = explode(" - ",$date);
      $fromDate = $dateArr[0];
      $toDate = $dateArr[1];
      $this->forms = Form::whereBetween('date', [$fromDate, $toDate])->get();
    }
    else{
     $this->forms = Form::orderBy('date')->get();
    }
    



    $this->benchmarkId = $benchmarkId;

    $cumulativeQuantity = 0; //values
    $cumulativePercentage = 0; //values
    $cumulativeWorkingHours = 0; //values

    $cumulativeArray = array();
    $cumulativeActivityArray = array();

        $benchamrkCost = self::calcTotalBenchmarkCost($this->benchmarkId,$activityId,$areaId);
        
        
        
        $totalActivityPlannedBugdet = 0;
        
        $totalActivityActualBudgetDeviated = 0 ;
        $totalActivityForecastBudgetTrend = 0 ;

        $totalForecastedTotalLaborCost = 0;
        $totalActualLaborCost = 0;
        $totalPlannedLaborCostPerActualMaterial = 0;
        $totalPlannedTotalLaborCost = 0;


        $totalPlannedCostMaterial=0;
        $totalPlannedTotalMaterialCost = 0;
        $totalActualCostMaterial = 0;
        $totalForecastedTotalMaterialCost = 0;

        $totalPlannedCompletedQtyMaterialPerActualLaborHours = 0;
        $totalActualQuantityMaterial=0;
        $totalForecastedTotalLaborHours = 0;
        $forecastedTotalLaborCost = 0;
        $totalForecastedPercentageBudgetDeviationTrend = 0;
        $totalB10 = 0;
        $totalB40 = 0;
        $totalB45 = 0;
        $totalB47 = 0;
        $totalB49 = 0;
        $totalB50 = 0;
        $totalB52 = 0;
        $totalB53 = 0;
        $totalB54 = 0;
        $totalB30 = 0;
        $totalT01 = 0;
        $totalT07 = 0;
        $totalT08 = 0;
        $totalT11 = 0;
        $totalT12 = 0;
        $totalT13 = 0;
        $totalT14 = 0;
        $totalT16 = 0;
        $totalT17 = 0;
        $totalT18 = 0;
        $totalT19 = 0;
        $totalT30 = 0;
        $totalT35 = 0;
        $totalT36 = 0;
        $totalT37 = 0;
        $totalT38 = 0;
        $totalT39 = 0;
        $totalT40 = 0;
        $totalT21 = 0;
        $totalT22 = 0;
        $totalT23 = 0;
        $totalT25 = 0;
        $totalT15 = 0;
        $totalT10 = 0;
        $totalT41 = 0;
        $totalT42 = 0;
        $totalM15 = 0;
        $totalM30 = 0;
        $totalM35 = 0;
        $totalM46 = 0;
        $totalM60 = 0;
        $totalM55 = 0;
        $totalM40 = 0;
        $totalM41 = 0;
        $totalM42 = 0;
        $totalH15 = 0;
        $totalH30 = 0;
        $totalH40 = 0;
        $totalH41 = 0;
        $totalH42 = 0;
        $totalH55 = 0;
        $totalH25 = 0;
        $totalT05 = 0;
        $totalT06 = 0;
        $totalM20 = 0;
        $totalM25 = 0;
        $totalM65 = 0;
        $totalM66 = 0;
        $totalM67 = 0;
        $totalM68 = 0;
        $totalM69 = 0;
        $totalH20 = 0;
        $totalH35 = 0;
        $totalH46 = 0;
        $totalH60 = 0;
        $totalH65 = 0;
        $totalH66 = 0;
        $totalH67 = 0;
        $totalH68 = 0;
        $totalH69 = 0;
        $totalI10 = 0;
        $totalI01 = 0;
        $totalUnitMaterialRate = 0;
        $totalLaborRate = 0;
        $totalForecastedProjectBudgetTrend = 0;
        $totalForecastedProjectBudgetMaterialTrend = 0;
        $totalForecastedProjectBudgetLaborTrend = 0;
        
        $totalForecastedProjectBudgetAreaTrend = 0;
        $totalForecastedProjectBudgetMaterialAreaTrend = 0;
        $totalForecastedProjectBudgetLaborAreaTrend = 0;
        
        $totalCUMH15 = 0;
        $totalCUMH30 = 0;
        $totalCUMM15 = 0;
        $totalCUMM30 = 0;
        $totalCUMI01 = 0;
        $totalCUMI10 = 0;
        
        //souheil
        $H01 = 0;
        $H05 = 0;
        $actualRemaningLaborHours = 0;
        
        
        
                        

    foreach ($this->forms as $keyForm => $form) {

        //actual
        $activities = FormDetail::where('form_id',$form->id);
        
        if(isset($activityId)){
            $activities->where('division_id',$activityId);
        }
        if(isset($areaId)){
            $activities->where('area_id',$areaId);
        }
        
        $activities = $activities->orderBy('division_id')->orderBy('area_id')->get();
        
        



        foreach ($activities as $key => $activity) {
            
            
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



                
                if(isset($benchmarkArr))
                {
                    //BOQ Planned
                    
                   
                      $B01 = $benchmarkArr->quantity;
                    
                
                    
                    if(isset($areaId)){
                      $B48 = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)->where('project_division_id', $activity->division_id)->where('area_id',$areaId)->sum('quantity');
                    }
                    else{
                      $B48 = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)->where('project_division_id', $activity->division_id)->leftJoin('areas','areas.id','=','benchmark_details.area_id')
                                 ->whereNotNull('areas.id')->sum('quantity');
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
                    $plannedHoursPerUnit = $hours_unit; //rename $plannedHoursPerUnit_B15 
                    
                    $B20 = $benchmarkArr->unit_labor_hour; 
                    
                    $plannedLaborCost = $plannedHoursPerUnit * $B20; //rename $plannedLaborUnitCost_B25
                    $B30 = $B01 * $plannedLaborCost; 
                    
                    
                  
                    $plannedTotalLaborCost = $B48 * $plannedLaborCost;
                    
                    $plannedUnitRate = $plannedLaborCost + $B05; //rename $plannedUnitRate_B35
                    
                    $B40 = $B10 + $B30; 
                    
                    
                    $B45 = $plannedHoursPerUnit * $B01; 
                    $totalB45 += $B45;
                    
                    $benchmarkPerActivity = self::calcBenchmarkPerActivity($this->benchmarkId,$activity->division_id);     
                    $B46 = $benchmarkPerActivity['totalB46']; 
            
                    $B47 = $B46 * $B20;
                    
                    
                     
                    $B49 = $B48 * $B05 ;
                    $B50 = $B47 + $B49; 
                    $B51 = $benchamrkCost['labor_hour'];
                    $B52 = $benchamrkCost['labor'];
                    $B53 = $benchamrkCost['material'] ;
                    $B54 = $benchamrkCost['total'] ;
                    
                    
                 
                    
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
                    $lastQty = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['quantity']))? $cumulativeArray[$keyForm - 1][$key]['quantity'] :0;
                    $lastPerc = ($keyForm!=0  && isset($cumulativeArray[$keyForm - 1][$key]['percentage_completed']))? $cumulativeArray[$keyForm - 1][$key]['percentage_completed'] :0;
                    $lastWorkingHours = ($keyForm!=0  && isset($cumulativeArray[$keyForm - 1][$key]['working_hours']))? $cumulativeArray[$keyForm - 1][$key]['working_hours'] :0;
                    $lastLaborOvertime = ($keyForm!=0  && isset($cumulativeArray[$keyForm - 1][$key]['extra_hours']))? $cumulativeArray[$keyForm - 1][$key]['extra_hours'] :0;

                    $cumulativeArray[$keyForm][$key]['activity'] = $benchmarkArr->id;
                    $cumulativeArray[$keyForm][$key]['quantity'] = $lastQty + $quantity;
                    $cumulativeArray[$keyForm][$key]['percentage_completed'] =  $lastPerc  + $activity->percentage_completed / 100;
                    $cumulativeArray[$keyForm][$key]['working_hours'] =  $lastWorkingHours + $activity->working_hours;
                    $cumulativeArray[$keyForm][$key]['extra_hours'] =  $lastLaborOvertime + $activity->extra_hours;

                    $cumulativeQuantity =  $cumulativeArray[$keyForm][$key]['quantity'];
                    $cumulativePercentage = $cumulativeArray[$keyForm][$key]['percentage_completed'];
                    $cumulativeWorkingHours = $cumulativeArray[$keyForm][$key]['working_hours'];
                    $cumulativeLaborOvertime = $cumulativeArray[$keyForm][$key]['extra_hours'];

                    $actualQuantityPerUnit = $quantity;

                    //Hours Calculations

                    $H01 = ($B45 != 0)? $cumulativeWorkingHours/$B45 :0; //rename $actualPercentageCompletedLaborHours_H01
                    $H05 = 1 - $H01; //rename $actualRemainingPercentageLaborHours_H05
                    $H10 = $H05 * $B45; //rename $H10
                    
                
                    $H15 = $actualQuantityPerUnit * $plannedHoursPerUnit; //to rename $plannedLaborHoursPerActualMaterial_H15
                    
                    $lastH15 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['H15']))? $cumulativeArray[$keyForm - 1][$key]['H15'] :0;
                    $cumulativeArray[$keyForm][$key]['H15'] = $lastH15 + $H15;
                    $CUMH15 =  $cumulativeArray[$keyForm][$key]['H15'];
                    
                    $totalCUMH15 = $CUMH15;
                    $totalH15 = $H15;
                    
                    $H16 = $B45 - $CUMH15;
                    
                    //added by souheil
                    $H20 = $H15 * $B20; //to rename $plannedLaborCostPerActualMaterial_H20
                    
                    $lastH20 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H20'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H20'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['H20'][$activity->area_id] = $lastH20 + $H20;
                    $CUMH20 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['H20'][$activity->area_id];
                    
                    $lastCUMH20 = ($keyForm!=0 && isset($cumulativeArray[$keyForm][$key - 1]['div'.$activity->division_id]['H20']))? $cumulativeArray[$keyForm][$key - 1]['div'.$activity->division_id]['H20'] :0;
                    $cumulativeActivityArray['H20']['div'.$activity->division_id] = $lastCUMH20 + $CUMH20;
                    
                    $H21 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['H20']); //!
                    
                    $lastH21 = ($keyForm !=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H21'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H21'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['H21'][$activity->area_id] = $lastH21 + $H21;
                    $CUMH21 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['H21'][$activity->area_id];
                    
                    
                    $H22 = $B47 - $H21;
                    
                    
                 
                    $H23 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['H21']);
                    
                    
                   
                    $H25 = $B20 *  $I10; //to be renamed $actualLaborCost_H25
                    
                    $lastH25 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H25'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H25'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['H25'][$activity->area_id] = $lastH25 + $H25;
                    $CUMH25 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['H25'][$activity->area_id];
                    
                    $lastCUMH25 = ($keyForm!=0 && isset($cumulativeArray[$keyForm][$key - 1]['div'.$activity->division_id]['H25']))? $cumulativeArray[$keyForm][$key - 1]['div'.$activity->division_id]['H25'] :0;
                    $cumulativeActivityArray['H25']['div'.$activity->division_id] = $lastCUMH25 + $CUMH25;
                    
                    $H26 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['H25']); //!
                    
                    $lastH26 = ($keyForm !=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H26'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H26'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['H26'][$activity->area_id] = $lastH26 + $H26;
                    $CUMH26 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['H26'][$activity->area_id];
                    
                    $H27 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['H26']);
                     
                    $H30 = $I10 - $H15; //to be renamed $I10Deviation_H30
                    $lastH30 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['H30']))? $cumulativeArray[$keyForm - 1][$key]['H30'] :0;
                    $cumulativeArray[$keyForm][$key]['H30'] = $lastH30 + $H30;
                    $CUH30 =  $cumulativeArray[$keyForm][$key]['H30'];
                    
                    $totalH30 = $H30;
                    $totalCUMH30 = $CUH30;
                    
                    $H35 = $B20 * $H30; //to be renamed $I10DeviationCost_H35
                    $lastH35 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H35'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['H35'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['H35'][$activity->area_id] = $lastH35 + $H35;
                    $CUMH35 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['H35'][$activity->area_id];
           
                    
                  // var_dump($CUMH35);
                    
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['H40'][$activity->area_id] = $CUMH35 + $B30;
                     
                    //added by souheil
                    $H40 = $H35 + $B30; //to be renamed $actualTotalLaborCost_H40
                    
                    $H42 = $B02 * $plannedLaborCost;
                    

                    $H41 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['H40']);
                    

                    
                    $H45 = ($H15 != 0)? $H30 / $H15 : 0; //to rename $actualPercentageLaborHourDeviation_H45
                
                    
                    $H46 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['H35']);
                    $H47 = ($H21 != 0) ? $H46/$H21 : 0;
                    
                   
                    
                    $H48 = $H16*(1+$H45);
                    
                    $lastI10 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['I10']))? $cumulativeArray[$keyForm - 1][$key]['I10'] :0;
                    $cumulativeArray[$keyForm][$key]['I10'] = $lastI10 + $I10;
                    $CUMI10 =  $cumulativeArray[$keyForm][$key]['I10'];
                    
                    $totalI10 = $I10;
                    $totalCUMI10 = $CUMI10;
                    
                    $H49 = $H48 + $CUMI10;
                    
                    
                    //added by souheil
                    $H50 = $H49 - $B45;
                    
                    $H60 = $H50 * $B20; //to rename $forecastedDeviationLaborHoursCost_H60
                   
                    $H55 = ($B30 != 0)? $H60 / $B30 : 0; 
                    
                    $H70 = $H22 * (1 + $H47);
     
                    $H65 = $H49 * $B20;
                    $H66 = $H70 + $H26; 
                    
                    
                    $H67 = $H66 - $B47;
                   
                    
                    
                    $cumulativeActivityArray['H67'][$keyForm]['div'.$activity->division_id] = $H67;
                    
                    $H69 = array_sum($cumulativeActivityArray['H67'][$keyForm]);
                    
                    $totalH69 = $H69;
                    
                     $H68 = $H69 + $B52;
                    
                    $totalH68 =  $H68;
                    
                    
                    
                   

                    //Material Calculations
                    
                    $lastAAI01 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['AAI01']))? $cumulativeArray[$keyForm - 1][$key]['AAI01'] :0;
                    $cumulativeArray[$keyForm][$key]['AAI01'] = $lastAAI01 + $quantity;
                    $CUMI01 = $cumulativeArray[$keyForm][$key]['AAI01'];
                    $CUAAI01 = FormDetail::where('form_id',$form->id)->where('division_id', $activity->division_id)->sum('quantity');
                    
                    $totalI01 = $quantity;
                    $totalCUMI01 = $CUMI01;
                    
                    $I16 = $quantity - $B01;
                    $I23 = $quantity * $B05;
                    
                    $lastI23 = ($keyForm !=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['I23'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['I23'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['I23'][$activity->area_id] = $lastI23 + $I23;
                    $CUMI23 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['I23'][$activity->area_id];
                    
                    $I33 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['I23']);
                    
                    $I43 = $I23 - $B05;
                    
                    $lastI43 = ($keyForm !=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['I43'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['I43'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['I43'][$activity->area_id] = $lastI43 + $I43;
                    $CUMI43 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['I43'][$activity->area_id];
                    
                    $I53 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['I43']);
                    
                    $I70 = $B49 - $I33;
                    
                    $I40 = $I43 + $B10;
                    
                    $lastI40 = ($keyForm !=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['I40'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['I40'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['I40'][$activity->area_id] = $lastI40 + $I40;
                    $CUMI40 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['I40'][$activity->area_id];
                    
                    $I41 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['I40']);
                    
                    
                    $lastB10 = ($keyForm !=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['B10'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['B10'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['B10'][$activity->area_id] = $lastB10 + $B10;
                    $CUMI40 =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['B10'][$activity->area_id];
                    
                    $I42 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['B10']);
                    
                    $I66 = $I53 + $B49;
                    
                //     var_dump($totalI01);
                    
                    //Newly Added
                    $actualPercentageCompletedMaterial = ($B01 != 0)? $cumulativeArray[$keyForm][$key]['AAI01']/$B01 : 0; //to rename $actualPercentageCompletedMaterial_M01
                    $M05 = 1 - $actualPercentageCompletedMaterial; //to rename $actualRemainingPercentageMaterial_M05
                    $M10 = $M05 * $B01; //to rename $actualRemainingMaterialQuantity_M10
                    $M11 = $M05 * $B48;//ActualremaningTotalMaterialQuantity
                    
                    $M15 = $B01 * $I05; //to rename $PlannedCompletedMaterialPerBOQQuantity_M15
                    $AAM15 = $B02 * $I05;
                    
                    $lastM15 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['M15']))? $cumulativeArray[$keyForm - 1][$key]['M15'] :0;
                    $cumulativeArray[$keyForm][$key]['M15'] = $lastM15+ $M15;
                    $CUMM15 =  $cumulativeArray[$keyForm][$key]['M15'];
                    
                    $totalCUMM15 = $CUMM15;
                    $totalM15 = $M15;
                    
                    $M16 = $B01 - $CUMM15;
                    
                    $AAM20 = $AAM15 * $B05;
  
                     $M20 = $M15 * $B05; //to rename $plannedCostMaterial_M20
                    
                    $lastM20 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M20'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M20'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['M20'][$activity->area_id] = $lastM20  + $M20 ;
                    $CUMM20  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['M20'][$activity->area_id];
                     
                    
                    
                   
                    $M21 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['M20']);
                    $M22 =  $B49 - $M21;
                    
                    $lastM21 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M21'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M21'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['M21'][$activity->area_id] = $lastM21  + $M21 ;
                    $CUMM21  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['M21'][$activity->area_id];
                     
                    
                    
                   
                    $M23 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['M21']);
                    
                  
                    

                    
                    $M25 =  $B05 * $actualQuantityPerUnit; //to rename $actualCostMaterial_M25
                    
                    
                    $lastM25 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M25'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M25'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['M25'][$activity->area_id] = $lastM25  + $M25 ;
                    $CUMM25  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['M25'][$activity->area_id];
                    
                  
                    
                    $lastCUMM25 = ($keyForm!=0 && isset($cumulativeArray[$keyForm][$key - 1]['div'.$activity->division_id]['M25']))? $cumulativeArray[$keyForm][$key - 1]['div'.$activity->division_id]['M25'] :0;
                    $cumulativeActivityArray['M25']['div'.$activity->division_id] = $lastCUMM25 + $CUMM25;
                    
                    $M26 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['M25']); 
                    
                    
                    $lastM26 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M26'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M26'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['M26'][$activity->area_id] = $lastM26  + $M26;
                    $CUMM26  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['M26'][$activity->area_id];
                     
                    
                    
                   
                    $M27 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['M26']);
                    
                    
                    
                    $M30 = $actualQuantityPerUnit - $M15; //to rename $actualMaterialQuantityDeviationComparedToPlanned_M30
                    
                    
                   
                    
                    
                    $AAM30 = $CUAAI01 - $AAM15;
                    $AAM35 = $AAM30 * $B05;
                    
                    $M35 = $M30 * $B05; 
                    
                   
                    
                    $lastM35 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M35'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['M35'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['M35'][$activity->area_id] = $lastM35  + $M35 ;
                    $CUMM35  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['M35'][$activity->area_id];
                    
                  
                    
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['M40'][$activity->area_id] = $CUMM35 + $B10;
                    
                    
                    $AAM40 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['M40']);
                    
           
               
                    
                    
                    
                    
                    $M41 = $AAM40;
                    $M42 = $AAB10;
                  
                    
                  //  var_dump($key." ".$M30." ".$activity->area_id." ".$activity->division_id);
                    
                    $lastM30 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['M30']))? $cumulativeArray[$keyForm - 1][$key]['M30'] :0;
                    $cumulativeArray[$keyForm][$key]['M30'] = $lastM30+ $M30;
                    $CUM30 =  $cumulativeArray[$keyForm][$key]['M30'];
                    
                    $totalM30 = $M30;
                    $totalCUMM30 = $CUM30;
                    
                    
                    $M40 = $M35 + $B10; //to rename $actualTotalMaterialCost_M40
                    $M45 = ($M15 != 0)? $M30 / $M15 : 0 ; //to rename $actualPercentageMaterialDeviation_M45
                    $M46 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['M35']);
                    $M47 = ($M21 != 0)? $M46 / $M21 : 0 ;
                    
                    $M48 = $M16 * (1+$M45);
                    $M49 = $M48 + $CUMI01;
                     
                    //added by souheil
                    $M50 = $M49 - $B01; //rename $forecastedDeviationMaterialQuantity_M50
                    // $M51 = ($M11 * $M45) + $CUM30;//ForecastedDeviationTotalMaterialQuantity
                   
                    
                    $M60 = $M50 * $B05; //rename $forecastedDeviationMaterialCost_M60
                    $M55 = ($B10 != 0)? $M60 / $B10 :0; 
                    // $M61 = $B05 * $M51;//forecastedDeviationTotalMaterialCost
                    $M65 = $M49 * $B05; 
                    
                   
                    $M70 = $M22 * (1+$M47);
                    
                   $M66 = $M70 + $M26;
                     
                    $M67 = $M66 - $B49;
                    
                    $cumulativeActivityArray['M68'][$keyForm]['div'.$activity->division_id] = $M67;
                    $totalM68 = array_sum($cumulativeActivityArray['M68'][$keyForm]) + $B53;
                    
                    $M69 = $totalM68 - $B53;
                    $totalM69 = $M69;
                    
                   
                    
                    
                    $M68 = $M69 + $B53;
                    
                    
                     
                    
                    $plannedCompletedQtyMaterialPerActualLaborHours = ($plannedHoursPerUnit != 0)? $I10 / $plannedHoursPerUnit :0; //to remove
                    $forecastedDeviationLaborHourDelay = $B45 * $M45; //to remove
                    $forecastedTotalLaborHours = $B45 - $forecastedDeviationLaborHourDelay; //to remove
                    
                   
                    
                    //Total Hours & Material
                    $T01 = $M35 + $H35; //rename $actualTotalDeviation_T01
                    
                    $lastT01 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['T01'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['T01'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['T01'][$activity->area_id] = $lastT01  + $T01 ;
                    $CUMT01  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['T01'][$activity->area_id];
                    
                    $T05 = $M20 + $H20; //rename $plannedProjectBudgetVsExecutedActivities_T05
                    $T06 = $M21 + $H21;
                    
                    
                    $lastT06 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['T06'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['T06'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['T06'][$activity->area_id] = $lastT06  + $T06 ;
                    $CUMT06  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['T06'][$activity->area_id];
                    
                    $T07 = $M23 + $H23;

                    $T08 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['T06']);
                    
                    
                    $T10 = $M25 + $H25; //rename $actualCostOfExecutedActivities_T10
                    
                    $T11 = $M26 + $H26;
                    
                    $lastT11 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['T11'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['T11'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['T11'][$activity->area_id] = $lastT11  + $T11 ;
                    $CUMT11  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['T11'][$activity->area_id];
                    
                    $T12 = $M27 + $H27;
                    $T13 = $T12 - $T07;
                    
                    $T14 = ($T07 != 0)? $T13 / $T07 :0;

                    $T21 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['T11']);
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
                    $T38 = $H68 + $M68;
                    
                    
                    
                    $cumulativeActivityArray['T36'][$keyForm]['div'.$activity->division_id] = $T37;
                    $totalT38 = $T38;
                    
                    $T39 = $H69 + $M69;
                    $totalT39 = $T39;
                    
                    $T40 = ($B54 != 0)? $T39 / $B54 :0;
                    
                    $totalCost = $plannedTotalMaterialCost+$plannedTotalLaborCost;
                    
                    
                    $lastT37 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['T37'][$activity->area_id]))? $cumulativeArray[$keyForm - 1]['div'.$activity->division_id]['T37'][$activity->area_id] :0;
                    $cumulativeArray[$keyForm]['div'.$activity->division_id]['T37'][$activity->area_id] = $lastT37  + $T37 ;
                    $CUMT37  =  $cumulativeArray[$keyForm]['div'.$activity->division_id]['T37'][$activity->area_id];

                    $T41 = array_sum($cumulativeArray[$keyForm]['div'.$activity->division_id]['T37']);
                    
                    
                //    $T42 = $T41 + $B57;
                    //   if($parent == 'Entire Budget')
                    //     {
                            
                            $totalActivityPlannedBugdet = $benchamrkCost['total'];
                            $totalPlannedTotalMaterialCost = $benchamrkCost['material'];
                            $totalPlannedTotalLaborCost = $benchamrkCost['labor'];
                           
                            
                        // }
                        // else{
                        //     $totalActivityPlannedBugdet +=  $totalCost;
                        //     $totalPlannedTotalMaterialCost += $plannedTotalMaterialCost;
                        //     $totalPlannedTotalLaborCost+= $plannedTotalLaborCost;
                            
                           
                        // }
                   
        
                    
                    
                    //to review according to above
                    
                  
           
                   
                    $totalActivityForecastBudgetTrend +=  $T35 * $B20;


                    $totalForecastedTotalLaborCost += $H65;
                    $totalActualLaborCost += $H25;
                    $totalPlannedLaborCostPerActualMaterial+= $H15;
                   

                    $totalPlannedCostMaterial+= $M20;
                   
                    $totalActualCostMaterial += $M25;
                    $totalForecastedTotalMaterialCost += $M65;

                    $totalPlannedCompletedQtyMaterialPerActualLaborHours+= $plannedCompletedQtyMaterialPerActualLaborHours;
                    $totalActualQuantityMaterial += $actualQuantityPerUnit;
                    $totalForecastedTotalLaborHours += $forecastedTotalLaborHours;
                   
                    
      
                
               
                    
                    $lastT05 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['T05']))? $cumulativeArray[$keyForm - 1][$key]['T05'] :0;
                    $cumulativeArray[$keyForm][$key]['T05'] = $lastT05+ $T05;
                    $totalT05 =  $cumulativeArray[$keyForm][$key]['T05'];
                    
                   // var_dump($form->title." ".$activity->division_id." ".$T06);
                    $totalT06 = $T06;
                    
                    $lastT10 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['T10']))? $cumulativeArray[$keyForm - 1][$key]['T10'] :0;
                    $cumulativeArray[$keyForm][$key]['T10'] = $lastT10+ $T10;
                    $totalT10 =  $cumulativeArray[$keyForm][$key]['T10'];
                    
                    $totalT41 = $T41;
                    $totalT42 = 0;
                    
                   
               //    $totalT10 = $T10
                    $totalB10 = $B10;
                    $totalB40 = $B40;
                    $totalB47 = $B47;
                    $totalB49 = $B49;
                    $totalB50 = $B50;
                    $totalB52 = $B52;
                    $totalB53 = $B53;
                    $totalB54 = $B54;
   
                    $totalB30 = $B30;
                    
                    $totalT21 = $T21;
                    $totalT22 = $T22;
                    $totalT23 = $T23;
                    
                    $totalT25 = $T25;
         
                    $totalT30 = ($totalB40 != 0 ) ? $totalT25/ $totalB40 : 0;
                    
                    $totalT01 = $CUMT01;
                    $totalT07 = $T07;
                    $totalT08 = $T08;
                    $totalT11 = $T11;
                    $totalT12 = $T12;
                    $totalT13 = $T13;
                    $totalT14 = $T14;
                    $totalT15 += $T15;
                    $totalT16 = $T16;
                    $totalT17 = $T17;
                    $totalT18 = $T18;
                    $totalT19 = $T19;
                    $totalT35 = $T35;
                    $totalT36 = $T36;
                    $totalT37 = $T37;
                    $totalT40 = $T40;
                    
                  
                    $totalM35 = $CUMM35;
                    $totalM46 = $M46;
                    $totalM60 = $M60;
                    $totalM65 = $M65;
                    $totalM66 = $M66;
                    $totalM67 = $M67;
                   
                   
                    $totalM55 = ($totalB10 != 0)? $totalM60 / $totalB10 :0;
                    $lastM20 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['M20']))? $cumulativeArray[$keyForm - 1][$key]['M20'] :0;
                    $cumulativeArray[$keyForm][$key]['M20'] = $lastM20+ $M20;
                    $totalM20 =  $cumulativeArray[$keyForm][$key]['M20'];
                    
                    $lastM25 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['M25']))? $cumulativeArray[$keyForm - 1][$key]['M25'] :0;
                    $cumulativeArray[$keyForm][$key]['M25'] = $lastM25+ $M25;
                    $totalM25 =  $cumulativeArray[$keyForm][$key]['M25'];
                    
              
                    $totalM40 += $M40;
                    $totalM41 = $M41;
                    $totalM42 = $M42;
                    
                    $totalH35 = $CUMH35;
                    $totalH40 += $H40;
                    $totalH41 = $H41;
                    $totalH42 = $H42;
                    $totalH46 = $H46;
                    $totalH60 = $H60;
                    
                   
                    $totalH55 = ($totalB30 != 0) ? $totalH60 / $totalB30 : 0;
                  
                    $lastH20 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['H20']))? $cumulativeArray[$keyForm - 1][$key]['H20'] :0;
                    $cumulativeArray[$keyForm][$key]['H20'] = $lastH20 + $H20;
                    $totalH20 =  $cumulativeArray[$keyForm][$key]['H20'];
                    
                    $lastH25 = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['H25']))? $cumulativeArray[$keyForm - 1][$key]['H25'] :0;
                    $cumulativeArray[$keyForm][$key]['H25'] = $lastH25+ $H25;
                    $totalH25 =  $cumulativeArray[$keyForm][$key]['H25'];
                  
                  
                    $totalH65 = $H65;
                    $totalH66 = $H66;
                    $totalH67 = $H67;
                    
                   
                    
                   
                    
                    
                    
                     
                    
                    $totalForecastedProjectBudgetTrend = (1+$totalT30) * $totalActivityPlannedBugdet;
                    $totalForecastedProjectBudgetMaterialTrend = (1+$totalM55) * $totalPlannedTotalMaterialCost;
                    $totalForecastedProjectBudgetLaborTrend = (1+$totalH55) * $totalPlannedTotalLaborCost;
                    
                    $totalForecastedProjectBudgetAreaTrend +=  (1+$totalT30) * $totalT05;
                    $totalForecastedProjectBudgetMaterialAreaTrend += (1+$totalM55) * $totalM20;
                    $totalForecastedProjectBudgetLaborAreaTrend += (1+$totalH55) * $totalH20;


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
            
     
   
     
            $this->activityArray[$keyForm]['total']['totalB10'] = $totalB10;
            $this->activityArray[$keyForm]['total']['totalB30'] = $totalB30;
            $this->activityArray[$keyForm]['total']['totalB40'] = $totalB40;
            $this->activityArray[$keyForm]['total']['totalB47'] = $totalB47;
            $this->activityArray[$keyForm]['total']['totalB49'] = $totalB49;
            $this->activityArray[$keyForm]['total']['totalB50'] = $totalB50;
            $this->activityArray[$keyForm]['total']['totalB52'] = $totalB52;
            $this->activityArray[$keyForm]['total']['totalB53'] = $totalB53;
            $this->activityArray[$keyForm]['total']['totalB54'] = $totalB54;
            $this->activityArray[$keyForm]['total']['totalM55'] = $totalM55 * 100;
            $this->activityArray[$keyForm]['total']['totalM40'] = $totalM40;
            $this->activityArray[$keyForm]['total']['totalM41'] = $totalM41;
            $this->activityArray[$keyForm]['total']['totalM42'] = $totalM42;
            $this->activityArray[$keyForm]['total']['totalH15'] = $totalH15;
            $this->activityArray[$keyForm]['total']['totalH30'] = $totalH30;
            $this->activityArray[$keyForm]['total']['totalH35'] = $totalH35;
            $this->activityArray[$keyForm]['total']['totalH46'] = $totalH46;
            $this->activityArray[$keyForm]['total']['totalH40'] = $totalH40;
            $this->activityArray[$keyForm]['total']['totalH41'] = $totalH41;
            $this->activityArray[$keyForm]['total']['totalH42'] = $totalH42;
            $this->activityArray[$keyForm]['total']['totalH55'] = $totalH55 * 100;
            $this->activityArray[$keyForm]['total']['totalH25'] = $totalH25;
            $this->activityArray[$keyForm]['total']['totalH68'] = $totalH68;
            $this->activityArray[$keyForm]['total']['totalH69'] = $totalH69;
            $this->activityArray[$keyForm]['total']['totalT10'] = $totalT10;
            $this->activityArray[$keyForm]['total']['totalT15'] = $totalT15;
            $this->activityArray[$keyForm]['total']['totalT16'] = $totalT16;
            $this->activityArray[$keyForm]['total']['totalT17'] = $totalT17;
            $this->activityArray[$keyForm]['total']['totalT21'] = $totalT21;
            $this->activityArray[$keyForm]['total']['totalT22'] = $totalT22;
            $this->activityArray[$keyForm]['total']['totalT23'] = $totalT23;
            $this->activityArray[$keyForm]['total']['totalT25'] = $totalT25;
            $this->activityArray[$keyForm]['total']['totalT30'] = $totalT30 * 100;
            $this->activityArray[$keyForm]['total']['totalT35'] = $totalT35;
            $this->activityArray[$keyForm]['total']['totalT36'] = $totalT36;
            $this->activityArray[$keyForm]['total']['totalT37'] = $totalT37;
            $this->activityArray[$keyForm]['total']['totalT38'] = $totalT38;
            $this->activityArray[$keyForm]['total']['totalT39'] = $totalT39;
            $this->activityArray[$keyForm]['total']['totalT01'] = $totalT01;
            $this->activityArray[$keyForm]['total']['totalT05'] = $totalT05;
            $this->activityArray[$keyForm]['total']['totalT06'] = $totalT06;
            $this->activityArray[$keyForm]['total']['totalT07'] = $totalT07;
            $this->activityArray[$keyForm]['total']['totalT08'] = $totalT08;
            $this->activityArray[$keyForm]['total']['totalT11'] = $totalT11;
            $this->activityArray[$keyForm]['total']['totalT12'] = $totalT12;
            $this->activityArray[$keyForm]['total']['totalT13'] = $totalT13;
            $this->activityArray[$keyForm]['total']['totalT14'] = $totalT14 * 100;
            $this->activityArray[$keyForm]['total']['totalT18'] = $totalT18;
            $this->activityArray[$keyForm]['total']['totalT19'] = $totalT19 * 100;
            $this->activityArray[$keyForm]['total']['totalT40'] = $totalT40 * 100;
            $this->activityArray[$keyForm]['total']['totalT41'] = $totalT41;
            $this->activityArray[$keyForm]['total']['totalT42'] = $totalT42;
            $this->activityArray[$keyForm]['total']['totalM15'] = $totalM15;
            $this->activityArray[$keyForm]['total']['totalM20'] = $totalM20;
            $this->activityArray[$keyForm]['total']['totalM30'] = $totalM30;
            $this->activityArray[$keyForm]['total']['totalM35'] = $totalM35;
            $this->activityArray[$keyForm]['total']['totalM46'] = $totalM46;
            $this->activityArray[$keyForm]['total']['totalM60'] = $totalM60;
            $this->activityArray[$keyForm]['total']['totalM65'] = $totalM65;
            $this->activityArray[$keyForm]['total']['totalM66'] = $totalM66;
            $this->activityArray[$keyForm]['total']['totalM67'] = $totalM67;
            $this->activityArray[$keyForm]['total']['totalM68'] = $totalM68;
            $this->activityArray[$keyForm]['total']['totalM69'] = $totalM69;
            $this->activityArray[$keyForm]['total']['totalM25'] = $totalM25;
            $this->activityArray[$keyForm]['total']['totalH20'] = $totalH20;
            $this->activityArray[$keyForm]['total']['totalH60'] = $totalH60;
            $this->activityArray[$keyForm]['total']['totalH65'] = $totalH65;
            $this->activityArray[$keyForm]['total']['totalH66'] = $totalH66;
            $this->activityArray[$keyForm]['total']['totalH67'] = $totalH67;
            $this->activityArray[$keyForm]['total']['totalI10'] = $totalI10;
            $this->activityArray[$keyForm]['total']['totalI01'] = $totalI01;
            
            
            $this->activityArray[$keyForm]['total']['totalCUMH15'] = $totalCUMH15;
            $this->activityArray[$keyForm]['total']['totalCUMH30'] = $totalCUMH30;
            $this->activityArray[$keyForm]['total']['totalCUMM15'] = $totalCUMM15;
            $this->activityArray[$keyForm]['total']['totalCUMM30'] = $totalCUMM30;
            $this->activityArray[$keyForm]['total']['totalCUMI01'] = $totalCUMI01;
            $this->activityArray[$keyForm]['total']['totalCUMI10'] = $totalCUMI10;
            
            
            
            
            
        

            
            
            

            $this->laborActivityArray[$keyForm]['total']['totalForecastedTotalLaborCost'] = $totalForecastedTotalLaborCost;
            $this->laborActivityArray[$keyForm]['total']['totalActualLaborCost'] = $totalActualLaborCost;
            $this->laborActivityArray[$keyForm]['total']['totalPlannedLaborCostPerActualMaterial'] = $totalPlannedLaborCostPerActualMaterial;
            

            $this->quantityActivityArray[$keyForm]['total']['totalPlannedCostMaterial'] = $totalPlannedCostMaterial;
           
            $this->quantityActivityArray[$keyForm]['total']['totalActualCostMaterial'] = $totalActualCostMaterial;
            $this->quantityActivityArray[$keyForm]['total']['totalForecastedTotalMaterialCost'] = $totalForecastedTotalMaterialCost;


            $this->scheduleActivityArray[$keyForm]['total']['totalPlannedCompletedQtyMaterialPerActualLaborHours'] = $totalPlannedCompletedQtyMaterialPerActualLaborHours;
            $this->scheduleActivityArray[$keyForm]['total']['totalB45'] = $totalB45;
            $this->scheduleActivityArray[$keyForm]['total']['totalActualQuantityMaterial'] = $totalActualQuantityMaterial;
            $this->scheduleActivityArray[$keyForm]['total']['totalForecastedTotalLaborHours'] = $totalForecastedTotalLaborHours;
            
            





        }

  }


  public function getlaborCost()
  {

    $formsArray = array();

    $totalForecastedTotalLaborCost = 0;
    $totalActualLaborCost = 0 ;
    $totalPlannedLaborCostPerActualMaterial = 0 ;

    foreach ($this->forms as $keyForm => $form) {
      $formsArray['details'][$keyForm] = $form;
      $formsArray['details'][$keyForm]['data'] = $this->laborActivityArray[$keyForm];
       }

    return $formsArray;

  }

  public function getQuantity()
  {

    $formsArray = array();

    $totalForecastedTotalLaborCost = 0;
    $totalActualLaborCost = 0 ;
    $totalPlannedLaborCostPerActualMaterial = 0 ;

    foreach ($this->forms as $keyForm => $form) {
      $formsArray['details'][$keyForm] = $form;
      $formsArray['details'][$keyForm]['data'] = $this->quantityActivityArray[$keyForm];
       }

    return $formsArray;

  }

  public function getSchedule()
  {

    $formsArray = array();

    $totalForecastedTotalLaborCost = 0;
    $totalActualLaborCost = 0 ;
    $totalPlannedLaborCostPerActualMaterial = 0 ;

    foreach ($this->forms as $keyForm => $form) {
      $formsArray['details'][$keyForm] = $form;
      $formsArray['details'][$keyForm]['data'] = $this->scheduleActivityArray[$keyForm];
       }

    return $formsArray;

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


  public function getLastDay()
  {

    $formsArray = array();

      $formsArray= $this->activityArray[count($this->forms) - 1];
    
    
    return $formsArray;
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
                  ->whereNotNull('areas.id')->where('project_division_id', $division_id)->get();
            
          $totalB46 = 0;
   
          
          $benchmarkPerActivityArr = array();
          
          foreach($benchmarks as $benchmark)
          {
              
              $hours_unit = 0;
                    if(isset($benchmark->hours_unit)) { $hours_unit = $benchmark->hours_unit;}
                    $plannedHoursPerUnit = $hours_unit; //rename $plannedHoursPerUnit_B15 
                    
             $totalB46 += $benchmark->quantity * $plannedHoursPerUnit;
             
          }
          
          $benchmarkPerActivityArr['totalB46'] = $totalB46;
        
          
          return $benchmarkPerActivityArr;
      }
      
      
      public function calcTotalBenchmarkCost($benchmark_id,$activityId,$areaId)
      {
          
          $cost = array();
          $benchmarks = BenchmarkDetail::where('benchmark_details.benchmark_id',$this->benchmarkId);
        
           
                if(isset($activityId)){
                    $benchmarks->where('benchmark_details.project_division_id',$activityId);
                }
                if(isset($areaId)){
                    $benchmarks->where('benchmark_details.area_id',$areaId);
                }
        
          $benchmarks = $benchmarks->leftJoin('areas','areas.id','=','benchmark_details.area_id')
                  ->whereNotNull('areas.id')->leftJoin('project_divisions','project_divisions.id','=','benchmark_details.project_division_id')->whereNotNull('project_divisions.id')->get();
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
                    $plannedHoursPerUnit = $hours_unit; //rename $plannedHoursPerUnit_B15 
                    
                    $B20 = $benchmark->unit_labor_hour; //rename $plannedLaborCostPerHour_B20
                    
                    $plannedLaborCost = $plannedHoursPerUnit * $B20; //rename $plannedLaborUnitCost_B25
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
