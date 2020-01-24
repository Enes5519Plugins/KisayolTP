<?php

declare(strict_types=1);

namespace Enes5519\KisayolTP\provider;

use Enes5519\KisayolTP\Warp;
use Enes5519\KisayolTP\KisayolTP;
use pocketmine\level\Location;
use pocketmine\utils\Config;

class YAMLProvider extends DataProvider {

	/** @var Config */
	private $config;
	/** @var Warp[] */
	private $data = [];

	public function load(KisayolTP $ktp){
		$this->config = new Config($ktp->getDataFolder() . "warps.yml", Config::YAML);

		foreach($this->config->getAll() as $name => $data){
			$this->data[$name] = new Warp($name, KisayolTP::decodeLocation($data["loc"]), $data["aliases"]);
		}
	}

	public function addWarp(string $name, Location $location, array $aliases) : int{
		if(isset($this->data[$name])){
			return self::ERROR_ALREADY_EXISTS;
		}

		$this->data[$name] = new Warp($name, $location, $aliases);

		$this->config->set($name, ["loc" => KisayolTP::encodeLocation($location), "aliases" => $aliases]);
		$this->config->save();

		$this->updateCommandList();

		return self::ERROR_NONE;
	}

	public function deleteWarp(string $name) : int{
		if(!isset($this->data[$name])){
			return self::ERROR_NOT_FOUND;
		}

		unset($this->data[$name]);

		$this->config->remove($name);
		$this->config->save();

		$this->updateCommandList();

		return self::ERROR_NONE;
	}

	public function getWarp(string $name) : ?Warp{
		return $this->data[$name] ?? null;
	}

	public function getAllWarps() : array{
		return $this->data;
	}
}