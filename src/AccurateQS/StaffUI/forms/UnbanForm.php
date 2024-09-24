<?php

namespace AccurateQS\StaffUI\forms;

use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use AccurateQS\StaffUI\Main;

class UnbanForm {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function sendTo(Player $player): void {
        $form = new CustomForm(function(Player $player, ?array $data) {
            if ($data === null) {
                $mainForm = new MainForm($this->plugin);
                $mainForm->openUnbanUnmuteCategory($player);
                return;
            }

            $targetPlayer = $data[0];

            if ($this->plugin->getBanManager()->unban($targetPlayer)) {
                $player->sendMessage("§a[StaffUI] §fLe joueur §e$targetPlayer §fa été débanni.");
            } else {
                $player->sendMessage("§c[StaffUI] §fImpossible de débannir le joueur §e$targetPlayer§f. Il n'est peut-être pas banni.");
            }
        });

        $form->setTitle("§aDébannir un joueur");
        $form->addInput("§7Nom du joueur", "Entrez le nom du joueur à débannir");

        $player->sendForm($form);
    }
}
