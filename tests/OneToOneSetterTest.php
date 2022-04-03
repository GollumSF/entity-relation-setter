<?php

namespace Test\GollumSF\EntityRelationSetter;

use GollumSF\EntityRelationSetter\OneToOneSetter;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use PHPUnit\Framework\TestCase;

class User {

	use OneToOneSetter;

	private $address3 = null;
	private $infos = null;
	
	/**
	 * @param ?Address3 $address3
	 * @return $this
	 */
	public function setAddress3($address3): self {
		return $this->oneToOneSet($address3);
	}
	
	/**
	 * @param ?Infos $infos
	 * @return $this
	 */
	public function setInfos($infos): self {
		return $this->oneToOneSet($infos);
	}
}

class Address3 {

	use OneToOneSetter;

	private $user = null;
	
	/**
	 * @param ?User $user
	 * @return $this
	 */
	public function setUser($user): self {
		return $this->oneToOneSet($user);
	}
}

if (interface_exists ('Doctrine\Persistence\Proxy')) {
	class ProxyAddress3 extends Address3 implements \Doctrine\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
}
if (interface_exists ('Doctrine\Common\Persistence\Proxy')) {
	class ProxyAddress3 extends Address3 implements \Doctrine\Common\Persistence\Proxy {
		public function __load() {}
		public function __isInitialized() {}
	}
}

class Infos {
}

class OneToOneSetterTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testOneToOneSet() {
		$user = new User();
		$address1 = new Address3();
		$address2 = new ProxyAddress3();

		$this->assertEquals($user->setAddress3($address1), $user);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), $user);
		$this->assertEquals($this->reflectionGetValue($address2, 'user', Address3::class), null);
		$this->assertEquals($user->setAddress3($address2), $user);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), null);
		$this->assertEquals($this->reflectionGetValue($address2, 'user', Address3::class), $user);

		$this->assertEquals($user->setAddress3(null), $user);
		$this->assertEquals($this->reflectionGetValue($address1, 'user'), null);
		$this->assertEquals($this->reflectionGetValue($address2, 'user', Address3::class), null);
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
