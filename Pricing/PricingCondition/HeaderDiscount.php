<?php

namespace Application\AssetBookingBundle\Pricing\PricingCondition;

use Application\AssetBookingBundle\Pricing\PricingCondition\AbstractPricingConditionExecution;

 class HeaderDiscount extends AbstractPricingConditionExecution {

   public function execute(){

	$discountRate = $this->pricingContextContainer->get('discount_rate');

    if($discountRate && $discountRate > 0 ){

       if($this->getParameter('type') == 'percentage'){

           $value =  $this->pricingContextContainer->get($this->getParameter('source'));
           $value = $value / 100 *  $discountRate;
           $this->pricingContextContainer->set(
                $this->getParameter('target'), $value);
       }
    }

   }

}
