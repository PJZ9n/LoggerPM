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
    
    namespace PJZ9n\LoggerPM\Database;
    
    use PJZ9n\LoggerPM\Library\Database\Type\SqliteDatabase;
    
    /**
     * Class ActionLoggerDatabase
     * @package PJZ9n\LoggerPM\Database
     */
    class ActionLoggerDatabase extends SqliteDatabase
    {
        
        public function __construct(string $filePath)
        {
            parent::__construct($filePath);
            
            $sql = "CREATE TABLE IF NOT EXISTS action_logger(" .
                "id INTEGER NOT NULL PRIMARY KEY," .
                "player_name TEXT NOT NULL," .
                "action_type TEXT NOT NULL," .
                "action_data JSON NOT NULL," .
                "created_at NUMERIC NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP,'localtime'))" .
                ")";
            $this->getSqlite3()->exec($sql);
        }
        
    }