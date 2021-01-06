<?php

namespace Drupal\zenty_projects\Plugin\GraphQL\Schema;

use Drupal\graphql\Plugin\GraphQL\Schema\ComposableSchema;
use Drupal\graphql\Plugin\GraphQL\Schema\SdlSchemaPluginBase;

/**
 * @Schema(
 *   id = "zenty_projects",
 *   name = "Zenty project schema",
 *   extensions = "zenty_projects",
 * )
 */
class ZentyProjectsSchema extends ComposableSchema {

  // @todo: you can put default resolvers here - it is like base for ZentyProjectExtension

}
