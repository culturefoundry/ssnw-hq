<?php

namespace Drupal\content_model_documentation\Report;

/**
 * A report that shows a user count and breaks them down by roles.
 */
class UserRoles extends ReportBase implements ReportInterface, ReportDiagramInterface {

  /**
   * An array to contain all the errors found.
   *
   * @var array
   *   An array of roles errors found in making the report.
   */
  protected $errorsFound = [];


  /**
   * All users for drupal.
   *
   * @var array
   *   An array of users in the system.
   */
  protected $users;

  /**
   * A map of role machine names to friendly names.
   *
   * @var array
   *   An array role machine name => label elements.
   */
  protected $roleMap;

  /**
   * {@inheritdoc}
   */
  public static function getReportTitle(): string {
    return 'User roles';
  }

  /**
   * {@inheritdoc}
   */
  public function getReportType(): string {
    return 'diagram';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return $this->t('Users on this site fall into the following roles.');
  }

  /**
   * {@inheritdoc}
   */
  public function getDiagramList(): array {
    $diagrams = [
      'Users' => [
        'diagram' => $this->getUsersActiveDiagram(),
        'caption' => $this->t('The breakdown of all user accounts by active or blocked'),
        'key' => '',
      ],
      'Users roles' => [
        'diagram' => $this->getUserRoleDiagram(),
        'caption' => $this->t('The breakdown of all user accounts by roles. Users can have multiple roles.'),
        'key' => '',
        'errors' => $this->errorsFound,
      ],
    ];

    return $diagrams;
  }

  /**
   * Get the mermaid for the active user diagram.
   *
   * @return string
   *   Mermaid string for the diagram.
   */
  protected function getUsersActiveDiagram(): string {
    $users = $this->getUsers();
    $total_count = count($users);
    $active = 0;
    $blocked = 0;
    foreach ($users as $uid => $user) {
      if ($user->isBlocked()) {
        $blocked++;
        continue;
      }
      $active++;
    }
    $vars = ['@total_count' => $total_count];
    $title = $this->t('There are @total_count user accounts total.', $vars);
    $mermaid = "pie title $title" . PHP_EOL;
    $active_msg = $this->t('Active: @count user accounts', ['@count' => $active]);
    $mermaid .= "  \"$active_msg\": {$active}" . PHP_EOL;
    $blocked_msg = $this->t('Blocked: @count user accounts', ['@count' => $blocked]);
    $mermaid .= "  \"$blocked_msg\": {$blocked}" . PHP_EOL;
    return $mermaid;
  }

  /**
   * Get the mermaid for the user role diagram.
   *
   * @return string
   *   Mermaid string for the diagram.
   */
  protected function getUserRoleDiagram(): string {
    $users = $this->getUsers();
    $total_count = count($users);
    $active = 0;
    $blocked = 0;
    $role_counts = [];
    foreach ($users as $uid => $user) {
      if ($user->isBlocked()) {
        $blocked++;
        continue;
      }
      $active++;
      $roles = $user->getRoles();
      foreach ($roles as $rid => $role) {
        if (($role === 'authenticated') || ($role === 'anonymous')) {
          // These are not true roles.  Do not count, do not log.
          continue;
        }

        if (!$this->isRole($role)) {
          // This is not a legitimate role.  Log it.
          $this->errorsFound[] = "{$user->getAccountName()}: '{$role}'";
          continue;
        }
        $role_name = $this->getRoleName($role);
        if (!isset($role_counts[$role_name])) {
          $role_counts[$role_name] = 1;
        }
        else {
          $role_counts[$role_name]++;
        }
      }
    }

    ksort($role_counts, SORT_NATURAL);
    $role_num = count($role_counts);
    $vars = [
      '@total_count' => $total_count,
      '@active' => $active,
      '@blocked' => $blocked,
      '@role_num' => $role_num,
    ];
    $totals_msg = $this->t('Of the @total_count user accounts, @active are active, and @blocked are blocked.', $vars);
    $roles_msg = $this->t('The active accounts span @role_num roles.', $vars);
    $mermaid = "pie title $totals_msg  $roles_msg" . PHP_EOL;
    foreach ($role_counts as $role => $count) {
      $mermaid .= "  \"{$role}: {$count} {$this->t('accounts')}\" : {$count}" . PHP_EOL;
    }

    return $mermaid;
  }

  /**
   * Gets all users.
   *
   * @return \Drupal\user\UserInterface[]
   *   An array of all users in the system.
   */
  protected function getUsers(): array {
    if (empty($this->users)) {
      $user_storage = $this->entityTypeManager->getStorage('user');
      $uids = $user_storage->getQuery()
        ->accessCheck(FALSE)
        ->execute();
      $this->users = $user_storage->loadMultiple($uids);
    }
    return $this->users;
  }

  /**
   * Gets a map of role machine names to friendly names.
   *
   * @return array
   *   An array of machine names to friendly names.
   */
  protected function getRoleMap(): array {
    if (empty($this->roleMap)) {
      $roles = $this->entityTypeManager->getStorage('user_role')->loadMultiple();
      $role_map = [];
      foreach ($roles as $role_machine => $role) {
        $role_map[$role_machine] = $role->label();
      }
      // Remove roles that are not assignable and are for the system.
      unset($role_map['anonymous']);
      unset($role_map['authenticated']);
      $this->roleMap = $role_map;
    }
    return $this->roleMap;
  }

  /**
   * CHecks to see if the machine name matches a known role.
   *
   * @param string $role
   *   The machine name of a role to check.
   *
   * @return bool
   *   TRUE if the machine name matches a role. FALSE otherwise.
   */
  protected function isRole($role): bool {
    $roles = $this->getRoleMap();
    return !empty($roles[$role]);
  }

  /**
   * Gets the name of the role that matches the machine name.
   *
   * @param string $role
   *   The machine name of a role to get the name for.
   *
   * @return string
   *   The name of the role.
   */
  protected function getRoleName($role): string {
    $roles = $this->getRoleMap();
    return $roles[$role];
  }

}
