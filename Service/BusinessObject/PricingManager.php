<?php
namespace Application\AssetBookingBundle\Service\BusinessObject;

use Application\AssetBookingBundle\Pricing\PricingContextContainer;
use Application\AssetBOokingBundle\Entity\PriceCondition;
use Application\AssetBOokingBundle\Entity\PriceConfiguration;


class PricingManager {

    protected $container;
    
    public function setContainer($container){
        $this->container = $container;
    }


	public function getPricesForEntity($entity, $pricingContextContainer = null){
		
		if(!$pricingContextContainer){
		
			$pricingContextContainer = new PricingContextContainer();
		}
		
		$pricingContextContainer->addEntity($booking);
	
		return $this->executePriceContextContainer($pricingContextContainer);
	}


    public function determinePriceConfiguration($pricingContextContainer){

        /**  Some advanced determination code should come here one day to determine different pricing schemas
         * based on multiple dimensions such as type of entity, type of customer, ...

         * Following code should be stored in the db some day
         */

        $priceConfiguration = new PriceConfiguration();
        $priceConfiguration->setName('default asset pricing for customers');

        $priceConditionBaseNetValue = new PriceCondition();
        $priceConditionBaseNetValue->setName('get_entity_values');

        //Retrieve entity field 'net_price' and put it in pricing context container node as value 'net_price'
        $priceConditionBaseNetValue->setParameters(serialize(
            array('entity.net_value' => 'container.net_value')));
        $priceConditionBaseNetValue->setClass('Application\AssetBookingBundle\Pricing\PricingCondition\GetEntityValues');

        //Determine promotional prices for specific periods and override the container value
        $priceConditionNetPromotionValue = new PriceCondition();
        $priceConditionNetPromotionValue->setName('get_promotion_values');

        $priceConditionNetPromotionValue->setParameters(serialize(
            array('source' => 'entity.net_value',
                  'target' => 'container.net_value')));
        $priceConditionNetPromotionValue->setClass('Application\AssetBookingBundle\Pricing\PricingCondition\GetPromotionValues');

        $priceConditionDiscount = new PriceCondition();
        $priceConditionDiscount->setName('add_discount');

        //Retrieve previously stored container.net_value field and put calculations into target container field container.discount
        $priceConditionDiscount->setParameters(serialize(array('source' => 'container.net_value', 'target' => 'container.discount')));
        $priceConditionDiscount->setClass('Application\AssetBookingBundle\Pricing\PricingCondition\HeaderDiscount');

        $priceConditionAddFee = new PriceCondition();
        $priceConditionAddFee->setName('add_fixed_value');
        $priceConditionAddFee->setClass('Application\AssetBookingBundle\Pricing\PricingCondition\AddFixedValue');
        $priceConditionAddFee->setParameters(serialize(
            array('amount' => 5,
                  'currency' => 'euro',
                  'target' => 'container.admin_fee')));


        $priceConditionTotalExcl = new PriceCondition();
        $priceConditionTotalExcl->setName('sum_total_excl_vat');
        $priceConditionTotalExcl->setClass('Application\AssetBookingBundle\Pricing\PricingCondition\AddSum');
        $priceConditionTotalExcl->setParameters(serialize(
            array('source' =>
                    array('+' => 'container.net_value',
                          '-' => 'container.discount',
                          '+' => 'container.admin_fee'),
                   'target' => 'container.total_excl_vat')));

        $priceConditionTotalVat = new PriceCondition();
        $priceConditionTotalVat->setClass('Application\AssetBookingBundle\Pricing\PricingCondition\AddVat');
        $priceConditionTotalVat->setParameters(serialize(
              array('source'    => 'container.total_excl_vat',
                    'vat_rate'  => 'container.vat_rate',
                    'target'    => 'container.total_excl_vat')));

        $priceConfiguration->addPriceCondition($priceConditionBaseNetValue);
        $priceConfiguration->addPriceCondition($priceConditionDiscount);
        $priceConfiguration->addPriceCondition($priceConditionAddFee);
        $priceConfiguration->addPriceCondition($priceConditionTotalExcl);
        $priceConfiguration->addPriceCondition($priceConditionTotalVat);
        
		/**
		//In which sequence should conditions be executed
        $priceConfiguration->setAccessSequence(
            $priceConditionBaseNetValue,
            $priceConditionNetPromotionValue,
            $priceConditionDiscount,
            $priceConditionAddFee,
            $priceConditionTotalExcl,
            $priceConditionTotalVat);
		*/
		return $priceConfiguration;
    }

    protected function executePricingAccessSequence(&$priceConfiguration, &$pricingContextContainer){
		
		//Todo, use access sequence instead of iterating over price conditions collection
        foreach($priceConfiguration->getPriceConditions() as $priceCondition){

            $priceConditionExecutionClassName = $priceCondition->getClass();
		    $priceConditionExecution = new $priceConditionExecutionClassName($priceCondition, $pricingContextContainer, $this->container);
			$priceConditionExecution->execute();
        }

        /**
         * Now the container is enhanced with calculated values
         * It's the initial caller which is responsible for determining which container values are needed.
         * Even better: the full pricing determination could be stored for auditing purposes
         */

    }
	
    protected function executePriceContextContainer(&$priceContextContainer){

        //Determine active pricing configuration

        if($priceConfiguration = $this->determinePriceConfiguration($priceContextContainer)){
			
            $this->executePricingAccessSequence($priceConfiguration, $priceContextContainer);
			
			return $priceContextContainer->getContainerData();
        }
    }
		
}
