<?php

use PHPUnit\Framework\TestCase;
use Give\Framework\FieldsAPI\Factory\Field;
use Give\Framework\FieldsAPI\Factory\Exception\TypeNotSupported;
final class FieldTest extends TestCase {

    public function testMakeTextField() {
        $field = Field::text( 'my-text-field' );
        $this->assertEquals( 'text', $field->getType() );
    }

    public function testMakeSelectField() {
        $field = Field::select( 'my-select-field' );
        $this->assertEquals( 'select', $field->getType() );
    }

    public function testMakeTextareaField() {
        $field = Field::textarea( 'my-textarea-field' );
        $this->assertEquals( 'textarea', $field->getType() );
    }

    public function testMakeRadio() {
        $field = Field::radio( 'my-radio-field' );
        $this->assertEquals( 'radio', $field->getType() );
    }

    public function testMakeCheckbox() {
        $field = Field::checkbox( 'my-checkbox-field' );
        $this->assertEquals( 'checkbox', $field->getType() );
    }

    public function testMakeFile() {
        $field = Field::file( 'my-upload-field' );
        $this->assertEquals( 'file', $field->getType() );
    }

    public function testTypeNotSupported() {
        $this->expectException(TypeNotSupported::class);
        Field::custom( 'my-custom-field-type' );
    }
}
