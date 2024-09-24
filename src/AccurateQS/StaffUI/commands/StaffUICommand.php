<?php

namespace AccurateQS\StaffUI\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use AccurateQS\StaffUI\Main;
use AccurateQS\StaffUI\forms\MainForm;

class StaffUICommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("staffui", "Ouvre l'interface principale du StaffUI");
        $this->setPermission("staffui.use");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage("Cette commande ne peut être utilisée que par un joueur.");
            return false;
        }

        $form = new MainForm($this->plugin);
        $form->sendTo($sender);

        return true;
    }
}
