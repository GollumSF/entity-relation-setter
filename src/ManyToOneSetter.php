<?php

namespace GollumSF\EntityRelationSetter;

use Doctrine\Common\Persistence\Proxy;

trait ManyToOneSetter {
	
	protected function manyToOneSet($value, $fieldName = null, $targetName = null): self {

		if ($fieldName === null) {
			$trace = debug_backtrace();
			$calledMethod = $trace[1]['function'];
			$fieldName = lcfirst(substr($calledMethod, 3));
		}

		if ($targetName === null) {
			$class = get_called_class();
			if ($class instanceof Proxy) {
				$class = get_parent_class($class);
			}
			$targetName = $class;
			if (($index = strrpos($targetName, '\\')) !== false) {
				$targetName = substr($targetName, $index + 1);
			}
			$targetName = lcfirst($targetName);
		}
		
		$addMethod = 'add'.ucfirst($targetName);
		$removeMethod = 'remove'.ucfirst($targetName);
		$oldValue = $this->$fieldName;
		$diff = $oldValue && $value;

		$this->$fieldName = $value;
		if ($diff) {
			if ($oldValue) {
				if (!method_exists($oldValue, $removeMethod)) {
					throw new \LogicException(sprintf('Method %s not exist or not public on class %s.', $removeMethod, get_class($oldValue)));
				}
				$oldValue->$removeMethod($this);
			}
			if ($value) {
				if (!method_exists($value, $addMethod)) {
					throw new \LogicException(sprintf('Method %s not exist or not public on class %s.', $addMethod, get_class($value)));
				}
				$value->$addMethod($this);
			}
		};
		return $this;
	}
}