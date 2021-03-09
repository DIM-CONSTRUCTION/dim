<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Spatial;

class Project extends Model
{
    use Spatial;
    protected $spatial = ['coordinates'];
    protected $hidden = ['coordinates'];
    
    public function delete(array $options = [])
    {
        
        $project = \Session::get('project');
          if($this->id == $project->id)
          {
                $project = Project::first();
                 \Session::put('project',$project);
          }
     
       
        parent::delete();
    }

}
