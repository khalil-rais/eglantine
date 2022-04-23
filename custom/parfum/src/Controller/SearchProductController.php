<?php

namespace Drupal\parfum\Controller;

use Drupal\commerce_product\Entity\Product; 
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Unicode;

class SearchProductController extends ControllerBase
{

  /**
   * Returns response for the autocompletion.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object containing the search string.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the autocomplete suggestions.
   */

  public function autocomplete(request $request) {
    $string = $request->query->get('q');
	$language = \Drupal::languageManager()->getCurrentLanguage()->getId();
	
/*
        if ($node->hasTranslation($langCode)) {
          $trans = $node->getTranslation($langCode);
          $title = $trans->getTitle();
          $url = $trans->toUrl('canonical')->toString();
        }
*/
	if ($language == "en"){
		$results = \Drupal::entityQuery('commerce_product')
			->execute();
		$products = array();

		
		
		if (isset($results)) {
			foreach ($results as $result) {
				$product = Product::load($result);
				if ($product->hasTranslation($language)) {
					$trans = $product->getTranslation($language);
					$title = $trans->getTitle();
					if (preg_match("/$string/i" , $title)){
						$products[] = ['value'=>$title.' ('.$result.')','label'=>$title];
					}
				}
			}
		}
	}
	else{
		if ($string) {
			$results = \Drupal::entityQuery('commerce_product')
				->condition("title",db_like($string), 'CONTAINS')
				->condition('langcode', $language)
				->execute();
			$products = array();
		
			if (isset($results)) {
				
				$custom_tag_list=array();
				foreach ($results as $result) {
							
				$product = Product::load($result);
				$products[] = ['value'=>$product ->getTitle().' ('.$result.')','label'=>$product ->getTitle()];
				//$custom_tag = \Drupal::service('entity.repository')->getTranslationFromContext($result, "en");
				//$custom_tag_list [] = $custom_tag;
				}
			}
		}
	}
    return new JsonResponse($products);
  }
}