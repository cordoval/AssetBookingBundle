<?php

namespace Application\AssetBookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    public function indexAction()
    {
	    $em = $this->get('doctrine.orm.entity_manager');
        
		$businessObjectManagementService = $this->get('erp.core.customization.business_object_profile');

        $booking = $businessObjectManagementService->createBusinessObject('booking_customer');

        if($booking){

            $booking->setBookingReference('abc123');
            $em->persist($booking);
            $em->flush();
        }
        //echo $statusManagementService->getStatusProfile('booking_default');

	
        return $this->render('AssetBookingBundle:Dashboard:index.twig', array('name' => 'z'));

    }


}
