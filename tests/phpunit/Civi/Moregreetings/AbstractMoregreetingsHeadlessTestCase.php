<?php
declare(strict_types = 1);

namespace Civi\Moregreetings;

use Civi\Test;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;

// phpcs:disable Generic.Files.LineLength.TooLong
abstract class AbstractMoregreetingsHeadlessTestCase extends TestCase implements HeadlessInterface, TransactionalInterface {
// phpcs:enable
  public function setUpHeadless(): CiviEnvBuilder {
    return Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  protected function setUp(): void {
    parent::setUp();
    $this->setUserPermissions(['access CiviCRM']);
  }

  /**
   * @phpstan-param array<string>|null $permissions
   */
  protected function setUserPermissions(?array $permissions): void {
    $userPermissions = \CRM_Core_Config::singleton()->userPermissionClass;
    // @phpstan-ignore-next-line
    $userPermissions->permissions = $permissions;
  }

}
