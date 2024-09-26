<?php

namespace AccurateQS\StaffUI\forms;

use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use AccurateQS\StaffUI\Main;
use AccurateQS\StaffUI\utils\TimeConverter;

class StaffHistoryForm {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function sendTo(Player $player): void {
        $form = new CustomForm(function(Player $player, ?array $data) {
            if ($data === null) {
                $mainForm = new MainForm($this->plugin);
                $mainForm->openHistoryCategory($player);
                return;
            }

            $staffName = $data[0];
            $this->showStaffHistory($player, $staffName);
        });

        $form->setTitle("§bHistorique Staff");
        $form->addInput("§7Nom du staff", "Entrez le nom du membre du staff");

        $player->sendForm($form);
    }

    private function showStaffHistory(Player $player, string $staffName): void {
        $history = $this->plugin->getHistoryManager()->getStaffHistory($staffName);

        if (empty($history)) {
            $player->sendMessage("§c[StaffUI] §fAucun historique trouvé pour le staff §e" . $staffName . "§f.");
            return;
        }

        $form = new SimpleForm(function(Player $player, ?int $data) {
            if ($data === null) {
                $mainForm = new MainForm($this->plugin);
                $mainForm->openHistoryCategory($player);
                return;
            }
        });

        $form->setTitle("§bHistorique du staff " . $staffName);
        $content = "";
        foreach ($history as $entry) {
            $content .= "§6Action: §f{$entry['action']}\n";
            $content .= "§6Cible: §f{$entry['target']}\n";
            $content .= "§6Raison: §f" . ($entry['reason'] ?? "Non spécifiée") . "\n";
            $content .= "§6Date: §f" . date('Y-m-d H:i:s', $entry['timestamp']) . "\n";
            if (isset($entry['duration'])) {
                $content .= "§6Durée: §f" . TimeConverter::secondsToHuman($entry['duration']) . "\n";
            }
            if (isset($entry['reduction'])) {
                $content .= "§6Réduction: §f" . TimeConverter::secondsToHuman($entry['reduction']) . "\n";
            }
            $content .= "\n";
        }
        $form->setContent($content);
        $form->addButton("§cRetour");

        $player->sendForm($form);
    }
}
