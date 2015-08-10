<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 */

namespace Commercetools\Core\Model\Order;

use Commercetools\Core\Model\Common\JsonObject;
use Commercetools\Core\Model\Common\LocalizedString;
use Commercetools\Core\Model\Common\Price;
use Commercetools\Core\Model\Channel\ChannelReference;
use Commercetools\Core\Model\TaxCategory\TaxRate;

/**
 * @package Commercetools\Core\Model\Order
 * @method string getProductId()
 * @method LineItemImportDraft setProductId(string $productId = null)
 * @method LocalizedString getName()
 * @method LineItemImportDraft setName(LocalizedString $name = null)
 * @method ProductVariantImportDraft getVariant()
 * @method LineItemImportDraft setVariant(ProductVariantImportDraft $variant = null)
 * @method Price getPrice()
 * @method LineItemImportDraft setPrice(Price $price = null)
 * @method int getQuantity()
 * @method LineItemImportDraft setQuantity(int $quantity = null)
 * @method ItemStateCollection getState()
 * @method LineItemImportDraft setState(ItemStateCollection $state = null)
 * @method ChannelReference getSupplyChannel()
 * @method LineItemImportDraft setSupplyChannel(ChannelReference $supplyChannel = null)
 * @method TaxRate getTaxRate()
 * @method LineItemImportDraft setTaxRate(TaxRate $taxRate = null)
 */
class LineItemImportDraft extends JsonObject
{
    public function getPropertyDefinitions()
    {
        return [
            'productId' => [static::TYPE => 'string'],
            'name' => [static::TYPE => '\Commercetools\Core\Model\Common\LocalizedString'],
            'variant' => [static::TYPE => '\Commercetools\Core\Model\Order\ProductVariantImportDraft'],
            'price' => [static::TYPE => '\Commercetools\Core\Model\Common\Price'],
            'quantity' => [static::TYPE => 'int'],
            'state' => [static::TYPE => '\Commercetools\Core\Model\Order\ItemStateCollection'],
            'supplyChannel' => [static::TYPE => '\Commercetools\Core\Model\Channel\ChannelReference'],
            'taxRate' => [static::TYPE => '\Commercetools\Core\Model\TaxCategory\TaxRate'],
        ];
    }
}