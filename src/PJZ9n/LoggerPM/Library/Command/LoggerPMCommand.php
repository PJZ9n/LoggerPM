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
    
    namespace PJZ9n\LoggerPM\Library\Command;
    
    use pocketmine\command\CommandSender;
    use pocketmine\command\PluginCommand;
    use pocketmine\command\utils\InvalidCommandSyntaxException;
    use pocketmine\lang\BaseLang;
    use pocketmine\Player;
    use pocketmine\plugin\Plugin;
    use pocketmine\utils\TextFormat;
    
    abstract class LoggerPMCommand extends PluginCommand
    {
        
        /** @var BaseLang */
        private $lang;
        
        /** @var bool */
        private $onlyPlayer;
        
        /** @var string|null */
        private $onlyPlayerMessage;
        
        public function __construct(Plugin $owner, BaseLang $lang, string $name)
        {
            parent::__construct($name, $owner);
            
            $this->lang = $lang;
            $this->onlyPlayer = false;
        }
        
        public function execute(CommandSender $sender, string $commandLabel, array $args)
        {
            if (!$this->getPlugin()->isEnabled()) {
                return false;
            }
            
            if (!$this->testPermission($sender)) {
                return false;
            }
            
            if ($this->onlyPlayer && !$sender instanceof Player) {
                if ($this->onlyPlayerMessage !== "") {
                    if ($this->onlyPlayerMessage !== null) {
                        $sender->sendMessage($this->onlyPlayerMessage);
                    } else {
                        $sender->sendMessage(TextFormat::RED . $this->lang->translateString("command.onlyplayer.error"));
                    }
                }
                return false;
            }
            
            $success = $this->getExecutor()->onCommand($sender, $this, $commandLabel, $args);
            
            if (!$success and $this->usageMessage !== "") {
                throw new InvalidCommandSyntaxException();
            }
            return $success;
        }
        
        public function getOnlyPlayer(): bool
        {
            return $this->onlyPlayer;
        }
        
        public function setOnlyPlayer(bool $onlyPlayer): void
        {
            $this->onlyPlayer = $onlyPlayer;
        }
        
        public function getOnlyPlayerMessage(): ?string
        {
            return $this->onlyPlayerMessage;
        }
        
        public function setOnlyPlayerMessage(?string $onlyPlayerMessage): void
        {
            $this->onlyPlayerMessage = $onlyPlayerMessage;
        }
        
        /**
         * @return BaseLang
         */
        public function getLang(): BaseLang
        {
            return $this->lang;
        }
        
    }