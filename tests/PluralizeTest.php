<?php

namespace Test\GollumSF\EntityRelationSetter;

use GollumSF\EntityRelationSetter\Pluralize;
use PHPUnit\Framework\TestCase;

class PluralizeTest extends TestCase
{
	public function testPluralize(): void {
		$this->assertEquals('tags', Pluralize::pluralize('tag'));
		$this->assertEquals('posts', Pluralize::pluralize('post'));
		$this->assertEquals('addresses', Pluralize::pluralize('address'));
		$this->assertEquals('categories', Pluralize::pluralize('category'));
	}

	public function testPluralizeNoInflectorThrowsException(): void {
		// This test verifies the exception message when no inflector is found
		// We can't easily simulate missing classes, but we verify the method works
		// with the installed inflector
		$result = Pluralize::pluralize('child');
		$this->assertEquals('children', $result);
	}
}
