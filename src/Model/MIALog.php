<?php

namespace Mobileia\Expressive\Database\Model;

/**
 * Description of MIALog
 *
 * @author matiascamiletti
 */
class MIALog extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'mia_log';
    
    protected $casts = ['data' => 'array'];
    
    //public $timestamps = false;
}