<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FAQAccordion extends Model
{
    use SoftDeletes;
    protected $table = 'f_a_q_accordions';
    protected $fillable = ['question', 'answer'];
}
