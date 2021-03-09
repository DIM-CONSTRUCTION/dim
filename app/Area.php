<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Area extends Model
{
     public function save(array $options = [])
    {
      
        if(!isset($this->project_id)){
            $this->project_id = \Session::get('project')->id;
        }
        parent::save();
    }
}
