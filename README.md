# EntityRelationSetter

[![Build Status](https://github.com/GollumSF/entity-relation-setter/actions/workflows/doctrine_2.4.5.yml/badge.svg?branch=master)](https://github.com/GollumSF/entity-relation-setter/actions)
[![Build Status](https://github.com/GollumSF/entity-relation-setter/actions/workflows/doctrine_2.7.yml/badge.svg?branch=master)](https://github.com/GollumSF/entity-relation-setter/actions)
[![Build Status](https://github.com/GollumSF/entity-relation-setter/actions/workflows/doctrine_2.11.yml/badge.svg?branch=master)](https://github.com/GollumSF/entity-relation-setter/actions)
[![Build Status](https://github.com/GollumSF/entity-relation-setter/actions/workflows/doctrine_latest.yml/badge.svg?branch=master)](https://github.com/GollumSF/entity-relation-setter/actions)

[![Build Status](https://github.com/GollumSF/entity-relation-setter/actions/workflows/symfony_4.4.yml/badge.svg?branch=master)](https://github.com/GollumSF/entity-relation-setter/actions)
[![Build Status](https://github.com/GollumSF/entity-relation-setter/actions/workflows/symfony_5.4.yml/badge.svg?branch=master)](https://github.com/GollumSF/entity-relation-setter/actions)
[![Build Status](https://github.com/GollumSF/entity-relation-setter/actions/workflows/symfony_6.4.yml/badge.svg?branch=master)](https://github.com/GollumSF/entity-relation-setter/actions)

[![Coverage](https://coveralls.io/repos/github/GollumSF/entity-relation-setter/badge.svg?branch=master)](https://coveralls.io/github/GollumSF/entity-relation-setter)
[![License](https://poser.pugx.org/gollumsf/entity-relation-setter/license)](https://packagist.org/packages/gollumsf/entity-relation-setter)
[![Latest Stable Version](https://poser.pugx.org/gollumsf/entity-relation-setter/v/stable)](https://packagist.org/packages/gollumsf/entity-relation-setter)
[![Latest Unstable Version](https://poser.pugx.org/gollumsf/entity-relation-setter/v/unstable)](https://packagist.org/packages/gollumsf/entity-relation-setter)
[![Discord](https://img.shields.io/discord/671741944149573687?color=purple&label=discord)](https://discord.gg/xMBc5SQ)

Trait for add method cross setter

## Installation:

```shell
composer require gollumsf/entity-relation-setter
```

## Exemple

### OneToOne

```php
use GollumSF\EntityRelationSetter\OneToOneSetter;

class User {
	
	use OneToOneSetter;
    
	/**
	 * @ORM\OneToOne(targetEntity=Address::class, inversedBy="tiers")
	 * @var Address
	 */
	private $address;

	////////////
	// Setter //
	////////////

	public function setAddress(?Address $address): self {
		return $this->oneToOneSet($address/*, 'address', 'user'*/);
	}
}

class Address {
	
	use OneToOneSetter;
    
	/**
	 * @ORM\OneToOne(targetEntity=Tiers::User, mappedBy="address")
	 * @var Address
	 */
	private $address;

	////////////
	// Setter //
	////////////

	public function setUser(?User $user): self {
		return $this->oneToOneSet($user/*, 'user', 'address'*/);
	}
}

```

### OneToManySetter and ManyToOneSetter

```php
use GollumSF\EntityRelationSetter\ManyToOneSetter;
use GollumSF\EntityRelationSetter\OneToManySetter;

class Address {
	
	use ManyToOneSetter;

	/**
	 * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="addresses")
	 * @var Country
	 */
	private $country;

	////////////
	// Setter //
	////////////

	public function setCountry(?Country $country): self {
		return $this->manyToOneSet($country/*, 'country', 'address'*/);
	}
}

class Country {
	
	use OneToManySetter;

	/**
	 * @ORM\OneToMany(targetEntity=Address::class, mappedBy="country")
	 * @var Address[]|ArrayCollection
	 */
	private $addresses;
	
	public function __construct() {
		$this->addresses = new ArrayCollection();
	}

	/////////
	// Add //
	/////////

	public function addAddress(Address $address): self {
		return $this->oneToManyAdd($address/*, 'addresses', 'country'*/);
	}
	
	////////////
	// Remove //
	////////////

	public function removeAddress(Address $address): self {
		return $this->oneToManyRemove($address/*, 'addresses', 'country'*/);
	}
}

```

### ManyToManySetter

```php
use GollumSF\EntityRelationSetter\ManyToManySetter;

class Post {
	
	use ManyToManySetter;

	/**
	 * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="posts")
	 * @var Tag[]|ArrayCollection
	 */
	private $tags;
	
	public function __construct() {
		$this->tags = new ArrayCollection();
	}

	/////////
	// Add //
	/////////

	public function addTag(Tag $tag): self {
		return $this->manyToManyAdd($tag/*, 'tags', 'post'*/);
	}
	
	////////////
	// Remove //
	////////////

	public function removeTag(Tag $tag): self {
		return $this->manyToManyRemove($tag/*, 'tags', 'post'*/);
	}
}

class Tag {
	
	use ManyToManySetter;

	/**
	 * @ORM\ManyToMany(targetEntity=Post:class, inversedBy="tags")²&
	 * @var Post[]|ArrayCollection
	 */
	private $posts;
	
	public function __construct() {
		$this->posts = new ArrayCollection();
	}

	/////////
	// Add //
	/////////

	public function addPost(Post $post): self {
		return $this->manyToManyAdd($post/*, 'posts', 'tag'*/);
	}
	
	////////////
	// Remove //
	////////////

	public function removePost(Post $post): self {
		return $this->manyToManyRemove($post/*, 'posts', 'tag'*/);
	}
}

```
