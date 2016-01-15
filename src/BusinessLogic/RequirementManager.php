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
use App\Model\Entity\Requirement;

class RequirementManager
{

    /**
     * Gets a requirement
     * @param $code
     * @return mixed
     */
    public static function getRequirement($code)
    {
        return TableRegistry::get('Requirements')->find()->where(['code' => $code])->first();
    }

    /**
     * Creates and saves a requirement
     * @param $code
     * @param $status
     * @return Requirement
     */
    public static function saveRequirement($code, $status)
    {
        $requirements = TableRegistry::get('Requirements');
        $requirements->addBehavior('Timestamp');
        $requirement = new Requirement(['code' => $code, 'status' => $status]);
        $requirements->save($requirement);
        return $requirement;
    }

    /**
     * @param $id
     * @param $data
     * @param $isRejection
     * @return \Cake\Network\Http\Response
     */
    public static function processRequirementApproval($id, $data, $isRejection)
    {
        $queryParams = ['cod_u' => $data->user_code, 'cod_sol'=>$data->request_user,
            'opt' => ($isRejection ? 'no' : 'si'), 'id' => $id];
        if ($isRejection)
            $queryParams['moti'] = $data->reject_reason;
        $http = new Client();
        // Simple get with query string
        $response = $http->get('http://192.168.30.57/mesaayuda/rq_autorizGte_u.php', $queryParams);
        return $response;
    }
} 