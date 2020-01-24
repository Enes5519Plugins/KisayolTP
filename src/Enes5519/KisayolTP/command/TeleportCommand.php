<?php

declare(strict_types=1);

namespace Enes5519\KisayolTP\command;

use Enes5519\KisayolTP\Warp;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TeleportCommand extends Command{

	/** @var Warp */
	private $warp;

	public function __construct(Warp $warp){
		$this->warp = $warp;
		parent::__construct($warp->getName(), "Teleport to " . $warp->getName(), null, $warp->getAliases());
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player){
			$sender->teleport($this->warp->getLocation());
			$sender->sendMessage(TextFormat::DARK_GRAY . "> " . TextFormat::GRAY . "You teleported to " . $this->warp->getName());
		}
	}
}