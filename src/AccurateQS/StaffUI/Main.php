<?php

namespace AccurateQS\StaffUI;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use AccurateQS\StaffUI\commands\StaffUICommand;
use AccurateQS\StaffUI\managers\BanManager;
use AccurateQS\StaffUI\managers\MuteManager;
use AccurateQS\StaffUI\managers\HistoryManager;
use AccurateQS\StaffUI\listeners\PlayerListener;

class Main extends PluginBase {

    private static $instance;
    private $banManager;
    private $muteManager;
    private $historyManager;

    public function onEnable(): void {
        self::$instance = $this;
        $this->saveDefaultConfig();

        $this->banManager = new BanManager($this);
        $this->muteManager = new MuteManager($this);
        $this->historyManager = new HistoryManager($this);

        // Supprimer l'ancienne commande si elle existe
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("staffui:staffui"));

        // Enregistrer la nouvelle commande
        $this->getServer()->getCommandMap()->register("staffui", new StaffUICommand($this));

        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener($this), $this);

        $this->getLogger()->info("StaffUI has been enabled!");
    }

    public function onDisable(): void {
        $this->getLogger()->info("StaffUI has been disabled!");
    }

    public static function getInstance(): self {
        return self::$instance;
    }

    public function getBanManager(): BanManager {
        return $this->banManager;
    }

    public function getMuteManager(): MuteManager {
        return $this->muteManager;
    }

    public function getHistoryManager(): HistoryManager {
        return $this->historyManager;
    }
}
