<?php

namespace TestGollumSF\EntityRelationSetter;

use Doctrine\Persistence\Proxy;
use GollumSF\EntityRelationSetter\OneToOneSetter;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use PHPUnit\Framework\TestCase;

class User {

	use OneToOneSetter;

	private $address = null;
	private $infos = null;

	public function setAddress(?Address $address): self {
		return $this->oneToOneSet($address);
	}

	public function setInfos(?Infos $infos): self {
		return $this->oneToOneSet($infos);
	}
}

class Address {

	use OneToOneSetter;

	private $user = null;

	public function setUser(?User $user): self {
		return $this->oneToOneSet($user);
	}
}
class ProxyAddress extends Address implements Proxy {
	public function __load() {}
	public function __isInitialized() {}
}
class Infos {
}

class OneToOneSetterTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testOneToOneSet() {
		$user = new User();
		$address1 = new Address();
		$address2 = new ProxyAddress();

		$this->assertEquals($user->setAddress($address1), $user);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), $user);
		$this->assertEquals($this->reflectionGetValue($address2, 'user'), null);
		$this->assertEquals($user->setAddress($address2), $user);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), null);
		$this->assertEquals($this->reflectionGetValue($address2, 'user'), $user);

		$this->assertEquals($user->setAddress(null), $user);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), null);
		$this->assertEquals($this->reflectionGetValue($address2, 'user'), null);
	}

	public function testOneToOneSeException1() {
		$user = new User();
		$infos = new Infos();

		$this->expectException(\LogicException::class);
		$this->assertEquals($user->setInfos($infos), $user);
	}

	public function testOneToOneSeException2() {
		$user = new User();
		$infos1 = new Infos();
		$infos2 = new Infos();
		$this->reflectionSetValue($user, 'infos', $infos1);

		$this->expectException(\LogicException::class);
		$this->assertEquals($user->setInfos($infos2), $user);
	}
}