<?php

namespace AccurateQS\StaffUI\forms;

use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use AccurateQS\StaffUI\Main;

class MainForm {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function sendTo(Player $player): void {
        $form = new SimpleForm(function(Player $player, ?int $data) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $this->openBanCategory($player);
                    break;
                case 1:
                    $kickForm = new KickForm($this->plugin);
                    $kickForm->sendTo($player);
                    break;
                case 2:
                    $this->openMuteCategory($player);
                    break;
                case 3:
                    $this->openUnbanUnmuteCategory($player);
                    break;
                case 4:
                    $this->openHistoryCategory($player);
                    break;
            }
        });

        $form->setTitle("§cStaffUI");
        $form->setContent("§7Bienvenue dans l'interface de modération. Choisissez une catégorie :");
        $form->addButton("§4Ban\n§8Cliquez pour ouvrir");
        $form->addButton("§cKick\n§8Cliquez pour ouvrir");
        $form->addButton("§6Mute\n§8Cliquez pour ouvrir");
        $form->addButton("§aUnban/Unmute\n§8Cliquez pour ouvrir");
        $form->addButton("§bHistorique\n§8Cliquez pour ouvrir");

        $player->sendForm($form);
    }

    public function openBanCategory(Player $player): void {
        $form = new SimpleForm(function(Player $player, ?int $data) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $tempBanForm = new TempBanForm($this->plugin);
                    $tempBanForm->sendTo($player);
                    break;
                case 1:
                    $banForm = new BanForm($this->plugin);
                    $banForm->sendTo($player);
                    break;
                case 2:
                    $banIPForm = new BanIPForm($this->plugin);
                    $banIPForm->sendTo($player);
                    break;
                case 3:
                    $this->sendTo($player);
                    break;
            }
        });

        $form->setTitle("§4Catégorie Ban");
        $form->setContent("§7Choisissez une action :");
        $form->addButton("§cBan Temporaire\n§8Cliquez pour ouvrir");
        $form->addButton("§4Ban Permanent\n§8Cliquez pour ouvrir");
        $form->addButton("§4Ban IP\n§8Cliquez pour ouvrir");
        $form->addButton("§cRetour\n§8Retour au menu principal");

        $player->sendForm($form);
    }

    public function openMuteCategory(Player $player): void {
        $form = new SimpleForm(function(Player $player, ?int $data) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $tempMuteForm = new TempMuteForm($this->plugin);
                    $tempMuteForm->sendTo($player);
                    break;
                case 1:
                    $muteForm = new MuteForm($this->plugin);
                    $muteForm->sendTo($player);
                    break;
                case 2:
                    $this->sendTo($player);
                    break;
            }
        });

        $form->setTitle("§6Catégorie Mute");
        $form->setContent("§7Choisissez une action :");
        $form->addButton("§eMute Temporaire\n§8Cliquez pour ouvrir");
        $form->addButton("§6Mute Permanent\n§8Cliquez pour ouvrir");
        $form->addButton("§cRetour\n§8Retour au menu principal");

        $player->sendForm($form);
    }

    public function openUnbanUnmuteCategory(Player $player): void {
        $form = new SimpleForm(function(Player $player, ?int $data) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $unmuteForm = new UnmuteForm($this->plugin);
                    $unmuteForm->sendTo($player);
                    break;
                case 1:
                    $unbanForm = new UnbanForm($this->plugin);
                    $unbanForm->sendTo($player);
                    break;
                case 2:
                    $reduceBanForm = new ReduceBanForm($this->plugin);
                    $reduceBanForm->sendTo($player);
                    break;
                case 3:
                    $reduceMuteForm = new ReduceMuteForm($this->plugin);
                    $reduceMuteForm->sendTo($player);
                    break;
                case 4:
                    $this->sendTo($player);
                    break;
            }
        });

        $form->setTitle("§aUnban/Unmute");
        $form->setContent("§7Choisissez une action :");
        $form->addButton("§aUnmute\n§8Cliquez pour ouvrir");
        $form->addButton("§aUnban\n§8Cliquez pour ouvrir");
        $form->addButton("§eRéduire Ban\n§8Cliquez pour ouvrir");
        $form->addButton("§eRéduire Mute\n§8Cliquez pour ouvrir");
        $form->addButton("§cRetour\n§8Retour au menu principal");

        $player->sendForm($form);
    }

    public function openHistoryCategory(Player $player): void {
        $form = new SimpleForm(function(Player $player, ?int $data) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $playerHistoryForm = new PlayerHistoryForm($this->plugin);
                    $playerHistoryForm->sendTo($player);
                    break;
                case 1:
                    $staffHistoryForm = new StaffHistoryForm($this->plugin);
                    $staffHistoryForm->sendTo($player);
                    break;
                case 2:
                    $this->sendTo($player);
                    break;
            }
        });

        $form->setTitle("§bHistorique");
        $form->setContent("§7Choisissez une action :");
        $form->addButton("§bHistorique Joueur\n§8Cliquez pour ouvrir");
        $form->addButton("§bHistorique Staff\n§8Cliquez pour ouvrir");
        $form->addButton("§cRetour\n§8Retour au menu principal");

        $player->sendForm($form);
    }
}
