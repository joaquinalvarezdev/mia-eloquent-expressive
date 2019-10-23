<?php namespace Mobileia\Expressive\Database\Model;

/**
 * Description of CacheInter
 *
 * @author matiascamiletti
 */
class CacheInter extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'cache_inter';
    
    protected $casts = ['data' => 'array'];
    
    public $timestamps = false;
}