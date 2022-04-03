<?php

namespace GollumSF\EntityRelationSetter;

class Pluralize
{
	/**
	 * @param string $value
	 * @return string
	 */
	public static function pluralize($value) {
		// @codeCoverageIgnoreStart
		if (class_exists('Doctrine\Inflector\InflectorFactory')) {
			return \Doctrine\Inflector\InflectorFactory::create()->build()->pluralize($value);
		}
		return \Doctrine\Common\Inflector\Inflector::pluralize($value);
		// @codeCoverageIgnoreEnd
	} 
}
