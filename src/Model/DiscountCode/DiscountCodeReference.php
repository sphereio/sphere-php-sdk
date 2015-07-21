<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Core\Model\DiscountCode;

use Sphere\Core\Model\Common\Context;
use Sphere\Core\Model\Common\Reference;

/**
 * @package Sphere\Core\Model\DiscountCode
 * @link http://dev.sphere.io/http-api-types.html#reference
 * @method string getTypeId()
 * @method DiscountCodeReference setTypeId(string $typeId = null)
 * @method string getId()
 * @method DiscountCodeReference setId(string $id = null)
 * @method DiscountCode getObj()
 * @method DiscountCodeReference setObj(DiscountCode $obj = null)
 */
class DiscountCodeReference extends Reference
{
    const TYPE_DISCOUNT_CODE = 'discount-code';

    public function getFields()
    {
        $fields = parent::getFields();
        $fields[static::OBJ] = [static::TYPE => '\Sphere\Core\Model\DiscountCode\DiscountCode'];

        return $fields;
    }

    /**
     * @param $id
     * @param Context|callable $context
     * @return DiscountCodeReference
     */
    public static function ofId($id, $context = null)
    {
        return static::ofTypeAndId(static::TYPE_DISCOUNT_CODE, $id, $context);
    }
}
