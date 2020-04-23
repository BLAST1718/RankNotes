<?php

declare(strict_types=1);

namespace Fadhel\RankNotes\commands;

use Fadhel\RankNotes\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\TextFormat;

class Rank extends Command
{
    protected $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->setPermission("ranknotes.command");
        parent::__construct("ranknotes", "Rank Notes", "/ranknotes <player> <rank> <amount>", ["rn"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$this->testPermission($sender) or !$sender->hasPermission("ranknotes.command")){
            return;
        }
        if (empty($args[0]) || empty($args[1])) {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
        $player = $this->plugin->getServer()->getPlayer($args[0]);
        $rank = $args[1];
        if ($player === null) {
            $sender->sendMessage(TextFormat::RED . "Player not found.");
            return;
        }
        $ranks = [];
        foreach ($this->plugin->rank->getGroups() as $group) {
            $ranks[] = $group->getName();
        }
        if (!in_array($rank, $ranks)) {
            $sender->sendMessage(TextFormat::RED . "Rank not found.");
            return;
        }
        $amount = 1;
        if (!empty($args[2])) {
            if (is_numeric($args[2])) {
                if ($args[2] > 0) {
                    $amount = (int)$args[2];
                } else {
                    $sender->sendMessage(TextFormat::RED . "The amount must be greater than 0.");
                    return;
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "The amount must numeric.");
                return;
            }
        }
        $item = Item::get(Item::PAPER, 0, $amount);
        $item->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));
        $item->setNamedTagEntry(new StringTag("rank", $rank));
        $player->getInventory()->addItem($item->setCustomName(TextFormat::RESET . TextFormat::RED . $rank . TextFormat::GOLD . " Rank")->setLore([TextFormat::RESET . TextFormat::GOLD . "Right-click to redeem the " . TextFormat::RED . $rank . TextFormat::GOLD . " rank.\n\nRank Notes: this plugin was coded by Fadhel.\nSubscribe at " . TextFormat::RED . "https://youtube.com/c/FadhelFS"]));
        $sender->sendMessage(TextFormat::GOLD . "Successfully gave " . TextFormat::RED . $player->getName() . TextFormat::GOLD . " x" . $amount . TextFormat::RED . " " . $rank . TextFormat::GOLD . " rank note(s).");
        $sender->sendMessage(TextFormat::GOLD . "You've received" . TextFormat::RED . TextFormat::GOLD . " x" . $amount . TextFormat::RED . " " . $rank . TextFormat::GOLD . " rank note(s).");
    }
}
