<?php

namespace Drupal\zenty_projects\Plugin\GraphQL\DataProducer;

use Drupal\zenty_projects\Plugin\GraphQL\QueryBase\EntityQueryBase;
use Drupal\zenty_projects\Plugin\GraphQL\QueryBase\Wrappers\QueryConnection;

/**
 * @DataProducer(
 *   id = "query_projects",
 *   name = @Translation("Query Projects"),
 *   description = @Translation("Loads a list of projects"),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Query Connection")
 *   )
 * )
 */
class QueryProjects extends EntityQueryBase {

  /**
   * @return \Drupal\Core\Access\AccessResultInterface|QueryConnection
   */
  public function resolve() {

    // Permissions
    $this->permissions->checkByPermissions(['view published zenty projects entities']);

    // QUERY
    $query = $this->buildBaseEntityQuery('zenty_projects');
    $query->condition('type', 'project');

    return new QueryConnection($query, $this->permissions);
  }

}
