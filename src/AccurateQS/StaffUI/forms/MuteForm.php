<?php

namespace AccurateQS\StaffUI\forms;

use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use AccurateQS\StaffUI\Main;

class MuteForm {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function sendTo(Player $player): void {
        $form = new CustomForm(function(Player $player, ?array $data) {
            if ($data === null) {
                $mainForm = new MainForm($this->plugin);
                $mainForm->openMuteCategory($player);
                return;
            }

            $targetPlayer = $data[0] === count($this->getPlayerList()) - 1 ? $data[1] : $this->getPlayerList()[$data[0]];
            $reason = $data[2];

            if ($this->plugin->getMuteManager()->mute($targetPlayer, $reason, null, $player->getName())) {
                $player->sendMessage("§a[StaffUI] §fLe joueur §e" . $targetPlayer . "§fa été rendu muet de manière permanente.");
            } else {
                $player->sendMessage("§c[StaffUI] §fImpossible de rendre muet le joueur §e" . $targetPlayer . "§f.");
            }
        });

        $form->setTitle("§6Mute Permanent");
        $form->addDropdown("§7Sélectionnez un joueur", $this->getPlayerList());
        $form->addInput("§7Nom du joueur (si autre)", "Entrez le nom du joueur");
        $form->addInput("§7Raison", "Entrez la raison du mute");

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
