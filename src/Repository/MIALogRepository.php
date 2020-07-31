<?php

namespace Mobileia\Expressive\Database\Repository;

/**
 * Description of MIALogRepository
 *
 * @author matiascamiletti
 */
class MIALogRepository
{
    static public function add($userId, $typeId, $itemId, $data, $caption)
    {
        $log = new \Mobileia\Expressive\Database\Model\MIALog();
        $log->user_id = $userId;
        $log->type_id = $typeId;
        $log->item_id = $itemId;
        $log->data = $data;
        $log->caption = $caption;
        $log->save();
    }
            
}