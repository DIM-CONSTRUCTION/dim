<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Controller;
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
use App\Specialty;
use App\Benchmark;
use App\Area;
use App\Project;
use App\Form;
use Illuminate\Support\Str;
use App\FormDetail;
use App\Labor;
use App\Equip;
use App\FdLabor;
use App\FdEquip;
use Illuminate\Support\Facades\Storage;
use App\LaborWorkType;

class MobileController extends Controller
{



   var $parentDivArr = array();
   var $parentAreaArr = array();

  

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


    public function getProjectsList(Request $request)
    {
      $projects = Project::all();
      return response(['projects'=>$projects]);
    }
    public function getReportList(Request $request)
    {
    
      $forms= Form::where('project_id',$request->project_id)->get();
      return response(['forms'=>$forms]);
    }
    
     public function getAreaList(Request $request)
    {
        $form = Form::where('id',$request->form_id)->first();
        $area = self::getFunction($form->project_id,'areas',$form->id);
        
         return response(['area'=>$area]);
    }

    public function getActivitiesList(Request $request)
    {

      $form = Form::where('id',$request->form_id)->first();
      $activityArray = array();
    
      $division = self::getFunction($form->project_id,'project_divisions',$form->id);
     
     
        
      return response(['division'=>$division]);
    }
    
    public function getGangDetails(Request $request)
    {

      $gang = Labor::all();
      $specialty = LaborWorkType::all();
    
        
      return response(['gang'=>$gang,'specialties'=>$specialty]);
    }
    
     public function getEquipmentDetails(Request $request)
    {

      $equipments = Equip::all();
  
      return response(['equipments'=>$equipments]);
    }
    
    
    
    public function getFormsList(Request $request)
    {
        
        $forms= Form::where('project_id',$request->project_id)->orderBy('id', 'desc')->limit(15)->get();
        foreach ($forms as $keyForm => $form) {
            $forms[$keyForm]= $form;
            
            $activities = FormDetail::where('form_id',$form->id)->orderBy('id', 'desc')->get();
            $cumulative = 0;
              foreach ($activities as $key => $activity) {

                  $cumulative += $activity->percentage_completed;
                  $activityArray[$key] = $activity;
                  $activityArray[$key]['cumulative'] =  $cumulative - $activity->percentage_completed;
                  $activityArray[$key]['area'] = self::getFunctionName($form->project_id,'areas',$activity->area_id);
                  $activityArray[$key]['division'] = self::getFunctionName($form->project_id,'project_divisions',$activity->division_id);
                  
                  if(isset($activity))
                    {
                          $equipment = FdEquip::where('form_details_id',$activity->id)
                                        ->leftJoin('equips','equips.id','=','fd_equips.equip_id')
                                        ->select('fd_equips.*','equips.name')
                                        ->get();
                                        
                            $activityArray[$key]['equipments'] =  $equipment;          
                                        
                                            
                             $labor = FdLabor::where('form_details_id',$activity->id)
                                ->leftJoin('labors','labors.id','=','fd_labors.labor_id')
                                ->leftJoin('labor_work_types','labor_work_types.id','=','fd_labors.labor_work_type_id')
                                ->select('fd_labors.*','labors.full_name','labor_work_types.name')
                                ->get();
                                
                           $activityArray[$key]['gangs'] =  $labor;      
                    }
                
        
              }
              
              


            $forms[$keyForm]['form_details'] = $activityArray;
            
        }
        $divisions = self::getFunction($request->project_id,'project_divisions',0);
        $areas = self::getFunction($request->project_id,'areas',0);
        $equipments = Equip::all();
        $gang = Labor::all();
        $specialty = LaborWorkType::all();
        $benchamrk = Benchmark::where('project_id',$request->project_id)->orderBy('id', 'desc')->first();
        
        $benchamrkDetails  = BenchmarkDetail::where('benchmark_details.benchmark_id',$benchamrk->id)
        ->where('benchmark_details.quantity','>','0')
        ->leftJoin('project_divisions','project_divisions.id','=','benchmark_details.project_division_id')
        ->leftJoin('areas','areas.id','=','benchmark_details.area_id')
        ->whereNotNull('areas.id')
        ->whereNotNull('project_divisions.id')
        ->select('benchmark_details.*')->get();
        foreach ($benchamrkDetails as $key => $benchamrkDetail) {
            $benchamrkDetails[$key] = $benchamrkDetail;
            $division = ProjectDivision::where('id',$benchamrkDetail->project_division_id)->first();
            $area = Area::where('id',$benchamrkDetail->area_id)->first();
            $benchamrkDetails[$key]['cumulative'] = FormDetail::where('area_id',$benchamrkDetail->area_id)->where('division_id',$benchamrkDetail->division_id)->sum('percentage_completed');
            $benchamrkDetails[$key]['division'] = $division;
            $benchamrkDetails[$key]['area'] = $area;
            $benchamrkDetails[$key]['parent_name'] = array_values(array_filter($this->parentDivArr, function ($key) use ($division) {
                                                            return $division->parent_id == $key['id'];
                                                        }))[0]['parent_name'];
             
            $parent_name =  array_values(array_filter($this->parentAreaArr, function ($key) use ($area) {
                                                            return $area->parent_id == $key['id'];
                                                        }));                                         
            $benchamrkDetails[$key]['area']['name'] = (isset($parent_name[0]))?$parent_name[0]['parent_name']." » ".$area->name:$area->name;
            
        }
        
        
        return response(['forms'=>$forms,'divisions'=>$divisions,'areas'=>$areas,'equipments'=>$equipments,'gang'=>$gang,'specialties'=>$specialty,'activities'=>$benchamrkDetails]);
        
    }
    
    public function getFormActivitiesList(Request $request)
    {
      $form = Form::where('id',$request->form_id)->first();
     // $activitiesIds = FormDetail::where('form_id',$form->id)->pluck('division_id')->toArray();

     // $activities = ProjectDivision::where('project_id',$form->project_id)->where('parent_id',$request->division_id)->get();
      
    
      $activityArray = array();


      $activities = FormDetail::where('form_id',$request->form_id)->get();

      foreach ($activities as $key => $activity) {

          $activityArray[$key] = ProjectDivision::where('id',$activity->division_id)->first();
          $activityArray[$key]['area'] = self::getFunctionName($form->project_id,'areas',$activity->area_id);
          $activityArray[$key]['division'] = self::getFunctionName($form->project_id,'project_divisions',$activity->division_id);

      }



      

      return response(['activities'=>$activityArray]);
    }
    
  
    
    public function setDailyReport(Request $request)
    {

      $form = Form::insert(['project_id'=>$request->project_id,'title'=>$request->title,'date'=>$request->date]);

      return response(['form'=>$form]);
    }



    function getFunction($project_id,$type,$id)
    {
      $areasArray = array();
      
      if($type == 'project_divisions')
      {
          $activitiesIds = FormDetail::where('form_id',$id)->pluck('division_id')->toArray();
         
      }
      if($type == 'areas')
      {
          $activitiesIds = FormDetail::where('form_id',$id)->pluck('area_id')->toArray();
         
      }
      
      
      
      $dataTypeContent = DB::table($type)->where('project_id',$project_id)->whereNull('parent_id')->get();

      foreach ($dataTypeContent as $key => $item) {
        $areaList = DB::table($type)->where('project_id',$item->project_id)->where('parent_id',$item->id)->get();
        
           if($type == 'areas')
            {
                  $areaName = $item->name;
                  array_push($this->parentAreaArr,array('id'=>$item->id,'parent_name'=>$areaName));
            }
            else{
                  $areaName = $item->title;
                  array_push($this->parentDivArr,array('id'=>$item->id,'parent_name'=>$areaName));
            }
            
               if(count($areaList) == 0)
              {
                 $areasArray[$key] = $item;
                  
                 $item->children = null;
                 $item->parent_name = $areaName;
              
              }
              else{
        
                $areasArray[$key] = $item;
               
                $item->parent_name = $areaName;
                $item->children = self::functionList($areasArray,$areaList,$type,$activitiesIds,$areaName);
              }
           
           
         
        // code...
      }

      return $areasArray;
    }
     function functionList($areasArray,$list,$type,$activitiesIds,$name)
    {
      $areasName = '';
      $areasArray = array();
      $index = 0;
      foreach ($list as $key => $item) {
          $areaList = DB::table($type)->where('project_id',$item->project_id)->where('parent_id',$item->id)->get();
          

                  if($type == 'project_divisions')
                  {
                      if(count($areaList) > 0)
                      {
                           $areaName = $name.' » '.$item->title;
                          $areasArray[$index] = $item;
                            $children = self::functionList($areasArray,$areaList,$type,$activitiesIds,$areaName);
                                if(count($children) != 0)
                                {
                                    $item->children = $children;
                                }
                              else{
                                    $item->children = null;
                                }
                                
                                $item->parent_name = $areaName;
                              
                                $index++;
                              array_push($this->parentDivArr,array('id'=>$item->id,'parent_name'=>$areaName));
                      }
                  }
                  else{
                       
                             $areaName = $name.' » '.$item->name;
                             $areasArray[$index] = $item;
                              $children = self::functionList($areasArray,$areaList,$type,$activitiesIds,$areaName);
                                if(count($children) != 0)
                                {
                                    $item->children = $children;
                                }
                              else{
                                    $item->children = null;
                                }
                                $item->parent_name = $areaName;
                                
                                $index++;
                                 array_push($this->parentAreaArr,array('id'=>$item->id,'parent_name'=>$areaName));
                  }
                  
                 
           
            
          
         
        }
        
        
        return $areasArray;
    }





    function getFunctionName($project_id,$type,$id)
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

            $areasArray = self::functionNameList($areasArray,$areaList,$areaName,$type,$id);
          }
        // code...
      }

      return $areasArray;
    }
    function functionNameList($areasArray,$list,$name,$type,$id)
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
                $areaArray['title'] = $item->title;
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

            $areasArray = self::functionNameList($areasArray,$areaList,$areaName,$type,$id);
          }
        }
        return $areasArray;
    }




  public function getFormActivity(Request $request)
    {

        $formDetail = FormDetail::where('form_id',$request->form_id)->where('division_id',$request->division_id)->where('area_id',$request->area_id)->first();
        $equipment = array();
        $labor = array();
        
        if(isset($formDetail))
        {
              $equipment = FdEquip::where('form_details_id',$formDetail->id)
                            ->leftJoin('equips','equips.id','=','fd_equips.equip_id')
                            ->select('fd_equips.*','equips.name')
                            ->get();
                            
                                
             $labor = FdLabor::where('form_details_id',$formDetail->id)
                ->leftJoin('labors','labors.id','=','fd_labors.labor_id')
                ->leftJoin('labor_work_types','labor_work_types.id','=','fd_labors.labor_work_type_id')
                ->select('fd_labors.*','labors.full_name','labor_work_types.name')
                ->get();
        }
      


      return response(['equipment'=>$equipment,'gang'=>$labor,'form'=>$formDetail]);

    }
    
    
     public function setFormActivity(Request $request)
    {
        
        
             DB::beginTransaction();
             try {
                 $form = json_decode($request->form,true);
                 
                   if($form['id'] == 0)
                    {
                        $formId = Form::insertGetId(['project_id'=>$form['project_id'],'title'=>$form['title'],'date'=>$form['date']]);
                    }
                    else{
                        $formId = $form['id'];
                    }
                

                 if(isset($form['form_details']))
                 {
                        $form_details_array = $form['form_details'];
                        
                        foreach($form_details_array as $form_details)
                       {
                        
                         $filesPath = [];
        
                
                          if (isset($form_details['image'])) {
                             foreach($form_details['image'] as $fileset)
                               {
                                    $base =  $fileset;
                                    $image=base64_decode($base);
                                    $imageName = Str::random(40).'.'.'jpg';
                                    Storage::disk('local')->put('public/form-details/Api/'.$imageName, $image, 'public');
                                    array_push($filesPath,'form-details/Api/'.$imageName);
                               }
                            
                         
                        }
                    
                         $filesPath = json_encode($filesPath);
                    
                        if($form_details['id'] == 0)
                        {
                             $formDetailId = FormDetail::insertGetId(['division_id'=>$form_details['division_id'],'area_id'=>$form_details['area_id'],'quantity'=>$form_details['quantity'],'form_id'=>$formId,'percentage_completed'=>$form_details['percentage_completed'],'working_hours'=>$form_details['working_hours'],'extra_hours'=>$form_details['extra_hours'],'note'=>$form_details['note'],'images'=>$filesPath]);
                        }
                        else{
                              $formDetailId =  $form_details['id'];
                             FormDetail::where('id',$formDetailId)->update(['percentage_completed'=>$form_details['percentage_completed'],'quantity'=>$form_details['quantity'],'working_hours'=>$form_details['working_hours'],'extra_hours'=>$form_details['extra_hours'],'note'=>$form_details['note'],'images'=>$filesPath]);
                    
                        }
                        
                       if(isset($form_details['gangs']))
                         {
                           $gangs = $form_details['gangs'];
                            foreach($gangs as $gang)
                            {
                                if(FdLabor::where('form_details_id',$formDetailId)->where('labor_id',$gang['labor_id'])->doesntExist())
                                  {
                                    $fdLabor_id = FdLabor::insertGetId(['labor_id'=>$gang['labor_id'],'form_details_id'=>$formDetailId,
                                    'hours_of_work'=>$gang['hours_of_work'],'extra_hours_of_work'=>$gang['extra_hours_of_work'],'labor_work_type_id'=>$gang['labor_work_type_id'],'created_at'=>date('Y-m-d H:i:s')]);
                                  }
                                  else{
                                     $fdLabor_id = FdLabor::where('form_details_id',$formDetailId)->where('labor_id',$gang['labor_id'])->update([
                                         'hours_of_work'=>$gang['hours_of_work'],'extra_hours_of_work'=>$gang['extra_hours_of_work'],'labor_work_type_id'=>$gang['labor_work_type_id'],'updated_at'=>date('Y-m-d H:i:s')]);
                                  }
                            }
                            
                        }
                        
                         if(isset($form_details['equipments']))
                        {
                            $equipments = $form_details['equipments'];
                            
                            foreach($equipments as $equipment)
                            {
                                
                                if(FdEquip::where('form_details_id',$formDetailId)->where('equip_id',$equipment['equip_id'])->doesntExist())
                                      {
                                       $FdEquip_id = FdEquip::insertGetId(['equip_id'=>$equipment['equip_id'],'form_details_id'=>$formDetailId,
                                          'hours_of_use'=>$equipment['hours_of_use'],'created_at'=>date('Y-m-d H:i:s')]);
                                  }
                                  else{
                                      $FdEquip_id = FdEquip::where('form_details_id',$formDetailId)->where('equip_id',$equipment['equip_id'])->update([
                                          'hours_of_use'=>$equipment['hours_of_use'],'updated_at'=>date('Y-m-d H:i:s')]);
                                  }
                            }
                        }
                      }
                 }
                
          DB::commit();      
        } catch (\Exception $ex) {
            DB::rollback();
            return response(['result'=>'0','message'=>$ex->getMessage()]);
        }    
                

      
            
       
      return response(['result'=>'1','message'=>'success']);
    }

    public function updateLabor(Request $request)
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






}
