<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Spatial;

class CompanyInfo extends Model
{
    use Spatial;
    protected $spatial = ['coordinates'];
}
