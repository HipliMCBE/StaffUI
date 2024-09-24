<?php

namespace AccurateQS\StaffUI\forms;

use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use AccurateQS\StaffUI\Main;

class UnmuteForm {

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

            if ($this->plugin->getMuteManager()->unmute($targetPlayer)) {
                $player->sendMessage("§a[StaffUI] §fLe joueur §e$targetPlayer §fa été démute.");
            } else {
                $player->sendMessage("§c[StaffUI] §fImpossible de démuter le joueur §e$targetPlayer§f. Il n'est peut-être pas mute.");
            }
        });

        $form->setTitle("§aDémuter un joueur");
        $form->addInput("§7Nom du joueur", "Entrez le nom du joueur à démuter");

        $player->sendForm($form);
    }
}
