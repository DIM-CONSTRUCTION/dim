<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FormDetail;
use App\BenchmarkDetail;
use App\Form;


class Graph
{
  public $totalPlannedBugdet;
  public $totalActualBudgetDeviated ;
  public $totalForecastBudgetTrend;

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


  public function __construct($benchmarkId,$date,$activityId)
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
    

    foreach ($this->forms as $keyForm => $form) {

        //actual
        if(isset($activityId)){
            $activities = FormDetail::where('form_id',$form->id)->where('division_id',$activityId)->get();
        }
        else{
            $activities = FormDetail::where('form_id',$form->id)->get();
        }

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
        $totalPlannedTotalLaborHour= 0;


        foreach ($activities as $key => $activity) {
                $benchmarkArr = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)->where('project_division_id', $activity->division_id)->first();
                if(isset($benchmarkArr))
                {
                    $unit_material_rate = 0;
                    $hours_unit = 0;
                    $quantity = 0;
                    if(isset($benchmarkArr->unit_material_rate))
                    {
                        $unit_material_rate = $benchmarkArr->unit_material_rate;
                    }
                    
                   if(isset($benchmarkArr->hours_unit))
                    {
                        $hours_unit = $benchmarkArr->hours_unit;
                    }
                    
                    if(isset($activity->quantity))
                    {
                        $quantity = $activity->quantity;
                    }
                  
                    $plannedMaterialUnitRate = $unit_material_rate; //values
                    $plannedHoursPerUnit = $hours_unit; //values
                    $plannedLaborCostPerHour = $benchmarkArr->unit_labor_hour; //values


                    $plannedTotalQuantity = BenchmarkDetail::where('benchmark_id',$this->benchmarkId)->where('project_division_id', $activity->division_id)->sum('quantity'); //values

                    $plannedTotalMaterialCost = $plannedTotalQuantity * $plannedMaterialUnitRate;

                    $plannedLaborCost = $plannedHoursPerUnit * $plannedLaborCostPerHour;

                    $plannedTotalLaborCost = $plannedTotalQuantity * $plannedLaborCost;

                    $plannedTotalBudget = $plannedTotalMaterialCost + $plannedTotalLaborCost;
                    
                    $lastQty = ($keyForm!=0 && isset($cumulativeArray[$keyForm - 1][$key]['quantity']))? $cumulativeArray[$keyForm - 1][$key]['quantity'] :0;
                    $lastPerc = ($keyForm!=0  && isset($cumulativeArray[$keyForm - 1][$key]['percentage_completed']))? $cumulativeArray[$keyForm - 1][$key]['percentage_completed'] :0;
                    $lastWorkingHours = ($keyForm!=0  && isset($cumulativeArray[$keyForm - 1][$key]['working_hours']))? $cumulativeArray[$keyForm - 1][$key]['working_hours'] :0;

                    $cumulativeArray[$keyForm][$key]['activity'] = $benchmarkArr->id;
                    $cumulativeArray[$keyForm][$key]['quantity'] = $lastQty+ $quantity;
                    $cumulativeArray[$keyForm][$key]['percentage_completed'] =  $lastPerc  + $activity->percentage_completed / 100;
                    $cumulativeArray[$keyForm][$key]['working_hours'] =  $lastWorkingHours + $activity->working_hours;

                    $cumulativeQuantity =  $cumulativeArray[$keyForm][$key]['quantity'];
                    $cumulativePercentage = $cumulativeArray[$keyForm][$key]['percentage_completed'] ;
                    $cumulativeWorkingHours = $cumulativeArray[$keyForm][$key]['working_hours'] ;

                    $actualQuantityPerUnit = $cumulativeQuantity;
                    $actualExecutedWorkPercentage = $cumulativePercentage;

                    $PlannedCompletedUnitsPerBOQQuantity = $plannedTotalQuantity * $actualExecutedWorkPercentage;

                    $actualUnitsQuantityDeviationComparedToPlanned = $actualQuantityPerUnit - $PlannedCompletedUnitsPerBOQQuantity;
                    $actualUnitsDeviationCost = $actualUnitsQuantityDeviationComparedToPlanned * $plannedMaterialUnitRate;

                    $actualGangHours = $cumulativeWorkingHours;

                    $plannedGangQuantityPerActualExecutedUnits = $actualQuantityPerUnit * $plannedHoursPerUnit;
                    $actualHoursDeviation = $actualGangHours - $plannedGangQuantityPerActualExecutedUnits;

                    $actualHoursDeviationCost = $plannedLaborCostPerHour * $actualHoursDeviation;
                    $actualTotalDeviated = $actualUnitsDeviationCost + $actualHoursDeviationCost;

                    $actualBudgetDeviated = $plannedTotalBudget + $actualTotalDeviated;


                    $plannedBudget = $plannedTotalBudget;


                    $actualPercentageDeviation = ($PlannedCompletedUnitsPerBOQQuantity != 0)? $actualUnitsQuantityDeviationComparedToPlanned / $PlannedCompletedUnitsPerBOQQuantity : 0 ;
                    $forecastedDeviationMaterialCost = $actualPercentageDeviation * $plannedTotalMaterialCost;
                    $forecastedTotalMaterialCost = $forecastedDeviationMaterialCost + $plannedTotalMaterialCost;

                    $actualPercentageDeviationHour = ($plannedGangQuantityPerActualExecutedUnits != 0)? $actualHoursDeviation / $plannedGangQuantityPerActualExecutedUnits : 0 ;
                    $forecastedDeviationCostHour = $plannedTotalLaborCost * $actualPercentageDeviationHour;
                    $forecastedTotalLaborHour = $plannedTotalLaborCost + $forecastedDeviationCostHour;
                    $forecastBudgetTrend = $forecastedTotalMaterialCost + $forecastedTotalLaborHour;

                    $actualCostHours = $plannedLaborCostPerHour *  $actualGangHours;
                    $plannedCostHours = $plannedGangQuantityPerActualExecutedUnits;
                    $forecastedTotalLaborCost = $forecastedDeviationCostHour + $plannedTotalLaborCost;
                    $actualCostMaterial =  $plannedMaterialUnitRate * $actualQuantityPerUnit;
                    $plannedCostMaterial = $PlannedCompletedUnitsPerBOQQuantity * $plannedMaterialUnitRate;
                    $plannedCompletedQtyMaterialPerActualLaborHours = ($plannedHoursPerUnit != 0)? $actualGangHours/$plannedHoursPerUnit :0;
                    $plannedTotalLaborHour = $plannedHoursPerUnit * $plannedTotalQuantity;
                    $forecastedDeviationLaborHourDelay = $plannedTotalLaborHour * $actualPercentageDeviation;
                    $forecastedTotalLaborHours = $plannedTotalLaborHour - $forecastedDeviationLaborHourDelay;




                    $totalActivityPlannedBugdet +=  $plannedBudget;
                    $totalActivityActualBudgetDeviated +=  $actualBudgetDeviated;
                    $totalActivityForecastBudgetTrend +=  $forecastBudgetTrend * $plannedLaborCostPerHour;


                    $totalForecastedTotalLaborCost += $forecastedTotalLaborCost;
                    $totalActualLaborCost += $actualCostHours;
                    $totalPlannedLaborCostPerActualMaterial+= $plannedCostHours;
                    $totalPlannedTotalLaborCost+= $plannedTotalLaborCost;

                    $totalPlannedCostMaterial+= $plannedCostMaterial;
                    $totalPlannedTotalMaterialCost += $plannedTotalMaterialCost;
                    $totalActualCostMaterial += $actualCostMaterial;
                    $totalForecastedTotalMaterialCost += $forecastedTotalMaterialCost;

                    $totalPlannedCompletedQtyMaterialPerActualLaborHours+= $plannedCompletedQtyMaterialPerActualLaborHours;
                    $totalActualQuantityMaterial += $actualQuantityPerUnit;
                    $totalForecastedTotalLaborHours += $forecastedTotalLaborHours;
                    $totalPlannedTotalLaborHour += $plannedTotalLaborHour;


                }

            }

            $this->activityArray[$keyForm]['total']['totalForecastBudgetTrend'] = $totalActivityForecastBudgetTrend;
            $this->activityArray[$keyForm]['total']['totalPlannedBugdet'] = $totalActivityPlannedBugdet;
            $this->activityArray[$keyForm]['total']['totalActualBudgetDeviated'] = $totalActivityActualBudgetDeviated;

            $this->laborActivityArray[$keyForm]['total']['totalForecastedTotalLaborCost'] = $totalForecastedTotalLaborCost;
            $this->laborActivityArray[$keyForm]['total']['totalActualLaborCost'] = $totalActualLaborCost;
            $this->laborActivityArray[$keyForm]['total']['totalPlannedLaborCostPerActualMaterial'] = $totalPlannedLaborCostPerActualMaterial;
            $this->laborActivityArray[$keyForm]['total']['totalPlannedTotalLaborCost'] = $totalPlannedTotalLaborCost;

            $this->quantityActivityArray[$keyForm]['total']['totalPlannedCostMaterial'] = $totalPlannedCostMaterial;
            $this->quantityActivityArray[$keyForm]['total']['totalPlannedTotalMaterialCost'] = $totalPlannedTotalMaterialCost;
            $this->quantityActivityArray[$keyForm]['total']['totalActualCostMaterial'] = $totalActualCostMaterial;
            $this->quantityActivityArray[$keyForm]['total']['totalForecastedTotalMaterialCost'] = $totalForecastedTotalMaterialCost;


            $this->scheduleActivityArray[$keyForm]['total']['totalPlannedCompletedQtyMaterialPerActualLaborHours'] = $totalPlannedCompletedQtyMaterialPerActualLaborHours;
            $this->scheduleActivityArray[$keyForm]['total']['totalPlannedTotalLaborHour'] = $totalPlannedTotalLaborHour;
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

    $totalPlannedBugdet = 0;
    $totalActualBudgetDeviated = 0 ;
    $totalForecastBudgetTrend = 0 ;
    foreach ($this->forms as $keyForm => $form) {
      $formsArray['details'][$keyForm] = $form;
      $formsArray['details'][$keyForm]['data'] = $this->activityArray[$keyForm];
    }
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
}
