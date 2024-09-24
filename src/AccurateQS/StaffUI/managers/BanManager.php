<?php

namespace AccurateQS\StaffUI\managers;

use pocketmine\utils\Config;
use AccurateQS\StaffUI\Main;
use AccurateQS\StaffUI\utils\TimeConverter;

class BanManager {

    private $plugin;
    private $bans;
    private $ipBans;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->bans = new Config($this->plugin->getDataFolder() . "bans.yml", Config::YAML);
        $this->ipBans = new Config($this->plugin->getDataFolder() . "ipbans.yml", Config::YAML);
    }

    public function ban(string $playerName, string $reason, ?int $duration, string $bannedBy): bool {
        $playerName = strtolower($playerName);
        $this->bans->set($playerName, [
            'reason' => $reason,
            'expiration' => $duration !== null ? time() + $duration : null,
            'bannedBy' => $bannedBy
        ]);
        $this->bans->save();
        $this->plugin->getHistoryManager()->addEntry($playerName, $duration === null ? 'Ban' : 'TempBan', $reason, $bannedBy, $duration);

        $player = $this->plugin->getServer()->getPlayerExact($playerName);
        if ($player !== null) {
            $durationText = $duration !== null ? TimeConverter::secondsToHuman($duration) : "permanente";
            $player->kick("§c[StaffUI] §fVous avez été banni\n§7Raison: §f$reason\n§7Durée: §f$durationText\n§7Banni par: §f$bannedBy");
        }

        return true;
    }

    public function banIP(string $playerName, string $ip, string $reason, string $bannedBy): bool {
        $this->ipBans->set($ip, [
            'playerName' => $playerName,
            'reason' => $reason,
            'bannedBy' => $bannedBy
        ]);
        $this->ipBans->save();
        $this->plugin->getHistoryManager()->addEntry($playerName, 'IPBan', $reason, $bannedBy);

        $player = $this->plugin->getServer()->getPlayerExact($playerName);
        if ($player !== null) {
            $player->kick("§c[StaffUI] §fVotre IP a été bannie\n§7Raison: §f$reason\n§7Banni par: §f$bannedBy");
        }

        return true;
    }

    public function unban(string $playerName): bool {
        $playerName = strtolower($playerName);
        if ($this->bans->exists($playerName)) {
            $this->bans->remove($playerName);
            $this->bans->save();
            $this->plugin->getHistoryManager()->addEntry($playerName, 'Unban', 'Débanni', 'Console');
            return true;
        }
        return false;
    }

    public function unbanIP(string $ip): bool {
        if ($this->ipBans->exists($ip)) {
            $playerName = $this->ipBans->get($ip)['playerName'];
            $this->ipBans->remove($ip);
            $this->ipBans->save();
            $this->plugin->getHistoryManager()->addEntry($playerName, 'UnbanIP', 'IP Débanni', 'Console');
            return true;
        }
        return false;
    }

    public function isBanned(string $playerName): bool {
        $playerName = strtolower($playerName);
        if ($this->bans->exists($playerName)) {
            $banInfo = $this->bans->get($playerName);
            if ($banInfo['expiration'] === null || $banInfo['expiration'] > time()) {
                return true;
            } else {
                $this->unban($playerName);
            }
        }
        return false;
    }

    public function isIPBanned(string $ip): bool {
        return $this->ipBans->exists($ip);
    }

    public function getBanInfo(string $playerName): ?array {
        $playerName = strtolower($playerName);
        if ($this->isBanned($playerName)) {
            return $this->bans->get($playerName);
        }
        return null;
    }

    public function reduceBanTime(string $playerName, int $reduction): bool {
        $playerName = strtolower($playerName);
        if ($this->bans->exists($playerName)) {
            $banInfo = $this->bans->get($playerName);
            if ($banInfo['expiration'] !== null) {
                $banInfo['expiration'] = max(time(), $banInfo['expiration'] - $reduction);
                $this->bans->set($playerName, $banInfo);
                $this->bans->save();
                $this->plugin->getHistoryManager()->addEntry($playerName, 'ReduceBan', "Temps réduit de " . TimeConverter::secondsToHuman($reduction), 'Console', null, $reduction);
                return true;
            }
        }
        return false;
    }
}
