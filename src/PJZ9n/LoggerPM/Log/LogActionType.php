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
    
    interface LogActionType
    {
        
        /** @var string ブロック破壊 */
        public const LOG_TYPE_BLOCK_BREAK = "block-break";
        
        /** @var string ブロック設置 */
        public const LOG_TYPE_BLOCK_PLACE = "block-place";
        
        /** @var string ブロック接触 */
        public const LOG_TYPE_BLOCK_TOUCH = "block-touch";
        
        /** @var string プレイヤーログイン */
        public const LOG_TYPE_PLAYER_LOGIN = "player-login";
        
        /** @var string プレイヤーJoin */
        public const LOG_TYPE_PLAYER_JOIN = "player-join";
        
        /** @var string プレイヤーQuit */
        public const LOG_TYPE_PLAYER_QUIT = "player-quit";
        
        /** @var string プレイヤーチャット */
        public const LOG_TYPE_PLAYER_CHAT = "player-chat";
        
        /** @var string エンティティ攻撃 */
        public const LOG_TYPE_ENTITY_ATTACK = "entity-attack";
        
        /** @var string プレイヤーダメージ */
        public const LOG_TYPE_PLAYER_DAMAGE = "player-damage";
        
        /** @var string コマンド実行 */
        public const LOG_TYPE_DISPATCH_COMMAND = "dispatch-command";
        
    }