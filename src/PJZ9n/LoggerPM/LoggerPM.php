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
    
    namespace PJZ9n\LoggerPM;
    
    use pocketmine\command\Command;
    use pocketmine\event\Listener;
    use pocketmine\plugin\PluginBase;
    use RuntimeException;
    
    /**
     * Class LoggerPM
     * @package PJZ9n\LoggerPM
     */
    class LoggerPM extends PluginBase
    {
        
        /** @var LoggerPM */
        private static $instance = null;
        
        public static function getInstance(): LoggerPM
        {
            if (self::$instance === null) {
                throw new RuntimeException("Attempt to retrieve This instance outside main thread");
            }
            return self::$instance;
        }
        
        public function onLoad(): void
        {
            self::$instance = $this;
        }
        
        public function onEnable(): void
        {
            $this->initConfig();
            $this->initListeners();
            $this->initCommands();
        }
        
        private function initConfig(): void
        {
            $this->saveDefaultConfig();
        }
        
        private function initListeners(): void
        {
            /** @var $listeners Listener[] */
            $listeners = [
                //
            ];
            foreach ($listeners as $listener) {
                $this->getServer()->getPluginManager()->registerEvents($listener, $this);
            }
        }
        
        private function initCommands(): void
        {
            /** @var $commands Command[] */
            $commands = [
                //
            ];
            $this->getServer()->getCommandMap()->registerAll("LoggerPM", $commands);
        }
        
    }