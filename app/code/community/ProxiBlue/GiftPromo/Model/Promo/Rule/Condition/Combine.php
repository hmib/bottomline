<?php

/**
 * Conditions combine model for gift product promo rules
 *
 * @category   ProxiBlue
 * @package    ProxiBlue_GiftPromo
 * @author     Lucas van Staden (support@proxiblue.com.au)
 */
class ProxiBlue_GiftPromo_Model_Promo_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine {

    public function __construct() {
        parent::__construct();
        $this->setType('giftpromo/promo_rule_condition_combine');
    }

    /**
     * Conditions child rules
     * Current supported:
     * Cart Subtotal
     *
     * @return array
     */
    public function getNewChildSelectOptions() {
        $addressCondition = Mage::getModel('giftpromo/promo_rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        $attributes[] = array('value' => 'giftpromo/promo_rule_condition_subtotal', 'label' => Mage::helper('giftpromo')->__('Sub Total'));
        $attributes[] = array('value' => 'giftpromo/promo_rule_condition_grandtotal', 'label' => Mage::helper('giftpromo')->__('Grand Total'));
        $attributes[] = array('value' => 'giftpromo/promo_rule_condition_discounttotal', 'label' => Mage::helper('giftpromo')->__('Sub Total After Discounts'));

        foreach ($addressAttributes as $code => $label) {
            $attributes[] = array('value' => 'giftpromo/promo_rule_condition_address|' . $code, 'label' => $label);
        }


        $checkoutCondition = Mage::getModel('giftpromo/promo_rule_condition_checkout');
        $checkoutAttributes = $checkoutCondition->loadAttributeOptions()->getAttributeOption();
        $chAttributes = array();

        foreach ($checkoutAttributes as $code => $label) {
            $chAttributes[] = array('value' => 'giftpromo/promo_rule_condition_checkout|' . $code, 'label' => $label);
        }

        $customerRules = array(array('value' => 'giftpromo/promo_rule_condition_customer_conditions', 'label' => Mage::helper('giftpromo')->__('Customer conditions combination')));
        $productRules = array(array('value' => 'giftpromo/promo_rule_condition_product_found', 'label' => Mage::helper('giftpromo')->__('Product attribute combination')),
            array('value' => 'giftpromo/promo_rule_condition_product_subselect', 'label' => Mage::helper('giftpromo')->__('Products subselection')));

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('label' => Mage::helper('giftpromo')->__('Cart Attributes'), 'value' => $attributes),
            array('label' => Mage::helper('giftpromo')->__('Checkout Attributes'), 'value' => $chAttributes),
            array('label' => Mage::helper('giftpromo')->__('Customer Related Rules'), 'value' => $customerRules),
            array('label' => Mage::helper('giftpromo')->__('Product Related Rules'), 'value' => $productRules),
//            array('value' => 'giftpromo/promo_rule_condition_twitter_conditions', 'label' => Mage::helper('giftpromo')->__('Twitter conditions combination')),
        ));
        return $conditions;
    }

    /**
     * Render condions as html
     * @return string
     */
    public function asHtml() {
        $html = $this->getTypeElement()->getHtml();
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * Load the aggrigator options
     * @return \ProxiBlue_GiftPromo_Model_Promo_Rule_Condition_Combine
     */
    public function loadAggregatorOptions() {
        $this->setAggregatorOption(array());
        return $this;
    }

    /**
     * Build the htlm rule selections for conditons
     * @return string
     */
    public function asHtmlRecursive() {
        $html = $this->asHtml() . '<ul id="' . $this->getPrefix() . '__' . $this->getId() . '__children" class="rule-param-start">';
        foreach ($this->getConditions() as $cond) {
            $html .= '<li>' . $cond->asHtmlRecursive() . '</li>';
        }
        $html .= '<li>' . $this->getNewChildElement()->getHtml() . '</li></ul>';
        return $html;
    }

    /**
     * Validate the conditions
     *
     * @param Varien_Object $object
     * @return boolean
     */
    public function validate(Varien_Object $object) {
        $validated = true; // trap no conditions to true.
        if ($this->getConditions()) {
            $validated = false; // start with not validated if any rules exist
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($object);
                if ($validated == false) {
                    return false;
                }
            }
        }
        return $validated;
    }

    /**
     * Validate the conditions
     *
     * @param Varien_Object $object
     * @return boolean
     */
    public function validateCount(Varien_Object $object) {
        $counted = array();
        if ($this->getConditions()) {
            foreach ($this->getConditions() as $cond) {
                if (method_exists($cond, 'validateCount')) {
                    $counted = $cond->validateCount($object);
                } else {
                    foreach ($object->setData('trigger_recollect', 0)->getAllItems() as $item) {
                        if (Mage::helper('giftpromo')->testGiftTypeCode($item->getProductType())) {
                            continue;
                        }
                        $counted[] = $item;
                        return $counted;
                    }
                }
            }
        }
        return $counted;
    }

}
