<?php

namespace AccurateQS\StaffUI\forms;

use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use AccurateQS\StaffUI\Main;
use AccurateQS\StaffUI\utils\TimeConverter;

class PlayerHistoryForm {

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

            $targetPlayer = $data[0];
            $this->showPlayerHistory($player, $targetPlayer);
        });

        $form->setTitle("§bHistorique Joueur");
        $form->addInput("§7Nom du joueur", "Entrez le nom du joueur");

        $player->sendForm($form);
    }

    private function showPlayerHistory(Player $player, string $targetPlayer): void {
        $history = $this->plugin->getHistoryManager()->getPlayerHistory($targetPlayer);
        $stats = $this->plugin->getHistoryManager()->getPlayerStats($targetPlayer);
        $currentSanction = $this->plugin->getHistoryManager()->getCurrentSanction($targetPlayer);

        $form = new SimpleForm(function(Player $player, ?int $data) {
            if ($data === null) {
                $mainForm = new MainForm($this->plugin);
                $mainForm->openHistoryCategory($player);
                return;
            }
        });

        $form->setTitle("§bHistorique de $targetPlayer");
        
        $content = "§6Sanction actuelle :\n";
        if ($currentSanction) {
            $content .= "§f{$currentSanction['type']}\n";
            if ($currentSanction['remaining'] !== null) {
                $content .= "§fTemps restant : " . TimeConverter::secondsToHuman($currentSanction['remaining']) . "\n";
            }
            $content .= "§fRaison : {$currentSanction['reason']}\n\n";
        } else {
            $content .= "§fAucune sanction active\n\n";
        }

        $content .= "§6Liste des sanctions :\n";
        $content .= "§fKick : {$stats['kick']}\n";
        $content .= "§fBan Permanent : {$stats['ban_permanent']}\n";
        $content .= "§fBan Temporaire : {$stats['ban_temporary']}\n";
        $content .= "§fMute Permanent : {$stats['mute_permanent']}\n";
        $content .= "§fMute Temporaire : {$stats['mute_temporary']}\n";
        $content .= "§fUnban : {$stats['unban']}\n";
        $content .= "§fRéduction de temps de ban : {$stats['ban_reduction']}\n";
        $content .= "§fRéduction de temps de mute : {$stats['mute_reduction']}\n\n";

        $content .= "§6Historique détaillé :\n";
        if (empty($history)) {
            $content .= "§fAucun historique trouvé pour ce joueur.\n";
        } else {
            foreach ($history as $entry) {
                if (!isset($entry['action'])) {
                    continue;  // Skip this entry if 'action' is not set
                }
                $content .= "§fAction: {$entry['action']}\n";
                $content .= "§fRaison: " . ($entry['reason'] ?? "Non spécifiée") . "\n";
                $content .= "§fStaff: " . ($entry['staff'] ?? "Non spécifié") . "\n";
                $content .= "§fDate: " . date('Y-m-d H:i:s', $entry['timestamp'] ?? time()) . "\n";
                if (isset($entry['duration'])) {
                    $content .= "§fDurée: " . TimeConverter::secondsToHuman($entry['duration']) . "\n";
                }
                if (isset($entry['reduction'])) {
                    $content .= "§fRéduction: " . TimeConverter::secondsToHuman($entry['reduction']) . "\n";
                }
                $content .= "\n";
            }
        }
        $form->setContent($content);
        $form->addButton("§cRetour");

        $player->sendForm($form);
    }
}
