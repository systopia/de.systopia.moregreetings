<?php
declare(strict_types = 1);

namespace Civi\Moregreetings\Fixtures;

use Civi\Api4\Contact;

final class ContactFixture {

  /**
   * @param array<string, scalar> $values
   *
   * @return array
   * @phpstan-return array<string, mixed>
   *
   * @throws \CRM_Core_Exception
   */
  public static function addIndividual(array $values = []): array {
    return Contact::create(FALSE)
      ->setValues($values + [
        'contact_type' => 'Individual',
        'first_name' => 'Some',
        'last_name' => 'Individual',
      ])->execute()->single();
  }

}
