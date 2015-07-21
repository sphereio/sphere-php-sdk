<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Sphere\Core\Request\Categories\Command;

use Sphere\Core\Model\Common\Context;
use Sphere\Core\Request\AbstractAction;

/**
 * @package Sphere\Core\Request\Categories\Command
 * @link http://dev.sphere.io/http-api-projects-categories.html#change-order-hint
 * @method string getOrderHint()
 * @method CategoryChangeOrderHintAction setOrderHint(string $orderHint = null)
 * @method string getAction()
 * @method CategoryChangeOrderHintAction setAction(string $action = null)
 */
class CategoryChangeOrderHintAction extends AbstractAction
{
    public function getFields()
    {
        return [
            'action' => [static::TYPE => 'string'],
            'orderHint' => [static::TYPE => 'string']
        ];
    }

    /**
     * @param array $data
     * @param Context|callable $context
     */
    public function __construct(array $data = [], $context = null)
    {
        parent::__construct($data, $context);
        $this->setAction('changeOrderHint');
    }

    /**
     * @param string $orderHint
     * @param Context|callable $context
     * @return CategoryChangeOrderHintAction
     */
    public static function ofOrderHint($orderHint, $context = null)
    {
        return static::of($context)->setOrderHint($orderHint);
    }
}
