<?php

namespace Drupal\zenty_projects\Plugin\GraphQL\DataProducer;

use Drupal\Component\Utility\Html;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "convert_html_to_absolute_urls",
 *   name = @Translation("Convert HTML to absolute urls"),
 *   description = @Translation("onvert HTML body text urls to absolute urls."),
 *   produces = @ContextDefinition("string",
 *     label = @Translation("HTML body with absolute urls")
 *   ),
 *   consumes = {
 *     "string" = @ContextDefinition("string",
 *       label = @Translation("String")
 *     )
 *   }
 * )
 */
class ConvertHtmlToAbsoluteUrls extends DataProducerPluginBase {

  /**
   * @param string $string
   *
   * @return mixed
   */
  public function resolve($string) {
    $base_url = \Drupal::request()->getSchemeAndHttpHost();
    // Return processed value
    return Html::transformRootRelativeUrlsToAbsolute($string, $base_url);
  }

}
