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
    
    namespace PJZ9n\LoggerPM\Task;
    
    use PJZ9n\LoggerPM\Library\Task\LoggerPMTask;
    use PJZ9n\LoggerPM\Log\LogManager;
    use pocketmine\plugin\Plugin;
    
    class DebugTask extends LoggerPMTask
    {
        
        /** @var LogManager */
        private $logManager;
        
        public function __construct(Plugin $plugin, LogManager $logManager)
        {
            parent::__construct($plugin);
            $this->logManager = $logManager;
        }
        
        public function onRun(int $currentTick)
        {
            ob_start();
            var_dump($this->logManager->getActionLog());
            $out = ob_get_contents();
            ob_end_clean();
            file_put_contents($this->getPlugin()->getDataFolder() . "var_dump.txt", $out);
        }
        
    }