<?php

declare(strict_types=1);

namespace Enes5519\KisayolTP\provider;

use Enes5519\KisayolTP\command\TeleportCommand;
use Enes5519\KisayolTP\Warp;
use Enes5519\KisayolTP\KisayolTP;
use pocketmine\level\Location;
use pocketmine\Server;

abstract class DataProvider{

	public const ERROR_NONE = 0;
	public const ERROR_ALREADY_EXISTS = 1;
	public const ERROR_NOT_FOUND = 2;

	final public function __construct(KisayolTP $ktp){
		$this->load($ktp);
		$this->updateCommandList();
	}

	final protected function updateCommandList() : void{
		foreach($this->getAllWarps() as $warp){
			$commandMap = Server::getInstance()->getCommandMap();
			if($commandMap->getCommand($warp->getName()) === null){
				$commandMap->register($warp->getName(), new class($warp) extends TeleportCommand{});
			}elseif($this->getWarp($warp->getName()) === null){
				$commandMap->unregister($commandMap->getCommand($warp->getName()));
			}
		}

		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$player->sendCommandData();
		}
	}

	/**
	 * Load data
	 *
	 * @param KisayolTP $ktp
	 * @return mixed
	 */
	abstract public function load(KisayolTP $ktp);

	/**
	 * Please add self::updateCommandList() to end of code
	 *
	 * @param string $name
	 * @param Location $location
	 * @param array $aliases
	 * @return int
	 */
	abstract public function addWarp(string $name, Location $location, array $aliases) : int;

	/**
	 * Please add self::updateCommandList() to end of code
	 *
	 * @param string $name
	 * @return int
	 */
	abstract public function deleteWarp(string $name) : int;

	/**
	 * Get warp by name
	 *
	 * @param string $name
	 * @return Warp|null
	 */
	abstract public function getWarp(string $name) : ?Warp;

	/**
	 * @return Warp[]
	 */
	abstract public function getAllWarps() : array;
}