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
    
    namespace PJZ9n\LoggerPM\Utils;

    /**
     * Class DateTime
     * @package PJZ9n\LoggerPM\Utils
     */
    final class DateTime
    {
        
        /** @var string DateTimeのフォーマット */
        private const DATETIME_FORMAT = "Y-m-d H:i:s";
        
        /**
         * 現在のUNIX時間をミリ秒単位で取得する
         * @return float UNIX時間(ms)
         */
        public static function getMilliSecond(): float
        {
            return floor(strval(microtime(true) * 1000));
        }
        
        /**
         * UNIX時間からDateTimeを取得する
         * @param int $time UNIX時間
         * @return string DateTime
         */
        public static function getDateTimeByUnixTime(int $time): string
        {
            return date(self::DATETIME_FORMAT, $time);
        }
        
        /**
         * DateTimeからUNIX時間を取得する
         * @param string $dateTime DateTime
         * @return int UNIX時間
         */
        public static function getUnixTimeByDateTime(string $dateTime): int
        {
            return strtotime($dateTime);
        }
        
    }