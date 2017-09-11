<?php

namespace Enes5519\KTP;

use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{
    
    private $p;
    
    public function __construct(KisayolTP $p){
        $this->p = $p;    
    }
    
    public function tabelaOlustur(SignChangeEvent $e){
        $o = $e->getPlayer();
        if($o->hasPermission("enes5519.kisayoltp.tabelaolustur")){
            if($e->getLine(0) == "ktp"){
                $yer = $e->getLine(1);
                if(!$this->p->yerVarmi($yer)){
                    $e->setLine(0, "§cHATA");
                    $e->setLine(1, "§cYer");
                    $e->setLine(2, "§cbulunamadı");
                    return;
                }
                if($e->getLine(2) != "" or $e->getLine(3) != ""){
                    $e->setLine(0, $this->p->tabelabaslik);
                    $e->setLine(1, "§e".$yer);
                    return;
                }
                $e->setLine(0, "");
                $e->setLine(1, $this->p->tabelabaslik);
                $e->setLine(2, "§e".$yer);
                $e->setLine(3, "");
            }
        }
    }

    public function tabelaTikla(PlayerInteractEvent $e){
        $o = $e->getPlayer();
        $t = $o->getLevel()->getTile($e->getBlock());
        if($t instanceof Sign){
            $satirlar = $t->getText();
            if(!in_array($this->p->tabelabaslik, $satirlar)){
                return;
            }
            $yerbul = array_search($this->p->tabelabaslik, $satirlar) + 1;
            $yerbul = TextFormat::clean($satirlar[$yerbul]);
            $o->getServer()->dispatchCommand($o, $yerbul);
        }
    }
}