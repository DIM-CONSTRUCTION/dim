<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GraphData 
{
    
    public $seriesname;
    public $name;
    public $description;
    public $color;
    public $dashed;
    public $is_displayed;
    
    
    public function __construct($seriesname,$name,$description,$color,$dashed,$is_displayed)
    {
        
        $this->seriesname = $seriesname;
        $this->name = $name;
        $this->description = $description;
        $this->color = $color;
        $this->dashed = $dashed;
        $this->is_displayed = $is_displayed;
        
    }
}