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
    
        //HACK: とりあえず動くけど最悪なコード
        
        /**
         * 条件のSQL文を取得する TODO: 適切な関数名に変更する
         * @param int|null $start
         * @param int|null $end
         * @param int|null $limit
         * @param string|null $opt
         * @return string
         */
        private static function getConditionsSqlStatement(?int $start, ?int $end, ?int $limit, ?string $opt = null): string
        {
            $startDateTime = $start === null ? null : DateTime::getDateTimeByUnixTime($start);
            $endDateTime = $end === null ? null : DateTime::getDateTimeByUnixTime($end);
    
            $sql = "";
    
            $sql .= $opt !== null ? "WHERE\n" . $opt : "";
    
            if ($startDateTime !== null) {
                $sql .= $opt !== null ? "AND\n" : "WHERE\n";
                $sql .= "created_at >= '{$startDateTime}'\n";
            }
            if ($endDateTime !== null) {
                $sql .= $opt !== null || $startDateTime !== null ? "AND\n" : "";
                $sql .= "created_at <= '{$endDateTime}'\n";
            }
            if ($limit !== null) {
                $sql .= "LIMIT {$limit}";
            }
    
            return $sql;
        }
    
        /**
         * ログを適切な形式に変更する
         * @param array $logs
         * @return array
         */
        private static function convertLogFormat(array $logs): array
        {
            $converted = [];
            foreach ($logs as $index => $log) {
                $log["action_data"] = $log["action_data"] !== null ? json_decode($log["action_data"], true) : null;
                $log["action_cancelled"] = $log["action_cancelled"] !== null ? boolval($log["action_cancelled"]) : null;
                $log["created_at"] = DateTime::getUnixTimeByDateTime($log["created_at"]);
                $converted[$index] = $log;
            }
            return $converted;
        }
        
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
    
        public function addActionLog(string $playerName, string $actionType, ?array $actionData = null, ?bool $actionCancelled = null, ?int $createdAt = null): void
        {
            $sql = /** @lang SQLite */
                <<< SQL
                INSERT INTO action_logger(
                    player_name,
                    action_type,
                    action_data,
                    action_cancelled,
                    created_at
                ) VALUES (
                    :player_name,
                    :action_type,
                    :action_data,
                    :action_cancelled,
                    :created_at
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
            $createdAt === null ?
                $stmt->bindValue(":created_at", null, SQLITE3_NULL) :
                $stmt->bindValue(":created_at", DateTime::getDateTimeByUnixTime($createdAt), SQLITE3_TEXT);
            $stmt->execute();
            $stmt->close();
        }
    
        public function getActionLog(?string $playerName = null, ?string $actionType = null, ?int $start = null, ?int $end = null, ?int $limit = null): array
        {
            $sql = "SELECT * FROM action_logger\n";
    
            $opt = "";
    
            if ($playerName !== null) {
                $opt .= "player_name = :player_name\n";
            }
            if ($actionType !== null) {
                $opt .= $playerName !== null ? "AND\n" : "";
                $opt .= "action_type = :action_type\n";
            }
    
            $opt = $opt === "" ? null : $opt;
    
            //var_dump($opt);
            //var_dump(str_replace("\n", "", $opt));
    
            $sql .= self::getConditionsSqlStatement($start, $end, $limit, $opt);
    
            //var_dump(str_replace("\n", " ", $sql));
    
            $stmt = $this->getSqlite3()->prepare($sql);
            $playerName === null ?
                $stmt->bindValue(":player_name", null, SQLITE3_NULL) :
                $stmt->bindValue(":player_name", $playerName, SQLITE3_TEXT);
            $actionType === null ?
                $stmt->bindValue(":action_type", null, SQLITE3_NULL) :
                $stmt->bindValue(":action_type", $actionType, SQLITE3_TEXT);
            $result = $stmt->execute();
            
            $return = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $return[] = $row;
            }
            $stmt->close();
            return self::convertLogFormat($return);
        }
        
    }