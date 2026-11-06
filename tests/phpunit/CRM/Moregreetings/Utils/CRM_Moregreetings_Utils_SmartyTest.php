<?php
declare(strict_types = 1);

namespace phpunit\CRM\Moregreetings\Utils;

use Civi\Moregreetings\AbstractMoregreetingsHeadlessTestCase;

/**
 * @covers \CRM_Moregreetings_Utils_Smarty
 *
 * @group headless
 */
final class CRM_Moregreetings_Utils_SmartyTest extends AbstractMoregreetingsHeadlessTestCase {

  public function testMg_startswith(): void {
    static::assertSame(
      'foo',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"abc"|mg_startswith:"ab" ? "foo" : "bar"}')
    );

    static::assertSame(
      'bar',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"abc"|mg_startswith:"bc" ? "foo" : "bar"}')
    );
  }

  public function testMg_endsswith(): void {
    static::assertSame(
      'foo',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"abc"|mg_endswith:"bc" ? "foo" : "bar"}')
    );

    static::assertSame(
      'bar',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"abc"|mg_endswith:"ab" ? "foo" : "bar"}')
    );
  }

  public function testMg_contains(): void {
    static::assertSame(
      'foo',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"abc"|mg_contains:"b" ? "foo" : "bar"}')
    );

    static::assertSame(
      'bar',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"abc"|mg_contains:"d" ? "foo" : "bar"}')
    );
  }

  public function testLcfirst(): void {
    static::assertSame(
      'aBC',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"ABC"|lcfirst}')
    );
  }

  public function testUcfirst(): void {
    static::assertSame(
      'Abc',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"abc"|ucfirst}')
    );
  }

  public function testToken_extract(): void {
    // Value of first index is returned by default.
    static::assertSame(
      'a',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"a, b, c"|token_extract:","}')
    );

    // Use value of index 1
    static::assertSame(
      'b',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"a, b, c"|token_extract:",":1}')
    );

    // Disable trimming.
    static::assertSame(
      ' b',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty('{"a, b, c"|token_extract:",":1:false}')
    );
  }

  public function testTokens_have_min_length(): void {
    static::assertSame(
      'foo',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab,cde"|tokens_have_min_length:",":2 ? "foo" : "bar"}'
      )
    );

    static::assertSame(
      'bar',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab,cde"|tokens_have_min_length:",":3 ? "foo" : "bar"}'
      )
    );

    // Check values at index 1 and 2.
    static::assertSame(
      'foo',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab,cde,fgh"|tokens_have_min_length:",":3:"1,2" ? "foo" : "bar"}'
      )
    );

    // Check values at index 1 and 2.
    static::assertSame(
      'foo',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab,cde,fgh"|tokens_have_min_length:",":3:[1,2] ? "foo" : "bar"}'
      )
    );

    // Check values at index 0 and 1.
    static::assertSame(
      'bar',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab,cde,fgh"|tokens_have_min_length:",":3:"0,1" ? "foo" : "bar"}'
      )
    );

    // Check values at index 0 and 1.
    static::assertSame(
      'bar',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab,cde,fgh"|tokens_have_min_length:",":3:[0,1] ? "foo" : "bar"}'
      )
    );

    // Test that any token has the minimum length.
    static::assertSame(
      'foo',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab,cde"|tokens_have_min_length:",":3:null:false ? "foo" : "bar"}'
      )
    );

    // Disable trimming.
    static::assertSame(
      'foo',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab ,cde"|tokens_have_min_length:",":3:null:true:false ? "foo" : "bar"}'
      )
    );

    // Trimming is enabled by default.
    static::assertSame(
      'bar',
      \CRM_Moregreetings_Utils_Smarty::parseOneOffStringThroughSmarty(
        '{"ab ,cde"|tokens_have_min_length:",":3 ? "foo" : "bar"}'
      )
    );
  }

}
