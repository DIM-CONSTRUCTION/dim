<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
class MobileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function getProjectsList(Request $request)
     {


       $projects = Project::all();

       return response(['projects'=>$projects]);
     }
     public function getReportList(Request $request)
     {
       $forms= Form::all();
       return response(['forms'=>$forms]);
     }
     public function getActivtiesList(Request $request)
     {

       $form = Form::where('id',$request->form_id)->first();
       $activityArray = array();
       $activities = FormDetail::where('form_id',$request->form_id)->get();
       foreach ($activities as $key => $activity) {
           $activityArray[$key]['data'] = $activity;
           $activityArray[$key]['division'] = self::getFunction($form->project_id,'project_divisions',$activity->division_id);
           $activityArray[$key]['area'] = self::getFunction($form->project_id,'areas',$activity->division_id);
       }

       return response(['activity'=>$activityArray]);
     }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
