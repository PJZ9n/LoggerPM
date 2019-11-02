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
    
    namespace PJZ9n\LoggerPM\Library\Database\Type;
    
    use PJZ9n\LoggerPM\Library\Database\Database;
    use SQLite3;

    /**
     * Class SqliteDatabase
     * @package PJZ9n\LoggerPM\Library\Database\Type
     */
    abstract class SqliteDatabase implements Database
    {
        
        /** @var string */
        private $filePath;
        
        /** @var SQLite3 */
        private $sqlite3;
        
        public function __construct(string $filePath)
        {
            $this->filePath = $filePath;
            
            $this->sqlite3 = new SQLite3($filePath);
        }
        
        public function __destruct()
        {
            $this->sqlite3->close();
        }
    
        public function getFilePath(): string
        {
            return $this->filePath;
        }
        
        protected function getSqlite3(): SQLite3
        {
            return $this->sqlite3;
        }
        
    }