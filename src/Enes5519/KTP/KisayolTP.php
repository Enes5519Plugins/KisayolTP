<?php

namespace Enes5519\KTP;

use pocketmine\command\{Command, CommandSender};
use pocketmine\command\PluginCommand;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class KisayolTP extends PluginBase{

    /** @var KisayolTP */
    private static $api;

    /** @var string */
    public $b = "§6KısayolTP §8» §r";
    /** @var string */
    public $tabelabaslik = "§7[§bIşınlan§7]";

    const DUNYA_SILINDI = 4;
    const YER_BULUNAMADI = 3;

    /**
     * @return void
     */
    public function onEnable(){
        self::$api = $this;
        @mkdir($this->getDataFolder());
        $cfg = new Config($this->getDataFolder()."config.yml", Config::YAML, [
            "baslik" => $this->b,
            "tabela-baslik" => $this->tabelabaslik,
            "kullan-AvailableCommandsPacket" => "false",
            "yerler" => array()
        ]);
        $this->b = $cfg->get("baslik");
        $this->tabelabaslik = $cfg->get("tabela-baslik");
        $yerler = $cfg->get("yerler");
        foreach ($yerler as $yer){
            if(!$cfg->exists($yer)){
                $this->getServer()->getLogger()->info($this->b."§c$yer isimli alan tam yüklenemedi lütfen en kısa sürede kaldırın.");
                return;
            }
            $kmt = new PluginCommand($yer, $this);
            $kmt->setDescription($yer." alanına ışınlar");
            $kmt->setExecutor($this);
            $this->getServer()->getCommandMap()->register($yer, $kmt);
        }
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    /**
     * @param CommandSender $g
     * @param Command $kmt
     * @param string $label
     * @param array $args
     *
     * @return bool|void
     */
    public function onCommand(CommandSender $g, Command $kmt, string $label, array $args) : bool{
        if(!$g instanceof Player) return;
        if($kmt->getName() == "ktp"){
            if(empty($args[0])){
                $cfg = new Config($this->getDataFolder()."config.yml", Config::YAML);
                $yerler = $cfg->get("yerler");
                if(count($yerler) > 0) {
                    $liste = "";
                    foreach ($yerler as $yer) {
                        $liste .= "\n§8§l> §r§a$yer ";
                    }
                    $g->sendMessage("§7==== §2Yer Listesi §7====$liste");
                }else{
                    $g->sendMessage($this->b."§cŞuan da ayarlanmış yer bulunmamaktadır!");
                }
                if ($g->isOp()) $g->sendMessage(PHP_EOL.$this->b."§eDiğer komutlar için: /ktp yardim");
                return;
            }else{
                if(!$g->isOp()){
                    return;
                }
            }
            switch ($args[0]){
                case "yardim":
                    $g->sendMessage("§8=== §eKısayolTP §8===\n§8» §6/ktp ekle <yer-isim> §eYer ekler\n§8» §6/ktp kaldir <yer-isim> §eYer kaldırır");
                    break;
                case "ekle":
                    if(empty($args[1])){
                        $g->sendMessage($this->b."§c/ktp ekle <yer-isim>");
                        return;
                    }
                    if($this->ktpEkle($args[1], $g)) {
                        $g->sendMessage($this->b."§a".$args[1]." isimli yer eklendi!");
                    }
                    break;
                case "kaldir":
                    if(empty($args[1])){
                        $g->sendMessage($this->b."§c/ktp kaldir <yer-isim>");
                        return;
                    }
                    $kaldir = $this->ktpKaldir($args[1]);
                    switch ($kaldir){
                        case self::YER_BULUNAMADI:
                            $g->sendMessage($this->b."§c".$args[1]." isimli yer yok!");
                            break;
                        case true:
                            $g->sendMessage($this->b."§c".$args[1]." isimli yer kaldırıldı!");
                            break;
                    }
                    break;
                default:
                    $g->sendMessage($this->b."§cYanlış bir komut girildi: /ktp yardim");
                    break;
            }
        }else{
            $komut = $kmt->getName();
            $isinla = $this->yerIsinla($g, $komut);
            switch ($isinla){
                case true:
                    $g->sendMessage($this->b."§a$komut yerine ışınlandınız.");
                    break;
                case self::DUNYA_SILINDI:
                    $g->sendMessage($this->b."§c$komut yerinin dünyası silinmiş.");
                    break;
                case self::YER_BULUNAMADI:
                    $g->sendMessage($this->b."§cYer silinmiş ya da kaldırılmış.");
                    break;
                default:
                    $g->sendMessage($this->b."§cBir hata oluştu!");
                    break;
            }
        }
    }

    /**
     * @param string $yer
     * @param Player $o
     *
     * @return bool
     */
    public function ktpEkle($yer, Player $o){
        $cfg = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $yerler = $cfg->get("yerler");
        if(!in_array($yer, $yerler)) $yerler[] = $yer;
        $cfg->set("yerler", $yerler);
        $cfg->setNested($yer.".X" , $o->getFloorX());
        $cfg->setNested($yer.".Y" , $o->getFloorY());
        $cfg->setNested($yer.".Z" , $o->getFloorZ());
        $cfg->setNested($yer.".Yaw" , $o->yaw);
        $cfg->setNested($yer.".Pitch" , $o->pitch);
        $cfg->setNested($yer.".Dunya" , $o->getLevel()->getFolderName());
        $cfg->save();
        $this->komutGuncelle();
        return true;
    }

    /**
     * @param string $yer
     *
     * @return bool
     */
    public function ktpKaldir($yer){
        $cfg = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $yerler = $cfg->get("yerler");
        $deger = self::YER_BULUNAMADI;
        if(in_array($yer, $yerler)){
            unset($yerler[array_search($yer, $yerler)]);
            $deger = true;
        }
        if($cfg->exists($yer)){
            $cfg->remove($yer);
            $deger = true;
        }
        if($deger == self::YER_BULUNAMADI){
            return $deger;
        }
        $cfg->set("yerler", $yerler);
        $cfg->save();
        $this->komutGuncelle();
        return $deger;
    }

    /**
     * @param Player $o
     * @param string $yer
     *
     * @return bool
     */
    public function yerIsinla(Player $o, $yer){
        $cfg = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $yerler = $cfg->get("yerler");
        if(!in_array($yer, $yerler) or !$cfg->exists($yer)){
            return self::YER_BULUNAMADI;
        }
        $level = $cfg->getNested($yer.".Dunya");
        if($this->getServer()->isLevelGenerated($level)){
            $level = $this->getServer()->getLevelByName($level);
            if(!$level instanceof Level){
                $this->getServer()->loadLevel($cfg->getNested($yer.".Dunya"));
                $level = $this->getServer()->getLevelByName($level);
            }
        }else{
            return self::DUNYA_SILINDI;
        }
        $o->teleport(new Position($cfg->getNested($yer.".X"), $cfg->getNested($yer.".Y"), $cfg->getNested($yer.".Z"), $level), $cfg->getNested($yer."Yaw"), $cfg->getNested($yer."Pitch"));
        return true;
    }

    /**
     * @return KisayolTP
     */
    public static function getAPI() : KisayolTP{
        return self::$api;
    }

    /**
     * @param string $yer
     *
     * @return bool
     */
    public function yerVarmi($yer){
        $cfg = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $yerler = $cfg->get("yerler");
        if(!in_array($yer, $yerler) or !$cfg->exists($yer)){
            return false;
        }
        return true;
    }

    /**
     * @return void
     */
    public function komutGuncelle(){
        $cfg = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $cfg->reload();
        $yerler = $cfg->get("yerler");
        foreach ($yerler as $yer){
            if(!$cfg->exists($yer) or $this->getServer()->getCommandMap()->getCommand($yer) != null){
                return;
            }
            $kmt = new PluginCommand($yer, $this);
            $kmt->setDescription($yer." alanına ışınlar");
            $kmt->setExecutor($this);
            $this->getServer()->getCommandMap()->register($yer, $kmt);
        }
        if($cfg->get("kullan-AvailableCommandsPacket") != "false"){
            foreach ($this->getServer()->getOnlinePlayers() as $o){
                $o->sendCommandData();
            }
        }
    }
}
