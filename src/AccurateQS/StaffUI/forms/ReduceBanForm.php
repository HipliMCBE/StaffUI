<?php

namespace AccurateQS\StaffUI\forms;

use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use AccurateQS\StaffUI\Main;

class ReduceBanForm {

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
            $days = (int)$data[1];
            $hours = (int)$data[2];
            $minutes = (int)$data[3];

            $reduction = ($days * 86400) + ($hours * 3600) + ($minutes * 60);

            if ($this->plugin->getBanManager()->reduceBanTime($targetPlayer, $reduction)) {
                $player->sendMessage("§a[StaffUI] §fLa durée du ban de §e" . $targetPlayer . "§f a été réduite de §6" . $days . " §fjours, §6" . $hours . " §fheures et §6" . $minutes . " §fminutes.");
            } else {
                $player->sendMessage("§c[StaffUI] §fImpossible de réduire la durée du ban de §e" . $targetPlayer . "§f. Il n'est peut-être pas banni ou le ban est permanent.");
            }
        });

        $form->setTitle("§eRéduire la durée d'un ban");
        $form->addInput("§7Nom du joueur", "Entrez le nom du joueur");
        $form->addSlider("§7Jours", 0, 365, 1, 0);
        $form->addSlider("§7Heures", 0, 23, 1, 0);
        $form->addSlider("§7Minutes", 0, 59, 1, 0);

        $player->sendForm($form);
    }
}
