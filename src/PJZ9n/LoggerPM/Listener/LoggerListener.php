<?php
    
    /**
     *  _                       _          _________  ___
     * | |                     | |         | ___ \  \/  |
     * | |     ___   __ _  __ _| | ___ _ __| |_/ / .  . |
     * | |    / _ \ / _` |/ _` | |/ _ \ '__|  __/| |\/| |
     * | |___| (_) | (_| | (_| | |  __/ |  | |   | |  | |
     * \_____/\___/ \__, |\__, |_|\___|_|  \_|   \_|  |_/
     *               __/ | __/ |
     *              |___/ |___/
     *
     * Copyright (c) 2019 PJZ9n.
     *
     * This file is part of LoggerPM(https://github.com/PJZ9n/LoggerPM).
     *
     * LoggerPM is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * LoggerPM is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with LoggerPM.  If not, see <http://www.gnu.org/licenses/>.
     */
    
    declare(strict_types=1);
    
    namespace PJZ9n\LoggerPM\Listener;
    
    use PJZ9n\LoggerPM\Library\Listener\LoggerPMListener;
    use PJZ9n\LoggerPM\Log\LogActionType;
    use PJZ9n\LoggerPM\Log\LogManager;
    use pocketmine\event\block\BlockBreakEvent;
    use pocketmine\event\block\BlockPlaceEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    use pocketmine\event\player\PlayerChatEvent;
    use pocketmine\event\player\PlayerInteractEvent;
    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\player\PlayerLoginEvent;
    use pocketmine\event\player\PlayerQuitEvent;
    use pocketmine\event\server\CommandEvent;
    use pocketmine\lang\BaseLang;
    use pocketmine\lang\TextContainer;
    use pocketmine\Player;
    use pocketmine\plugin\Plugin;
    
    /**
     * Class LoggerListener
     * @package PJZ9n\LoggerPM\Listener
     */
    class LoggerListener extends LoggerPMListener
    {
        
        /** @var LogManager */
        private $logManager;
        
        public function __construct(Plugin $plugin, BaseLang $lang, LogManager $logManager)
        {
            parent::__construct($plugin, $lang);
            $this->logManager = $logManager;
        }
        
        /**
         * @param BlockBreakEvent $event
         *
         * @priority MONITOR
         */
        public function onBlockBreak(BlockBreakEvent $event): void
        {
            $block = $event->getBlock()->__toString();
            $item = $event->getItem()->__toString();
            $instaBreak = $event->getInstaBreak();
            $blockDrops = [];
            foreach ($event->getDrops() as $drop) {
                $blockDrops[] = $drop->__toString();
            }
            $xpDrops = $event->getXpDropAmount();
            $this->logManager->addActionLog($event->getPlayer()->getName(), LogActionType::LOG_TYPE_BLOCK_BREAK, [
                "Block" => $block,
                "Item" => $item,
                "InstaBreak" => $instaBreak,
                "BlockDrops" => $blockDrops,
                "XpDrops" => $xpDrops,
            ], $event->isCancelled());
        }
        
        /**
         * @param BlockPlaceEvent $event
         *
         * @priority MONITOR
         */
        public function onBreakPlace(BlockPlaceEvent $event): void
        {
            $block = $event->getBlock()->__toString();
            $item = $event->getItem()->__toString();
            $blockReplace = $event->getBlockReplaced()->__toString();
            $blockAgainst = $event->getBlockAgainst()->__toString();
            $this->logManager->addActionLog($event->getPlayer()->getName(), LogActionType::LOG_TYPE_BLOCK_PLACE, [
                "Block" => $block,
                "Item" => $item,
                "BlockReplace" => $blockReplace,
                "BlockAgainst" => $blockAgainst,
            ], $event->isCancelled());
        }
        
        /**
         * @param PlayerInteractEvent $event
         *
         * @priority MONITOR
         */
        public function onPlayerInteract(PlayerInteractEvent $event): void
        {
            if ($event->getAction() !== PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                return;
            }
            $blockTouched = $event->getBlock()->__toString();
            $touchVector = $event->getTouchVector()->__toString();
            $blockFace = $event->getFace();
            $this->logManager->addActionLog($event->getPlayer()->getName(), LogActionType::LOG_TYPE_BLOCK_TOUCH, [
                "BlockTouched" => $blockTouched,
                "TouchVector" => $touchVector,
                "BlockFace" => $blockFace,
            ], $event->isCancelled());
        }
        
        /**
         * @param PlayerLoginEvent $event
         *
         * @priority MONITOR
         */
        public function onPlayerLogin(PlayerLoginEvent $event): void
        {
            $kickMessage = $event->getKickMessage();
            $this->logManager->addActionLog($event->getPlayer()->getName(), LogActionType::LOG_TYPE_PLAYER_LOGIN, [
                "KickMessage" => $kickMessage,
            ], $event->isCancelled());
        }
        
        /**
         * @param PlayerJoinEvent $event
         *
         * @priority MONITOR
         */
        public function onPlayerJoin(PlayerJoinEvent $event): void
        {
            $joinMessage = $event->getJoinMessage();
            $joinMessage = $joinMessage instanceof TextContainer ? $joinMessage->getText() : $joinMessage;
            $this->logManager->addActionLog($event->getPlayer()->getName(), LogActionType::LOG_TYPE_PLAYER_JOIN, [
                "JoinMessage" => $joinMessage,
            ]);
        }
        
        /**
         * @param PlayerQuitEvent $event
         *
         * @priority MONITOR
         */
        public function onPlayerQuit(PlayerQuitEvent $event): void
        {
            $quitMessage = $event->getQuitMessage();
            $quitMessage = $quitMessage instanceof TextContainer ? $quitMessage->getText() : $quitMessage;
            $quitReason = $event->getQuitReason();
            $this->logManager->addActionLog($event->getPlayer()->getName(), LogActionType::LOG_TYPE_PLAYER_QUIT, [
                "QuitMessage" => $quitMessage,
                "QuitReason" => $quitReason,
            ]);
        }
        
        /**
         * @param PlayerChatEvent $event
         *
         * @priority MONITOR
         */
        public function onPlayerChat(PlayerChatEvent $event): void
        {
            $message = $event->getMessage();
            $format = $event->getFormat();
            $recipients = [];
            foreach ($event->getRecipients() as $recipient) {
                $recipients[] = $recipient->getName();
            }
            $this->logManager->addActionLog($event->getPlayer()->getName(), LogActionType::LOG_TYPE_PLAYER_CHAT, [
                "Message" => $message,
                "Format" => $format,
                "Recipients" => $recipients,
            ], $event->isCancelled());
        }
        
        /**
         * @param EntityDamageByEntityEvent $event
         *
         * @priority MONITOR
         */
        public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void
        {
            $entity = $event->getEntity();
            if (!$entity instanceof Player) {
                return;
            }
            $damager = $event->getDamager();
            if (!$damager instanceof Player) {
                return;
            }
            $cause = $event->getCause();
            $baseDamage = $event->getBaseDamage();
            $originalBase = $event->getOriginalBaseDamage();
            $modifiers = $event->getModifiers();
            $originals = $event->getModifiers();//WARNING
            $attackCooldown = $event->getAttackCooldown();
            $knockBack = $event->getKnockBack();
            
            //攻撃した側
            $attackTo = $entity->getName();
            $this->logManager->addActionLog($damager->getName(), LogActionType::LOG_TYPE_PLAYER_ATTACK, [
                "Cause" => $cause,
                "BaseDamage" => $baseDamage,
                "OriginalBase" => $originalBase,
                "Modifiers" => $modifiers,
                "Originals" => $originals,
                "AttackCoolDown" => $attackCooldown,
                "AttackTo" => $attackTo,
                "KnockBack" => $knockBack,
            ], $event->isCancelled());
            
            //攻撃を受けた側
            $damager = $damager->getName();
            $this->logManager->addActionLog($entity->getName(), LogActionType::LOG_TYPE_PLAYER_DAMAGE, [
                "Cause" => $cause,
                "BaseDamage" => $baseDamage,
                "OriginalBase" => $originalBase,
                "Modifiers" => $modifiers,
                "Originals" => $originals,
                "AttackCoolDown" => $attackCooldown,
                "Damanger" => $damager,
                "KnockBack" => $knockBack,
            ], $event->isCancelled());
        }
        
        /**
         * @param CommandEvent $event
         *
         * @priority MONITOR
         */
        public function onCommand(CommandEvent $event): void
        {
            $sender = $event->getSender();
            if (!$sender instanceof Player) {
                return;
            }
            $command = $event->getCommand();
            $this->logManager->addActionLog($sender->getName(), LogActionType::LOG_TYPE_DISPATCH_COMMAND, [
                "Command" => $command,
            ], $event->isCancelled());
        }
        
    }