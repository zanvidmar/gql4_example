<?php

namespace Drupal\zenty_projects\Plugin\GraphQL\QueryBase;

use Drupal\Core\Session\AccountInterface;

class EntityQueryPermission {

  /**
   * Current user to test permissions on.
   *
   * @var AccountInterface
   */
  protected $user;

  /**
   * Roles that grants access for given query.
   *
   * @var array
   */
  protected $roles = [];

  /**
   * Permissions that grants access for given query.
   *
   * @var array
   */
  protected $permissions = [];

  /**
   * Access status based on roles and permissions check.
   *
   * @var bool
   */
  protected $access = FALSE;

  /**
   * Human readable error response for GQL API endpoint users.
   *
   * @var string
   */
  protected $error = '';

  /**
   * EntityQueryPermission constructor.
   * @param AccountInterface $user
   */
  public function __construct(AccountInterface $user) {
    $this->user = $user;
  }

  /**
   * @return EntityQueryPermission
   */
  public static function init(AccountInterface $user) {
    return new EntityQueryPermission($user);
  }

  /**
   * Set roles.
   *
   * @param array $roles
   */
  public function checkByRoles(array $roles) {
    $this->roles = $roles;
  }

  /**
   * Set permissions.
   *
   * @param array $permissions
   */
  public function checkByPermissions(array $permissions) {
    $this->permissions = $permissions;
  }

  // @todo: cache per user!
  // @todo: if $this->access is true stop the function
  /**
   * Check if user has access to content by given conditions.
   */
  public function checkAccess() {

    $user_roles = $this->user->getRoles();

    // Always allow access for administrator.
    if (in_array('administrator', $user_roles)) {
      $this->access = TRUE;
      return;
    }

    if (!empty($this->roles) || !empty($this->permissions)) {
      // Access by roles.
      if (count(array_intersect($this->roles, $user_roles)) === 0) {
        $this->access = TRUE;
        return;
      }

      // Access by permissions.
      if (!empty($this->permissions)) {
        $roles_permissions = user_role_permissions($user_roles);

        foreach ($roles_permissions as $role => $permissions) {

          // Check if at least one of permission intersects (matches) user permissions.
          if (count(array_intersect($this->permissions, $permissions)) !== 0) {
            $this->access = TRUE;
            return;
          }
        }
      }

    } else {
    // @todo: should we completely deny the access or allow it if no permissions or roles are granted?
    // @todo: maybe some additional step to see if we allow this or not / special permission
    }
  }


  /**
   * @return bool
   */
  public function getAccess($override = FALSE): bool{
    // Force override to allow access regardless of permissions
    if ($override === TRUE) {
      $this->access = $override;
    }
    return $this->access;
  }

  /**
   * @todo: make an descriptive error
   *
   * @return string
   */
  public function getError(): string{
    /*
    if (!$permission_allowed) {
      throw new UserError('no access');
    }
    */
    return $this->error;
  }
}
