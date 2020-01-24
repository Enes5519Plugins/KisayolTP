<?php

namespace Enes5519\KisayolTP;

use pocketmine\level\Location;

class Warp{

	/** @var string */
	private $name;
	/** @var Location */
	private $loc;
	/** @var array */
	private $aliases;

	public function __construct(string $name, Location $location, array $aliases){
		$this->name = $name;
		$this->loc = $location->asLocation();
		$this->aliases = $aliases;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return Location
	 */
	public function getLocation() : Location{
		return $this->loc;
	}

	/**
	 * @return array
	 */
	public function getAliases() : array{
		return $this->aliases;
	}
}