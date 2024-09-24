<?php

namespace AccurateQS\StaffUI\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerChatEvent;
use AccurateQS\StaffUI\Main;
use AccurateQS\StaffUI\utils\TimeConverter;

class PlayerListener implements Listener {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerLogin(PlayerLoginEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $ip = $player->getNetworkSession()->getIp();

        if ($this->plugin->getBanManager()->isBanned($playerName)) {
            $banInfo = $this->plugin->getBanManager()->getBanInfo($playerName);
            $event->cancel();
            $event->setKickMessage($this->formatBanMessage($banInfo));
        } elseif ($this->plugin->getBanManager()->isIPBanned($ip)) {
            $event->cancel();
            $event->setKickMessage("§c[StaffUI] §fVotre IP est bannie du serveur.");
        }
    }

    public function onPlayerChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();

        if ($this->plugin->getMuteManager()->isMuted($playerName)) {
            $muteInfo = $this->plugin->getMuteManager()->getMuteInfo($playerName);
            $event->cancel();
            $player->sendMessage($this->formatMuteMessage($muteInfo));
        }
    }

    private function formatBanMessage(array $banInfo): string {
        $message = "§c[StaffUI] §fVous êtes banni du serveur.\n";
        $message .= "§7Raison: §f" . $banInfo['reason'] . "\n";
        if ($banInfo['expiration'] !== null) {
            $remainingTime = $banInfo['expiration'] - time();
            $message .= "§7Expiration: §f" . TimeConverter::secondsToHuman($remainingTime);
        } else {
            $message .= "§7Durée: §fPermanent";
        }
        return $message;
    }

    private function formatMuteMessage(array $muteInfo): string {
        $message = "§c[StaffUI] §fVous ne pouvez pas parler car vous êtes mute.\n";
        $message .= "§7Raison: §f" . $muteInfo['reason'] . "\n";
        if ($muteInfo['expiration'] !== null) {
            $remainingTime = $muteInfo['expiration'] - time();
            $message .= "§7Expiration: §f" . TimeConverter::secondsToHuman($remainingTime);
        } else {
            $message .= "§7Durée: §fPermanent";
        }
        return $message;
    }
}
