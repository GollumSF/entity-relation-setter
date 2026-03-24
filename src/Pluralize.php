<?php

namespace GollumSF\EntityRelationSetter;

class Pluralize
{
	public static function pluralize(string $value): string {
		return \Doctrine\Inflector\InflectorFactory::create()->build()->pluralize($value);
	}
}
