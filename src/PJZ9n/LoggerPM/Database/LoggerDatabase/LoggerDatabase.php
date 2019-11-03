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

    use ErrorException;
    use PJZ9n\LoggerPM\Library\Database\Database;

    /**
     * Interface LoggerDatabase
     * @package PJZ9n\LoggerPM\Database\LoggerDatabase
     */
    interface LoggerDatabase extends Database
    {
    
        /**
         * アクションログに追加する
         * @param string $playerName プレイヤー名
         * @param string $actionType アクションタイプ
         * @param array|null $actionData データ
         * @param bool|null $actionCancelled キャンセルされたか
         * @throws ErrorException JSONのエンコードに失敗したとき
         * @see LogActionType $actionTypeに使う
         */
        public function addActionLog(string $playerName, string $actionType, ?array $actionData = null, ?bool $actionCancelled = null): void;
    
        /**
         * 全てのアクションログを取得する
         * 注意: どうしても必要な時以外使わない(パフォーマンスの問題)
         * @param int|null $start 開始(UNIX時間(sec))
         * @param int|null $end 終了(UNIX時間(sec))
         * @param int|null $limit 取得リミット
         * @return array
         * @throws ErrorException JSONのデコードに失敗したとき
         */
        public function getActionLogAll(?int $start = null, ?int $end = null, ?int $limit = null): array;
    
        /**
         * プレイヤー名からアクションログを取得する
         * @param string $playerName プレイヤー名
         * @param int|null $start 開始(UNIX時間(sec))
         * @param int|null $end 終了(UNIX時間(sec))
         * @param int|null $limit 取得リミット
         * @return array
         * @throws ErrorException JSONのデコードに失敗したとき
         */
        public function getActionLogByPlayerName(string $playerName, ?int $start = null, ?int $end = null, ?int $limit = null): array;
    
        /**
         * アクションからアクションログを取得する
         * @param string $actionType アクションタイプ
         * @param int|null $start 開始(UNIX時間(sec))
         * @param int|null $end 終了(UNIX時間(sec))
         * @param int|null $limit 取得リミット
         * @return array
         * @throws ErrorException JSONのデコードに失敗したとき
         */
        public function getActionLogByActionType(string $actionType, ?int $start = null, ?int $end = null, ?int $limit = null): array;
        
        
    }