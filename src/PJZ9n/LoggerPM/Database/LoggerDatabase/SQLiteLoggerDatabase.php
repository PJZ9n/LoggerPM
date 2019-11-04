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
    use PJZ9n\LoggerPM\Utils\DateTime;
    
    /**
     * Class SQLiteLoggerDatabase
     * @package PJZ9n\LoggerPM\Database\LoggerDatabase
     */
    class SQLiteLoggerDatabase extends SqliteDatabase implements LoggerDatabase
    {
    
        public function __construct(string $filePath)
        {
            parent::__construct($filePath);
    
            $sql = /** @lang SQLite */
                <<< SQL
                CREATE TABLE IF NOT EXISTS action_logger(
                    id INTEGER NOT NULL PRIMARY KEY,
                    player_name TEXT NOT NULL,
                    action_type TEXT NOT NULL,
                    action_data JSON,
                    action_cancelled NUMERIC,
                    created_at NUMERIC NOT NULL DEFAULT (datetime(CURRENT_TIMESTAMP,'localtime'))
                )
            SQL;
            $this->getSqlite3()->exec($sql);
        }
    
        public function addActionLog(string $playerName, string $actionType, ?array $actionData = null, ?bool $actionCancelled = null): void
        {
            $sql = /** @lang SQLite */
                <<< SQL
                INSERT INTO action_logger(
                    player_name,
                    action_type,
                    action_data,
                    action_cancelled
                ) VALUES (
                    :player_name,
                    :action_type,
                    :action_data,
                    :action_cancelled
                )
            SQL;
    
            $stmt = $this->getSqlite3()->prepare($sql);
            $stmt->bindValue(":player_name", $playerName, SQLITE3_TEXT);
            $stmt->bindValue(":action_type", $actionType, SQLITE3_TEXT);
            $actionData === null ?
                $stmt->bindValue(":action_data", null, SQLITE3_NULL) :
                $stmt->bindValue(":action_data", json_encode($actionData), SQLITE3_TEXT);
            $actionCancelled === null ?
                $stmt->bindValue(":action_cancelled", null, SQLITE3_NULL) :
                $stmt->bindValue(":action_cancelled", $actionCancelled, SQLITE3_INTEGER);
            $stmt->execute();
            $stmt->close();
        }
    
        public function getActionLogAll(?int $start = null, ?int $end = null, ?int $limit = null): array
        {
            $sql = "SELECT * FROM action_logger\n";
            $sql .= $this->getConditionsSqlStatement($start, $end, $limit);
    
            $result = $this->getSqlite3()->query($sql);
    
            $return = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $return[] = $row;
            }
            return $return;
        }
    
        public function getActionLogByPlayerName(string $playerName, ?int $start = null, ?int $end = null, ?int $limit = null): array
        {
            // TODO: Implement getActionLogByPlayerName() method.
            return [];
        }
    
        public function getActionLogByActionType(string $actionType, ?int $start = null, ?int $end = null, ?int $limit = null): array
        {
            // TODO: Implement getActionLogByActionType() method.
            return [];
        }
    
        /**
         * 条件のSQL文を取得する TODO: 適切な関数名に変更する
         * @param int|null $start
         * @param int|null $end
         * @param int|null $limit
         * @return string
         */
        private function getConditionsSqlStatement(?int $start, ?int $end, ?int $limit): string
        {
            $startDateTime = $start === null ? null : DateTime::getDateTimeByUnixTime($start);
            $endDateTime = $end === null ? null : DateTime::getDateTimeByUnixTime($end);
        
            $sql = "";
        
            if ($startDateTime !== null) {
                $sql .= "WHERE\n";
                $sql .= "created_at >= '{$startDateTime}'\n";
            }
            if ($endDateTime !== null) {
                $sql .= $startDateTime !== null ? "AND\n" : "WHERE\n";
                $sql .= "created_at <= '{$endDateTime}'\n";
            }
            if ($limit !== null) {
                $sql .= "LIMIT {$limit}";
            }
        
            return $sql;
        }
        
    }