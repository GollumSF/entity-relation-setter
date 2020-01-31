<?php

namespace GollumSF\EntityRelationSetter;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\Persistence\Proxy;

trait ManyToManySetter {
	
	protected function manyToManyAdd($value, string $fieldName = null, string $targetName = null): self {

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
		
		
		$addMethod = 'add'.ucfirst($targetName);
		if (!$this->$fieldName->contains($value)) {
			$this->$fieldName->add($value);
			$value->$addMethod($this);
		}
		return $this;
	}
	
	protected function manyToManyRemove($value, string $fieldName = null, string $targetName = null): self {

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
		
		$removeMethod = 'remove'.ucfirst($targetName);
		if ($this->$fieldName->contains($value)) {
			$this->$fieldName->removeElement($value);
			$value->$removeMethod($this);
		}
		return $this;
	}
}