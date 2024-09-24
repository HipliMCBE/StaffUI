<?php

namespace AccurateQS\StaffUI\managers;

use pocketmine\utils\Config;
use AccurateQS\StaffUI\Main;
use AccurateQS\StaffUI\utils\TimeConverter;

class MuteManager {

    private $plugin;
    private $mutes;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->mutes = new Config($this->plugin->getDataFolder() . "mutes.yml", Config::YAML);
    }

    public function mute(string $playerName, string $reason, ?int $duration, string $mutedBy): bool {
        $playerName = strtolower($playerName);
        $this->mutes->set($playerName, [
            'reason' => $reason,
            'expiration' => $duration !== null ? time() + $duration : null,
            'mutedBy' => $mutedBy
        ]);
        $this->mutes->save();
        $this->plugin->getHistoryManager()->addEntry($playerName, $duration === null ? 'Mute' : 'TempMute', $reason, $mutedBy, $duration);

        $player = $this->plugin->getServer()->getPlayerExact($playerName);
        if ($player !== null) {
            $durationText = $duration !== null ? TimeConverter::secondsToHuman($duration) : "permanent";
            $player->sendMessage("§c[StaffUI] §fVous avez été rendu muet\n§7Raison: §f$reason\n§7Durée: §f$durationText\n§7Par: §f$mutedBy");
        }

        return true;
    }

    public function unmute(string $playerName): bool {
        $playerName = strtolower($playerName);
        if ($this->mutes->exists($playerName)) {
            $this->mutes->remove($playerName);
            $this->mutes->save();
            $this->plugin->getHistoryManager()->addEntry($playerName, 'Unmute', 'Démute', 'Console');

            $player = $this->plugin->getServer()->getPlayerExact($playerName);
            if ($player !== null) {
                $player->sendMessage("§a[StaffUI] §fVous n'êtes plus muet.");
            }

            return true;
        }
        return false;
    }

    public function isMuted(string $playerName): bool {
        $playerName = strtolower($playerName);
        if ($this->mutes->exists($playerName)) {
            $muteInfo = $this->mutes->get($playerName);
            if ($muteInfo['expiration'] === null || $muteInfo['expiration'] > time()) {
                return true;
            } else {
                $this->unmute($playerName);
            }
        }
        return false;
    }

    public function getMuteInfo(string $playerName): ?array {
        $playerName = strtolower($playerName);
        if ($this->isMuted($playerName)) {
            return $this->mutes->get($playerName);
        }
        return null;
    }

    public function reduceMuteTime(string $playerName, int $reduction): bool {
        $playerName = strtolower($playerName);
        if ($this->mutes->exists($playerName)) {
            $muteInfo = $this->mutes->get($playerName);
            if ($muteInfo['expiration'] !== null) {
                $muteInfo['expiration'] = max(time(), $muteInfo['expiration'] - $reduction);
                $this->mutes->set($playerName, $muteInfo);
                $this->mutes->save();
                $this->plugin->getHistoryManager()->addEntry($playerName, 'ReduceMute', "Temps réduit de " . TimeConverter::secondsToHuman($reduction), 'Console', null, $reduction);
                return true;
            }
        }
        return false;
    }
}
