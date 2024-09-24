<?php

namespace AccurateQS\StaffUI\managers;

use pocketmine\utils\Config;
use AccurateQS\StaffUI\Main;
use AccurateQS\StaffUI\utils\TimeConverter;

class HistoryManager {

    private $plugin;
    private $playerHistory;
    private $staffHistory;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->playerHistory = new Config($this->plugin->getDataFolder() . "player_history.yml", Config::YAML);
        $this->staffHistory = new Config($this->plugin->getDataFolder() . "staff_history.yml", Config::YAML);
    }

    public function addEntry(string $playerName, string $action, string $reason, string $staff, ?int $duration = null, ?int $reduction = null): void {
        $playerName = strtolower($playerName);
        $staff = strtolower($staff);
        $entry = [
            'action' => $action,
            'reason' => $reason,
            'staff' => $staff,
            'timestamp' => time(),
            'duration' => $duration,
            'reduction' => $reduction
        ];

        // Ajouter à l'historique du joueur
        $playerEntries = $this->playerHistory->get($playerName, []);
        array_unshift($playerEntries, $entry);
        $this->playerHistory->set($playerName, array_slice($playerEntries, 0, 50)); // Garder les 50 dernières entrées
        $this->playerHistory->save();

        // Ajouter à l'historique du staff
        if ($staff !== 'console') {
            $staffEntries = $this->staffHistory->get($staff, []);
            $entry['target'] = $playerName;
            array_unshift($staffEntries, $entry);
            $this->staffHistory->set($staff, array_slice($staffEntries, 0, 50)); // Garder les 50 dernières entrées
            $this->staffHistory->save();
        }

        // Mettre à jour les statistiques du joueur
        $this->updatePlayerStats($playerName, $action);
    }

    public function getPlayerHistory(string $playerName): array {
        return $this->playerHistory->get(strtolower($playerName), []);
    }

    public function getStaffHistory(string $staffName): array {
        return $this->staffHistory->get(strtolower($staffName), []);
    }

    public function getPlayerStats(string $playerName): array {
        $playerName = strtolower($playerName);
        $stats = $this->playerHistory->getNested("$playerName.stats", [
            'kick' => 0,
            'ban_permanent' => 0,
            'ban_temporary' => 0,
            'mute_permanent' => 0,
            'mute_temporary' => 0,
            'unban' => 0,
            'unmute' => 0,
            'ban_reduction' => 0,
            'mute_reduction' => 0
        ]);
        return $stats;
    }

    private function updatePlayerStats(string $playerName, string $action): void {
        $playerName = strtolower($playerName);
        $stats = $this->getPlayerStats($playerName);
        
        switch ($action) {
            case 'Kick':
                $stats['kick']++;
                break;
            case 'Ban':
                $stats['ban_permanent']++;
                break;
            case 'TempBan':
                $stats['ban_temporary']++;
                break;
            case 'Mute':
                $stats['mute_permanent']++;
                break;
            case 'TempMute':
                $stats['mute_temporary']++;
                break;
            case 'Unban':
                $stats['unban']++;
                break;
            case 'Unmute':
                $stats['unmute']++;
                break;
            case 'ReduceBan':
                $stats['ban_reduction']++;
                break;
            case 'ReduceMute':
                $stats['mute_reduction']++;
                break;
        }

        $this->playerHistory->setNested("$playerName.stats", $stats);
        $this->playerHistory->save();
    }

    public function getCurrentSanction(string $playerName): ?array {
        $playerName = strtolower($playerName);
        $banInfo = $this->plugin->getBanManager()->getBanInfo($playerName);
        $muteInfo = $this->plugin->getMuteManager()->getMuteInfo($playerName);

        if ($banInfo) {
            $type = $banInfo['expiration'] === null ? "Ban Permanent" : "Ban Temporaire";
            $remaining = $banInfo['expiration'] === null ? null : $banInfo['expiration'] - time();
            return [
                'type' => $type,
                'remaining' => $remaining,
                'reason' => $banInfo['reason']
            ];
        } elseif ($muteInfo) {
            $type = $muteInfo['expiration'] === null ? "Mute Permanent" : "Mute Temporaire";
            $remaining = $muteInfo['expiration'] === null ? null : $muteInfo['expiration'] - time();
            return [
                'type' => $type,
                'remaining' => $remaining,
                'reason' => $muteInfo['reason']
            ];
        }

        return null;
    }
}
