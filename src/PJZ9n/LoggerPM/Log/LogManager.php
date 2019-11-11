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
    
    namespace PJZ9n\LoggerPM\Log;
    
    use PJZ9n\LoggerPM\Database\LoggerDatabase\LoggerDatabase;
    use PJZ9n\LoggerPM\Database\LoggerDatabase\SQLiteLoggerDatabase;
    use pocketmine\plugin\Plugin;
    
    /**
     * Class LogManager
     * @package PJZ9n\LoggerPM\Log
     */
    class LogManager
    {
        
        /** @var Plugin */
        private $plugin;
        
        /** @var LoggerDatabase|null */
        private $loggerDataBase;
        
        /** @var string */
        private $filePath;
        
        public function __construct(Plugin $plugin)
        {
            $this->plugin = $plugin;
            
            $folderPath = $this->plugin->getDataFolder() . "db/";
            $fileName = "log.sqlite3";
            
            !is_dir($folderPath) ? mkdir($folderPath, 0777, true) : true;
            
            $this->filePath = $folderPath . $fileName;
            
            $this->loggerDataBase = new SQLiteLoggerDatabase($this->filePath);
        }
        
        /**
         * アクションログに追加する
         * @param string $playerName プレイヤー名
         * @param string $actionType アクションタイプ
         * @param array|null $actionData データ
         * @param bool|null $actionCancelled キャンセルされたか
         * @param int|null $createdAt 時間
         * @see LogActionType $actionTypeに使う
         */
        public function addActionLog(string $playerName, string $actionType, ?array $actionData = null, ?bool $actionCancelled = null, ?int $createdAt = null): void
        {
            $this->loggerDataBase->addActionLog($playerName, $actionType, $actionData, $actionCancelled, $createdAt ?? time());
        }
        
        /**
         * アクションログを取得する
         * @param string|null $playerName プレイヤー名
         * @param string|null $actionType アクションタイプ
         * @param int|null $start 開始(UNIX時間)
         * @param int|null $end 終了(UNIX時間)
         * @param int|null $limit 取得リミット件数
         * @return array ログデータ
         */
        public function getActionLog(?string $playerName = null, ?string $actionType = null, ?int $start = null, ?int $end = null, ?int $limit = null): array
        {
            return $this->loggerDataBase->getActionLog($playerName, $actionType, $start, $end, $limit);
        }
        
    }