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

    /**
     * Class YamlDatabase
     * @package PJZ9n\LoggerPM\Library\Database\Type
     */
    abstract class YamlDatabase implements Database
    {
        
        /** @var string */
        private $filePath;
    
        /** @var array */
        private $data;
        
        public function __construct(string $filePath)
        {
            $this->filePath = $filePath;
    
            !file_exists($filePath) ?
                $this->data = [] :
                $this->data = json_decode((file_get_contents($filePath)), true);
        }
        
        public function __destruct()
        {
            file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT));
        }
        
        public function getFilePath(): string
        {
            return $this->filePath;
        }
        
    }