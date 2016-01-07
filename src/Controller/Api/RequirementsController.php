<?php
/**
 * Created by PhpStorm.
 * User: drodriguez
 * Date: 24/12/15
 * Time: 13:41
 */

namespace App\Controller\Api;
use App\Controller\Api\AppController;
use Cake\Network\Http\Client;

class RequirementsController extends AppController
{
    /**
     * No se utiliza este método
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->response->statusCode(404);
        return $this->response;
    }

    /**
     * No se utiliza este método
     * @param $id
     * @return \Cake\Network\Response|null
     */
    public function view($id)
    {
        $this->response->statusCode(404);
        return $this->response;
    }

    /**
     * No se utiliza este método
     * @return \Cake\Network\Response|null
     */
    public function add()
    {
        $this->response->statusCode(404);
        return $this->response;
    }

    /**
     * No se utiliza este método
     * @param $id
     * @return \Cake\Network\Response|null
     */
    public function delete($id)
    {
        $this->response->statusCode(404);
        return $this->response;
    }

    /**
     * Metodo utilizado para aprobar/rechazar un requerimiento
     * @param $id
     * @return \Cake\Network\Response|null
     */
    public function edit($id)
    {
        $data = $this->request->input('json_decode');
        if (isset($data->status) && isset($data->user_code) &&
            (strtolower($data->status)=='approved' ||
                (strtolower($data->status)=='rejected' && isset($data->reject_reason)
                                                       && !empty(trim($data->reject_reason))))) {
            $isRejected = strtolower($data->status)=='rejected';
            $queryParams = ['cod_u'=>$data->user_code, 'opt'=>($isRejected?'no':'si'), 'id'=>$id, 'rq'=>'M-001'];
            if($isRejected)
                $queryParams['moti'] = $data->reject_reason;
            $http = new Client();
            // Simple get with querystring
            $response = $http->get('http://192.168.30.57/mesaayuda/rq_autorizSup_u.php', $queryParams);
            $this->response->body($response->body());
            return $this->response;
        }
        else {
            $this->response->statusCode(400);
            return $this->response;
        }
    }
}