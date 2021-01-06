<?php

namespace Drupal\zenty_projects\Plugin\GraphQL\QueryBase\Wrappers;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\zenty_projects\Plugin\GraphQL\QueryBase\EntityQueryPermission;
use GraphQL\Deferred;

/**
 * Helper class that wraps entity queries.
 */
class QueryConnection {

  /**
   * @var \Drupal\Core\Entity\Query\QueryInterface
   */
  protected $query;

  /**
   * @var EntityQueryPermission
   */
  protected $permissions;

  /**
   * QueryConnection constructor.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   */
  public function __construct(QueryInterface $query, EntityQueryPermission $permissions) {
    $this->query = $query;
    $this->permissions = $permissions;
    $this->permissions->checkAccess();
  }

  /**
   * Check permissions.
   *
   * @todo: correctly implement errors: at the moment we are just hiding content
   * check web/modules/contrib/graphql/doc/mutations/validations.md
   *
   * @param $allowed_value
   * @param $forbidden_value
   */
  protected function permissionAllowed() {
    return $this->permissions->getAccess();
  }

  /**
   * @return int
   */
  public function total() {
    // Return 0 if user does not have permissions to see the content.
    if (!$this->permissionAllowed()) {
      return 0;
    }

    $query = clone $this->query;
    $query->range(NULL, NULL)->count();
    return $query->execute();
  }

  /**
   * @return array|\GraphQL\Deferred
   */
  public function items() {
    // Return [] if user does not have permissions to see the content.
    if (!$this->permissionAllowed()) {
      return [];
    }

    $result = $this->query->execute();
    if (empty($result)) {
      return [];
    }

    $buffer = \Drupal::service('graphql.buffer.entity');
    $callback = $buffer->add($this->query->getEntityTypeId(), array_values($result));
    return new Deferred(function () use ($callback) {
      return $callback();
    });
  }

}
