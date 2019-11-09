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
    
    
    namespace PJZ9n\LoggerPM\Library\Form;
    
    use pocketmine\form\Form;
    use pocketmine\form\FormValidationException;
    use pocketmine\lang\BaseLang;
    use pocketmine\Player;
    use pocketmine\plugin\Plugin;
    use pocketmine\utils\TextFormat;
    
    /**
     * Class LoggerPMForm
     * @package PJZ9n\LoggerPM\Library\Form
     */
    abstract class LoggerPMForm implements Form
    {
        
        /** @var Plugin */
        private $plugin;
        
        /** @var BaseLang */
        private $lang;
        
        /** @var Player */
        private $player;
        
        /** @var FormProcessor */
        private $processor;
        
        /** @var array */
        private $data = [];
        
        /** @var null string|null */
        private $permission = null;
        
        /** @var string|null */
        private $permissionMessage = null;
        
        /** @var string|null */
        private $errorMessage = null;
        
        /**
         * @param Plugin $plugin
         * @param Player $player
         */
        public function __construct(Plugin $plugin, BaseLang $lang, Player $player)
        {
            $this->plugin = $plugin;
            $this->lang = $lang;
            $this->player = $player;
            $this->processor = $plugin;
        }
        
        /**
         * @param Player $player
         * @param mixed $data
         * @throws FormValidationException
         */
        final public function handleResponse(Player $player, $data): void
        {
            if (!$this->plugin->isEnabled()) {
                return;
            }
            
            if (!$this->testPermission($player)) {
                return;
            }
            
            if ($data !== null) {
                try {
                    $validated = $this->processor->validate($data);
                } catch (FormValidationException $exception) {
                    $player->sendMessage(TextFormat::RED . $this->lang->translateString("form.validate.error"));
                    throw $exception;
                }
                $success = $this->processor->onHandle($player, $validated);
            } else {
                $success = $this->processor->onNull($player);
            }
            
            if (!$success && $this->errorMessage !== "") {
                if ($this->errorMessage !== null) {
                    $player->sendMessage($this->errorMessage);
                } else {
                    $player->sendMessage(TextFormat::RED . $this->lang->translateString("form.unexpected.error"));
                }
            }
        }
        
        /**
         * @return array
         */
        final public function jsonSerialize(): array
        {
            return $this->data;
        }
        
        /**
         * @return string|null
         */
        public function getPermission(): ?string
        {
            return $this->permission;
        }
        
        /**
         * @param string|null $permission
         */
        public function setPermission(?string $permission = null)
        {
            $this->permission = $permission;
        }
        
        /**
         * @param Player $target
         * @return bool
         */
        public function testPermission(Player $target): bool
        {
            if ($this->testPermissionSilent($target)) {
                return true;
            }
            
            if ($this->permissionMessage === null) {
                $target->sendMessage(TextFormat::RED . $this->lang->translateString("form.permission.error"));
            } else if ($this->permissionMessage !== "") {
                $target->sendMessage(str_replace("<permission>", $this->permission, $this->permissionMessage));
            }
            
            return false;
        }
        
        /**
         * @param Player $target
         * @return bool
         */
        public function testPermissionSilent(Player $target): bool
        {
            if ($this->permission === null || $this->permission === "") {
                return true;
            }
            
            foreach (explode(";", $this->permission) as $permission) {
                if ($target->hasPermission($permission)) {
                    return true;
                }
            }
            
            return false;
        }
        
        /**
         * @return array
         */
        public function getData(): array
        {
            return $this->data;
        }
        
        /**
         * @param array $data
         */
        public function setData(array $data): void
        {
            $this->data = $data;
        }
        
        /**
         * @return string|null
         */
        public function getPermissionMessage(): ?string
        {
            return $this->permissionMessage;
        }
        
        /**
         * @param string|null $permissionMessage
         */
        public function setPermissionMessage(?string $permissionMessage): void
        {
            $this->permissionMessage = $permissionMessage;
        }
        
        /**
         * @return string|null
         */
        public function getErrorMessage(): ?string
        {
            return $this->errorMessage;
        }
        
        /**
         * @param string|null $errorMessage
         */
        public function setErrorMessage(?string $errorMessage): void
        {
            $this->errorMessage = $errorMessage;
        }
        
        /**
         * @return FormProcessor
         */
        public function getProcessor(): FormProcessor
        {
            return $this->processor;
        }
        
        /**
         * @param FormProcessor $processor
         */
        public function setProcessor(FormProcessor $processor): void
        {
            $this->processor = $processor;
        }
        
        /**
         * @return Plugin
         */
        public function getPlugin(): Plugin
        {
            return $this->plugin;
        }
        
    }