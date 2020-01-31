<?php

namespace GollumSF\EntityRelationSetter;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\Persistence\Proxy;

trait OneToManySetter {
	
	protected function oneToManyAdd($value, string $fieldName = null, $targetName = null): self {
		
		if ($fieldName === null) {
			$trace = debug_backtrace();
			$calledMethod = $trace[1]['function'];
			$fieldName = lcfirst(substr($calledMethod, 3));
			$fieldName = Inflector::pluralize($fieldName);
		}
		
		if ($targetName === null) {
			$class = get_called_class();
			if (is_subclass_of($class, Proxy::class)) {
				$class = get_parent_class($class);
			}
			$targetName = $class;
			if (($index = strrpos($targetName, '\\')) !== false) {
				$targetName = substr($targetName, $index + 1);
			}
			$targetName = lcfirst($targetName);
		}
		
		$methodSet = 'set'.ucfirst($targetName);
		if (!$this->$fieldName->contains($value)) {
			$this->$fieldName->add($value);
			$value->$methodSet($this);
		}
		return $this;
	}
	
	protected function oneToManyRemove($value, string $fieldName = null, $targetName = null): self {
		
		if ($fieldName === null) {
			$trace = debug_backtrace();
			$calledMethod = $trace[1]['function'];
			$fieldName = lcfirst(substr($calledMethod, 6));
			$fieldName = Inflector::pluralize($fieldName);
		}
		
		if ($targetName === null) {
			$class = get_called_class();
			if (is_subclass_of($class, Proxy::class)) {
				$class = get_parent_class($class);
			}
			$targetName = $class;
			if (($index = strrpos($targetName, '\\')) !== false) {
				$targetName = substr($targetName, $index + 1);
			}
			$targetName = lcfirst($targetName);
		}
		
		$methodSet = 'set'.ucfirst($targetName);
		if ($this->$fieldName->contains($value)) {
			$this->$fieldName->removeElement($value);
			$value->$methodSet(null);
		}
		return $this;
	}
}