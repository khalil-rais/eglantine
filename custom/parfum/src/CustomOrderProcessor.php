<?php
 
namespace Drupal\parfum;
 
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_order\Adjustment;
 
/**
* Provides an order processor that modifies the cart according to the business logic.
*/
class CustomOrderProcessor implements OrderProcessorInterface
{
 /**
  * {@inheritdoc}
  */
 public function process(OrderInterface $order)  {
	$my_adjustment = new Adjustment([
		'type' => 'custom_adjustment',
		'label' => t('Fiscal stamp'),
		'amount' =>  new Price("0.600", 'TND'),
	]);
	$order->addAdjustment($my_adjustment);
 }
}