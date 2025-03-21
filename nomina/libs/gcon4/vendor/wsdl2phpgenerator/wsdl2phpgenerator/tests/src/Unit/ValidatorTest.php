<?php
/**
 * @package wsdl2phpTest
 */
namespace Wsdl2PhpGenerator\Tests\Unit;

use PHPUnit_Framework_TestCase;
use Wsdl2PhpGenerator\Validator;

/**
 * Test class for Validator.
 * Generated by PHPUnit on 2009-11-11 at 00:56:02.
 *
 * @package wsdl2phpTest
 */
class ValidatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * Testing the validate class function
     */
    public function testValidateClass()
    {
        $this->assertEquals('foo', Validator::validateClass('foo'));
        $this->assertEquals('foobar', Validator::validateClass('foo-bar'));
        $this->assertEquals('Foo', Validator::validateClass('Foo'));
        $this->assertEquals('foo523', Validator::validateClass('foo523'));
    }

    /**
     * Testing the validate class function with a reserved keyword.
     */
    public function testValidateClassReservedKeyword()
    {
        // for is reserved keyword
        $this->assertEquals('forCustom', Validator::validateClass('for'));
        // list is reserved keyword. PHP is not case sensitive in keywords
        $this->assertEquals('ListCustom', Validator::validateClass('List'));
    }

    /**
     * Testing the validate class function with existing classes.
     */
    public function testValidateClassExists() {
        // Base handling
        $this->assertEquals('SoapClientCustom', Validator::validateClass('SoapClient'));
        // Use eval to allow creating a class inside a class.
        eval("class SoapClientCustom {};");
        // Now that both SoapClient and SoapClientCustom are defined we append numbering.
        $this->assertEquals('SoapClientCustom2', Validator::validateClass('SoapClient'));
        eval("class SoapClientCustom2 {};");
        // ... And numbering should continue.
        $this->assertEquals('SoapClientCustom3', Validator::validateClass('SoapClient'));
    }

    /**
     * Test the typename
     */
    public function testValidateType()
    {
        $this->assertEquals('foo', Validator::validateType('foo'));
        $this->assertEquals('foobar', Validator::validateType('foo-bar'));
        $this->assertEquals('Foo', Validator::validateType('Foo'));
        $this->assertEquals('foo523', Validator::validateType('foo523'));
        $this->assertEquals('arrayOfTest', Validator::validateType('arrayOfTest'));
        $this->assertEquals('test[]', Validator::validateType('test[]'));

        $this->assertEquals('int', Validator::validateType('nonNegativeInteger'));
        $this->assertEquals('float', Validator::validateType('float'));
        $this->assertEquals('string', Validator::validateType('normalizedString'));
        $this->assertEquals('string', Validator::validateType('<anyXML>'));
        $this->assertEquals('Foo[]', Validator::validateType('Foo[]'));

        $this->assertEquals('andCustom', Validator::validateType('and')); // and is reserved keyword
    }

}
