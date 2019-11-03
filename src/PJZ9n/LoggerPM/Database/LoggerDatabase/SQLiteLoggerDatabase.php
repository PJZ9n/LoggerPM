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
    
    namespace PJZ9n\LoggerPM\Database\LoggerDatabase;
    
    use PJZ9n\LoggerPM\Library\Database\Type\SqliteDatabase;
    use PJZ9n\LoggerPM\Log\LogActionType;
    use SQLite3;
    
    /**
     * Class SQLiteLoggerDatabase
     * @package PJZ9n\LoggerPM\Database\LoggerDatabase
     */
    class SQLiteLoggerDatabase extends SqliteDatabase implements LoggerDatabase
    {
        
        public function __construct(string $filePath)
        {
            parent::__construct($filePath);
            
            $this->getSqlite3()->exec(
                "CREATE TABLE IF NOT EXISTS action_logger(" .
                "id INTEGER NOT NULL PRIMARY KEY," .
                "player_name TEXT NOT NULL," .
                "action_type TEXT NOT NULL," .
                "action_data JSON," .//データ JSON
                "action_cancelled NUMERIC," .//キャンセルされたか 論理値
                "created_at NUMERIC NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP,'localtime'))" .
                ")"
            );
        }
        
        public function addActionLog(string $playerName, string $actionType, ?array $actionData = null, ?bool $actionCancelled = null): void
        {
            $playerName = SQLite3::escapeString($playerName);
            $actionType = SQLite3::escapeString($actionType);
            if ($actionData === null) {
                $actionData = "null";
            } else {
                $actionData = "'" . SQLite3::escapeString(json_encode($actionData, JSON_UNESCAPED_UNICODE)) . "'";
            }
            if ($actionCancelled === null) {
                $actionCancelled = "null";
            } else {
                if (!$actionCancelled) {
                    $actionCancelled = "false";
                } else {
                    $actionCancelled = "true";
                }
            }
            
            $sql = "INSERT INTO action_logger(" .
                "player_name, action_type, action_data, action_cancelled" .
                ") VALUES (" .
                "'{$playerName}', '{$actionType}', {$actionData}, {$actionCancelled}" .
                ")";
            
            $this->getSqlite3()->exec($sql);
        }
        
        /**
         * 全てのアクションログを取得する
         * 注意: どうしても必要な時以外使わない(パフォーマンスの問題)
         * @return array
         */
        public function getAllActionLog(): array
        {
            $sql = "SELECT * FROM action_logger";
            
            $result = $this->getSqlite3()->query($sql);
            
            $allActionLogs = [];
            
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                if ($row["action_data"] !== null) {
                    $row["action_data"] = json_decode($row["action_data"], true);
                }
                $allActionLogs[] = $row;
            }
            
            return $allActionLogs;
        }
        
    }