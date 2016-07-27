<?php

namespace Charcoal\Admin\Action\Object;

// Dependencies from `PHP`
use \Exception;

// PSR-7 (http messaging) dependencies
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

use \Pimple\Container;

// From `charcoal-base`
use \Charcoal\User\authenticator;

// Intra-module (`charcoal-admin`) dependencies
use \Charcoal\Admin\AdminAction;
use \Charcoal\Admin\Ui\ObjectContainerInterface;
use \Charcoal\Admin\Ui\ObjectContainerTrait;

/**
 * Admin Save Action: Save an object in its Storage.
 *
 * ## Required Parameters
 * - `obj_type`
 *
 * ## Response
 * - `success` _boolean_ True if the object was properly saved, false in case of any error.
 * - `obj_id` _mixed_ The created object ID, if any.
 * - `obj` _array_ The created object data.
 *
 * ## HTTP Codes
 * - `200` in case of a successful login
 * - `404` if any error occurs
 */
class SaveAction extends AdminAction implements ObjectContainerInterface
{
    use ObjectContainerTrait;

    /**
     * @var Authenticator $authenticator
     */
    private $authenticator;

    /**
     * @var array $saveData
     */
    private $saveData = [];

    /**
     * @param array|\ArrayAccess $data The action data.
     * @return LoginAction Chainable
     */
    public function setData($data)
    {
        parent::setData($data);

        unset($data['obj_type']);
        $this->setSaveData($data);

        return $this;
    }

    /**
     * @param Container $container A DI Container.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setAuthenticator($container['admin/authenticator']);
    }

    /**
     * @param Authenticator $authenticator The authenticator service.
     * @return void
     */
    private function setAuthenticator(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * @param array $saveData The save data.
     * @return SaveAction Chainable
     */
    public function setSaveData(array $saveData)
    {
        $this->saveData = $saveData;
        return $this;
    }

    /**
     * @return array
     */
    public function saveData()
    {
        return $this->saveData;
    }

    /**
     * @param ModelInterface|null $obj The object.
     * @return SaveAction Chainable
     */
    public function setObj($obj)
    {
        $this->Obj = $obj;
        return $this;
    }

    /**
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        try {
            $this->setData($request->getParams());

            $authorIdent = $this->authorIdent();

            // Create or load object (From `ObjectContainerTrait`)
            $obj = $this->obj();

            $saveData = $this->saveData();
            $saveData['created_by'] = $authorIdent;
            $saveData['last_modifided_by'] = $authorIdent;

            $obj->setFlatData($this->saveData());
            $valid = $obj->validate();
            $valid = true;
            if (!$valid) {
                $validation = $obj->validation();
                // @todo: Validation info to feedback
                $this->setSuccess(false);
                $this->addFeedback('error', 'Failed to save object: validation error(s).');
                return $response->withStatus(404);
            }

            $ret = $obj->save();

            if ($ret) {
                $this->setObj($obj);
                $this->setSuccess(true);
                $this->addFeedback('success', 'Object saved successfully');
                return $response;
            } else {
                $this->setObj(null);
                $this->setSuccess(false);
                return $response->withStatus(404);
            }
        } catch (Exception $e) {
            $this->setObj(null);
            $this->setSuccess(false);
            $this->addFeedback('error', $e->getMessage());
            return $response->withStatus(404);
        }
    }

    /**
     * @return string
     */
    private function authorIdent()
    {
        $author = $this->authenticator->authenticate();
        return (string)$author->id();
    }

    /**
     * @return array
     */
    public function results()
    {
        return [
            'success'   => $this->success(),
            'obj_id'    => $this->obj()->id(),
            'obj'       => $this->obj(),
            'feedbacks' => $this->feedbacks()
        ];
    }
}
