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

	public function setAddress($address) {
		$this->oneToOneSet($address);
	}

	public function setInfos($infos) {
		$this->oneToOneSet($infos);
	}
}

class Address {

	use OneToOneSetter;

	private $user = null;

	public function setUser($user) {
		$this->oneToOneSet($user);
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

		$user->setAddress($address1);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), $user);
		$this->assertEquals($this->reflectionGetValue($address2, 'user'), null);
		$user->setAddress($address2);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), null);
		$this->assertEquals($this->reflectionGetValue($address2, 'user'), $user);

		$user->setAddress(null);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), null);
		$this->assertEquals($this->reflectionGetValue($address2, 'user'), null);
	}

	public function testOneToOneSeException1() {
		$user = new User();
		$infos = new Infos();

		$this->expectException(\LogicException::class);
		$user->setInfos($infos);
	}

	public function testOneToOneSeException2() {
		$user = new User();
		$infos1 = new Infos();
		$infos2 = new Infos();
		$this->reflectionSetValue($user, 'infos', $infos1);

		$this->expectException(\LogicException::class);
		$user->setInfos($infos2);
	}
}