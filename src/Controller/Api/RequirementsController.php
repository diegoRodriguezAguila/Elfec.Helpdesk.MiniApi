<?php
/**
 * Created by PhpStorm.
 * User: drodriguez
 * Date: 24/12/15
 * Time: 13:41
 */

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use App\BusinessLogic\RequirementManager;

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
        $this->response->type('json');
        $data = $this->request->input('json_decode');
        if (!$this->validParameters($data))
            return $this->invalidParametersResponse();

        $requirement = RequirementManager::getRequirement($id);
        if ($requirement != null)
            return $this->alreadyProcessedRequirement($requirement->status);

        $isRejection = $this->isRejection($data->status);
        $response = RequirementManager::processRequirementApproval($id, $data, $isRejection);
        if ($this->responseHasError($response))
            return $this->unprocessableRequirement($response->statusCode());

        $requirement = RequirementManager::saveRequirement($id, ($isRejection ? 0 : 1));
        $this->response->body(json_encode($requirement->jsonSerialize()));
        return $this->response;
    }

    //region Private Methods

    /**
     * Verifica que los parametros sean adecuados
     * @param $data
     * @return bool
     */
    private function validParameters($data)
    {
        return isset($data->status) && isset($data->user_code) &&
        (strtolower($data->status) == 'approved' ||
            (strtolower($data->status) == 'rejected' && isset($data->reject_reason)
                && !empty(trim($data->reject_reason))));
    }

    /**
     * Crea response de parámetros invalidos
     * @return \Cake\Network\Response|null
     */
    private function invalidParametersResponse()
    {
        $this->response->statusCode(400);
        $this->response->body(json_encode((object)['message' =>
            'Parametros inválidos para solicitud actual', 'code' => 400]));
        return $this->response;
    }

    /**
     * Verifica si ya se proceso un requerimiento
     * @param $status
     * @return \Cake\Network\Response|null
     */
    private function alreadyProcessedRequirement($status)
    {
        $this->response->statusCode(409);
        $errorMessage = 'El requerimiento ya fue ' . ($status == 1 ? 'aprobado' : 'rechazado') .
            ', no puede realizar ninguna otra acción';
        $this->response->body(json_encode((object)['message' => $errorMessage, 'code' => 409]));
        return $this->response;
    }

    /**
     * Crea response de que no se pudo completar la
     * solicitud al servidor de mesa de ayuda
     * @param Integer $statusCode
     * @return \Cake\Network\Response|null
     */
    private function unprocessableRequirement($statusCode)
    {
        //Code is not error, but it has error message
        if($statusCode==200)
            $statusCode = 400;
        $this->response->statusCode($statusCode);
        $this->response->body(json_encode((object)['message' => "El servidor de Mesa de Ayuda no pudo procesar "
        ."correctamente su solicitud. Intentelo de nuevo mas tarde.", 'code' =>$statusCode]));
        return $this->response;
    }

    /**
     * Es solicitud de rechazo de requerimiento
     * @param $status
     * @return bool
     */
    private function isRejection($status)
    {
        return strtolower($status) == 'rejected';
    }

    /**
     * Verifica si la respuesta del servidor tenía errores
     * @param \Cake\Network\Http\Response $response
     * @return bool
     */
    public function responseHasError($response)
    {
        return $response->statusCode() != 200 ||
               $response->body()==null || strpos(strtolower($response->body()),'error') !== false ;
    }

    //endregion
}