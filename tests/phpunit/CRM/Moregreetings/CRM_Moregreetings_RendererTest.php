<?php
declare(strict_types = 1);

namespace CRM\Moregreetings;

use Civi\Api4\Contact;
use Civi\Moregreetings\AbstractMoregreetingsHeadlessTestCase;
use Civi\Moregreetings\Fixtures\ContactFixture;

/**
 * @covers \CRM_Moregreetings_Renderer
 *
 * @group headless
 */
final class CRM_Moregreetings_RendererTest extends AbstractMoregreetingsHeadlessTestCase {

  public function testUpdateMoreGreetings(): void {
    $contact = ContactFixture::addIndividual(['first_name' => 'Foo', 'last_name' => 'Bar']);

    $template = 'Hello {$contact.first_name} {$contact.last_name}';
    $templates = ['greeting_smarty_1' => $template];
    \Civi::settings()->set('moregreetings_templates', $templates);
    \CRM_Moregreetings_Config::setActiveFieldCount(1);

    \CRM_Moregreetings_Renderer::updateMoreGreetings($contact['id']);

    static::assertSame(
      'Hello Foo Bar',
      Contact::get(FALSE)
        ->addSelect('more_greetings_group.greeting_field_1')
        ->addWhere('id', '=', $contact['id'])
        ->execute()->single()['more_greetings_group.greeting_field_1']
    );
  }

}
