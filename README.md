# EnTityRelationSetter

[![Build Status](https://travis-ci.org/GollumSF/entity-relation-setter.svg?branch=master)](https://travis-ci.org/GollumSF/entity-relation-setter)
[![License](https://poser.pugx.org/gollumsf/entity-relation-setter/license)](https://packagist.org/packages/gollumsf/entity-relation-setter)
[![Latest Stable Version](https://poser.pugx.org/gollumsf/entity-relation-setter/v/stable)](https://packagist.org/packages/gollumsf/entity-relation-setter)
[![Latest Unstable Version](https://poser.pugx.org/gollumsf/entity-relation-setter/v/unstable)](https://packagist.org/packages/gollumsf/entity-relation-setter)

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
		return $this->oneToOneSet($address, 'address', 'user');
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
		return $this->oneToOneSet($user, 'user', 'address');
	}
}

```

### OneToManySetter and ManyToOneSetter

```php
use GollumSF\EntityRelationSetter\OneToOneMany;
use GollumSF\EntityRelationSetter\ManyToOneOne;

class Address {
	
	use ManyToOneSetter;

	/**
	 * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="addresss")
	 * @var Country
	 */
	private $country;

	////////////
	// Setter //
	////////////

	public function setCountry(?Country $country): self {
		return $this->manyToOneSet($country, 'country', 'address');
	}
}

class Country {
	
	use ManyToOneOne;

	/**
	 * @ORM\OneToMany(targetEntity=Address::class, mappedBy="country")
	 * @var Address[]|ArrayCollection
	 */
	private $addresss;
	
	public function __construct() {
		$this->addresss = new ArrayCollection();
	}

	/////////
	// Add //
	/////////

	public function addAddress(Address $address): self {
		return $this->oneToManyAdd($address, 'addresss', 'country');
	}
	
	////////////
	// Remove //
	////////////

	public function removeAddress(Address $address): self {
		return $this->oneToManyRemove($address, 'addresss', 'country');
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
		return $this->manyToManyAdd($tag, 'tags', 'post');
	}
	
	////////////
	// Remove //
	////////////

	public function removeTag(Tag $tag): self {
		return $this->manyToManyRemove($tag, 'tags', 'post');
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
		return $this->manyToManyAdd($post, 'posts', 'tag');
	}
	
	////////////
	// Remove //
	////////////

	public function removePost(Post $post): self {
		return $this->manyToManyRemove($post, 'posts', 'tag');
	}
}

```