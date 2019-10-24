<?php namespace Mobileia\Expressive\Database\Repository;

use \Illuminate\Database\Capsule\Manager as DB;

/**
 * Description of CacheInterRepository
 *
 * @author matiascamiletti
 */
class CacheInterRepository
{
    /**
     * 
     * @param string $key
     * @return boolean
     */
    public static function has($key)
    {
        $row = self::get($key);
        if($row === null){
            return false;
        }
        return true;
    }
    /**
     * 
     * @param string $key
     * @return \Mobileia\Expressive\Database\Model\CacheInter
     */
    public static function get($key)
    {
        $row = self::getIntern($key);
        if($row === null){
            return null;
        }
        return $row->data;
    }
    /**
     * 
     * @param string $key
     * @return CacheIntern|null
     */
    public static function getIntern($key)
    {
        return \Mobileia\Expressive\Database\Model\CacheInter::
                where('key_name', $key)
                ->whereRaw('expires >= NOW()')
                ->first();
    }
    /**
     * 
     * @param string $key
     * @param array $value
     */
    public static function set($key, array $value)
    {
        $row = self::getIntern($key);
        if($row === null){
            $row = new \Mobileia\Expressive\Database\Model\CacheInter();
        }
        $row->key_name = $key;
        $row->data = $value;
        $row->expires = DB::raw('DATE_ADD(NOW(), INTERVAL 1 DAY)');
        $row->save();
    }
    /**
     * 
     * @param string $key
     */
    public static function remove($key)
    {
        $row = self::getIntern($key);
        if($row === null){
            return false;
        }
        $row->delete();
    }
}
