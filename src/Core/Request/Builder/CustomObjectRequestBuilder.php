<?php
/**
 * @author @jenschude <jens.schulze@commercetools.de>
 */

namespace Commercetools\Core\Request\Builder;

use Commercetools\Core\Model\CustomObject\CustomObject;
use Commercetools\Core\Model\CustomObject\CustomObjectDraft;
use Commercetools\Core\Request\CustomObjects\CustomObjectByIdGetRequest;
use Commercetools\Core\Request\CustomObjects\CustomObjectByKeyGetRequest;
use Commercetools\Core\Request\CustomObjects\CustomObjectCreateRequest;
use Commercetools\Core\Request\CustomObjects\CustomObjectDeleteRequest;
use Commercetools\Core\Request\CustomObjects\CustomObjectQueryRequest;

class CustomObjectRequestBuilder
{
    /**
     * @return CustomObjectQueryRequest
     */
    public function query()
    {
        return CustomObjectQueryRequest::of();
    }

    /**
     * @param CustomObjectDraft $customObjectDraft
     * @return CustomObjectCreateRequest
     */
    public function create(CustomObjectDraft $customObjectDraft)
    {
        return CustomObjectCreateRequest::ofDraft($customObjectDraft);
    }

    /**
     * @param CustomObject $customObject
     * @return CustomObjectDeleteRequest
     */
    public function delete(CustomObject $customObject)
    {
        return CustomObjectDeleteRequest::ofIdAndVersion($customObject->getId(), $customObject->getVersion());
    }

    /**
     * @param $container
     * @param $key
     * @return CustomObjectByKeyGetRequest
     */
    public function getByContainerAndKey($container, $key)
    {
        return CustomObjectByKeyGetRequest::ofContainerAndKey($container, $key);
    }

    /**
     * @param $id
     * @return CustomObjectByIdGetRequest
     */
    public function getById($id)
    {
        return CustomObjectByIdGetRequest::ofId($id);
    }
}
