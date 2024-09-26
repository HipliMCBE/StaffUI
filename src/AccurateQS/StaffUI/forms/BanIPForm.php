<?php

namespace AccurateQS\StaffUI\forms;

use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use AccurateQS\StaffUI\Main;

class BanIPForm {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function sendTo(Player $player): void {
        $form = new CustomForm(function(Player $player, ?array $data) {
            if ($data === null) {
                $mainForm = new MainForm($this->plugin);
                $mainForm->openBanCategory($player);
                return;
            }

            $targetPlayer = $data[0] === count($this->getPlayerList()) - 1 ? $data[1] : $this->getPlayerList()[$data[0]];
            $reason = $data[2];

            $target = $this->plugin->getServer()->getPlayerExact($targetPlayer);
            if ($target instanceof Player) {
                $ip = $target->getNetworkSession()->getIp();
                if ($this->plugin->getBanManager()->banIP($targetPlayer, $ip, $reason, $player->getName())) {
                    $player->sendMessage("§a[StaffUI] §fL'IP du joueur §e" . $targetPlayer . "§fa été bannie.");
                } else {
                    $player->sendMessage("§c[StaffUI] §fImpossible de bannir l'IP du joueur §e" . $targetPlayer . "§f.");
                }
            } else {
                $player->sendMessage("§c[StaffUI] §fLe joueur §e" . $targetPlayer . "§fn'est pas en ligne.");
            }
        });

        $form->setTitle("§l§4Ban IP");
        $form->addDropdown("§7Sélectionnez un joueur", $this->getPlayerList());
        $form->addInput("§7Nom du joueur (si autre)", "Entrez le nom du joueur");
        $form->addInput("§7Raison", "Entrez la raison du ban IP");

        $player->sendForm($form);
    }

    private function getPlayerList(): array {
        $players = [];
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $p) {
            $players[] = $p->getName();
        }
        $players[] = "Autre (entrez le nom)";
        return $players;
    }
}
