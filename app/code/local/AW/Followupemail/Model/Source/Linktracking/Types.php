<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Followupemail
 * @version    3.6.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Followupemail_Model_Source_Linktracking_Types
{
    const LINKTRACKING_TYPE_LINK_ONLY = 'link';
    const LINKTRACKING_TYPE_LINK_CART = 'link_cart';
    const LINKTRACKING_TYPE_LINK_CART_ORDER = 'link_order';

    public static function toOptionArray()
    {
        $helper = Mage::helper('followupemail');

        return array(
            self::LINKTRACKING_TYPE_LINK_ONLY       => $helper->__('All links'),
            self::LINKTRACKING_TYPE_LINK_CART       => $helper->__('Incomplete abandoned carts (order not placed)'),
            self::LINKTRACKING_TYPE_LINK_CART_ORDER => $helper->__('Complete abandoned carts (order placed)'),
        );
    }
}