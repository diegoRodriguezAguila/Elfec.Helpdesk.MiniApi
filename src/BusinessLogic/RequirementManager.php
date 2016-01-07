<?php
/**
 * Created by PhpStorm.
 * User: drodriguez
 * Date: 7/01/16
 * Time: 17:28
 */

namespace App\BusinessLogic;


use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;

class RequirementManager {

    /**
     * Gets a requirement
     * @param $code
     * @return mixed
     */
    public static function getRequirement($code){
        return TableRegistry::get('Requirements')->find()->where(['code' => $code])->first();
    }

    /**
     * Creates and saves a requirement
     * @param $code
     * @param $status
     * @return \Cake\Datasource\EntityInterface|\Cake\ORM\Entity
     */
    public static function saveRequirement($code, $status) {
        $requirements = TableRegistry::get('Requirements');
        $requirement = $requirements->newEntity(['code'=>$code, 'status'=>$status]);
        $requirements->save($requirement);
        return $requirement;
    }

    /**
     * @param $id
     * @param $data
     * @param $isRejection
     * @return \Cake\Network\Http\Response
     */
    public static function processRequirementAprroval($id, $data, $isRejection)
    {
        $queryParams = ['cod_u' => $data->user_code, 'opt' => ($isRejection ? 'no' : 'si'), 'id' => $id, 'rq' => 'M-001'];
        if ($isRejection)
            $queryParams['moti'] = $data->reject_reason;
        $http = new Client();
        // Simple get with querystring
        $response = $http->get('http://192.168.30.57/mesaayuda/rq_autorizSup_u.php', $queryParams);
        return $response;
    }
} 