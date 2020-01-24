<?php

namespace Enes5519\KisayolTP\command;

use Enes5519\KisayolTP\KisayolTP;
use Enes5519\KisayolTP\provider\DataProvider;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class MainCommand extends Command{

	public function __construct(){
		parent::__construct("ktp", "List warps", "/ktp help");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!($sender instanceof Player)){
			$sender->sendMessage(KisayolTP::PREFIX . "Please run ingame.");
			return;
		}

		if(empty($args)){
			$text = TextFormat::DARK_GRAY . ">> " . TextFormat::GREEN . "Warp List" . TextFormat::DARK_GRAY . " <<";
			foreach(KisayolTP::getAPI()->getProvider()->getAllWarps() as $warp){
				$text .= TextFormat::EOL . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $warp->getName();
			}
			$sender->sendMessage($text);
		}elseif($sender->hasPermission("enes5519.kisayoltp.warp.edit")){
			switch($args[0]){
				case "help":
				case "yardim":
					$pre = TextFormat::EOL . TextFormat::DARK_GRAY . "> " . TextFormat::GRAY . "/ktp ";
					$text = TextFormat::DARK_GRAY . ">>" . TextFormat::GREEN . " HELP " . TextFormat::DARK_GRAY . "<<";
					$text .= $pre . "add <warp name> [aliases]";
					$text .= $pre . "delete <warp name>";
					$sender->sendMessage($text);
					break;
				case "add":
				case "ekle":
					if(empty($args[1])){
						$sender->sendMessage(KisayolTP::PREFIX . "/ktp add <warp name> [aliases]");
						return;
					}

					$aliases = !empty($args[2]) ? explode(",", $args[2]) : [];

					switch(KisayolTP::getAPI()->getProvider()->addWarp($args[1], $sender, $aliases)){
						case DataProvider::ERROR_NONE:
							$sender->sendMessage(KisayolTP::PREFIX . "Warp added");
							break;
						case DataProvider::ERROR_ALREADY_EXISTS:
							$sender->sendMessage(KisayolTP::PREFIX . "Warp already exists.");
							break;
					}
					break;
				case "delete":
				case "kaldir":
					if(empty($args[1])){
						$sender->sendMessage(KisayolTP::PREFIX . "/ktp delete [warp name]");
						return;
					}

					switch(KisayolTP::getAPI()->getProvider()->deleteWarp($args[1])){
						case DataProvider::ERROR_NONE:
							$sender->sendMessage(KisayolTP::PREFIX . "Warp deleted");
							break;
						case DataProvider::ERROR_NOT_FOUND:
							$sender->sendMessage(KisayolTP::PREFIX . "Warp not found.");
							break;
					}
					break;
			}
		}
	}
}