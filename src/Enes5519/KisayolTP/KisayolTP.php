<?php

namespace Enes5519\KisayolTP;

use Enes5519\KisayolTP\command\MainCommand;
use Enes5519\KisayolTP\provider\DataProvider;
use Enes5519\KisayolTP\provider\YAMLProvider;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class KisayolTP extends PluginBase{

	public const PREFIX = TextFormat::GREEN . "KisayolTP> " . TextFormat::GRAY;
	public const LOCATION_SEPARATOR = ":";

	/** @var KisayolTP */
	private static $api;

	/** @var DataProvider */
	private $provider;

	public static function encodeLocation(Location $location) : string{
		return
			$location->x . self::LOCATION_SEPARATOR . $location->y . self::LOCATION_SEPARATOR . $location->z .
			self::LOCATION_SEPARATOR . $location->yaw . self::LOCATION_SEPARATOR . $location->pitch .
			self::LOCATION_SEPARATOR . $location->level->getFolderName();
	}

	public static function decodeLocation(string $loc) : Location{
		$explode = explode(self::LOCATION_SEPARATOR, $loc);
		return new Location(intval($explode[0]), intval($explode[1]), intval($explode[2]), floatval($explode[3]), floatval($explode[4]), self::getLevel($explode[5]));
	}

	protected static function getLevel(string $folderName) : Level{
		Server::getInstance()->loadLevel($folderName);
		return Server::getInstance()->getLevelByName($folderName);
	}

	/**
	 * @return void
	 */
	public function onEnable(){
		self::$api = $this;

		if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder());
		}

		$this->saveDefaultConfig();
		$this->getServer()->getCommandMap()->register("ktp", new MainCommand());
		$this->setProvider(new YAMLProvider($this));
	}

	/**
	 * @return KisayolTP
	 */
	public static function getAPI() : KisayolTP{
		return self::$api;
	}

	/**
	 * @param DataProvider $provider
	 */
	public function setProvider(DataProvider $provider) : void{
		$this->provider = $provider;
	}

	/**
	 * @return DataProvider
	 */
	public function getProvider() : DataProvider{
		return $this->provider;
	}
}
