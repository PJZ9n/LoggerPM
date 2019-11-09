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
    
    namespace PJZ9n\LoggerPM;

    use PJZ9n\LoggerPM\Log\LogManager;
    use pocketmine\command\Command;
    use pocketmine\event\Listener;
    use pocketmine\lang\BaseLang;
    use pocketmine\plugin\PluginBase;

    /**
     * Class LoggerPM
     * @package PJZ9n\LoggerPM
     */
    class LoggerPM extends PluginBase
    {
    
        /** @var LogManager */
        private $logManager;
    
        /** @var BaseLang */
        private $lang;
    
        public function onEnable(): void
        {
            $this->initialize();
        
            $this->sendStartupMessage();
        }
    
        private function initialize(): void
        {
            $this->saveDefaultConfig();
        
            //OPTIMIZE
            $rawConfigFile = file_get_contents($this->getDataFolder() . "config.yml");
            $rawConfigFile = str_replace("{language}", $this->getServer()->getLanguage()->getLang(), $rawConfigFile);
            file_put_contents($this->getDataFolder() . "config.yml", $rawConfigFile);
        
            $this->reloadConfig();
        
            $this->logManager = new LogManager($this);
        
            foreach ($this->getResources() as $resource) {
                $path = explode(DIRECTORY_SEPARATOR, $resource->getPath());
                $last = end($path);//WARNING: 内部ポインタを移動させます
                if ($last !== "languages" || $resource->getExtension() !== "ini") {
                    continue;
                }
                $oldLangFile = $this->getDataFolder() . "languages/" . $resource->getFilename();
                $newLangFile = $last . DIRECTORY_SEPARATOR . $resource->getFilename();
                if (!file_exists($oldLangFile)) {
                    $this->saveResource($newLangFile);
                    continue;
                }
                $old = array_map('\stripcslashes', parse_ini_file($oldLangFile, false, INI_SCANNER_RAW));
                if (!isset($old["version"]) || !isset($old["allow-overwrite"])) {
                    $this->getLogger()->warning("Failed to load language file: {$oldLangFile}");
                    $this->getLogger()->warning("A required parameter is missing.");
                    continue;
                }
                $new = array_map('\stripcslashes', parse_ini_file($resource->getRealPath(), false, INI_SCANNER_RAW));
                if (!isset($new["version"]) || !isset($new["allow-overwrite"])) {
                    $this->getLogger()->warning("Failed to load language file (resources): {$resource->getRealPath()}");
                    $this->getLogger()->warning("A required parameter is missing.");
                    continue;
                }
                $allowOverwrte = $old["allow-overwrite"];
                $oldVersion = explode(".", $old["version"]);
                $newVersion = explode(".", $new["version"]);
                if ($oldVersion[0] < $newVersion[0] || $oldVersion[1] < $newVersion[1] || $oldVersion[2] < $newVersion[2]) {
                    if ($allowOverwrte === "yes") {
                        $this->saveResource($newLangFile, true);
                        $this->getLogger()->notice("Updated language file {$oldLangFile} from {$old["version"]} to {$new["version"]}!");
                        //$this->getLogger()->notice("言語ファイル {$oldLangFile} を {$old["version"]} から {$new["version"]} にアップデートしました！");
                    } /** @noinspection PhpStatementHasEmptyBodyInspection */ else {
                        //$this->getLogger()->notice("Language file {$oldLangFile} could not be updated because overwrite is off.");
                        //$this->getLogger()->notice("言語ファイル {$oldLangFile} の上書きが無効なため、アップデートができませんでした。");
                    }
                } /** @noinspection PhpStatementHasEmptyBodyInspection */ else {
                    //$this->getLogger()->info("Language file {$oldLangFile} ({$old["version"]}) is the already latest!");
                    //$this->getLogger()->info("言語ファイル {$oldLangFile} ($old["version"]) はすでに最新です！");
                }
            }
        
            $this->lang = new BaseLang(strval($this->getConfig()->get("language")), $this->getDataFolder() . "languages/");
            $this->getLogger()->info($this->lang->translateString("language.selected", [$this->lang->getName(), $this->lang->getLang()]));
        
            /** @var $listeners Listener[] */
            $listeners = [
                //
            ];
            foreach ($listeners as $listener) {
                $this->getServer()->getPluginManager()->registerEvents($listener, $this);
            }
        
            /** @var $commands Command[] */
            $commands = [
                //
            ];
            $this->getServer()->getCommandMap()->registerAll("LoggerPM", $commands);
        }
    
        private function sendStartupMessage(): void
        {
            $this->getLogger()->notice($this->lang->translateString("license1"));
            $this->getLogger()->notice($this->lang->translateString("license2", ["https://github.com/PJZ9n/LoggerPM/blob/master/LICENSE"]));
        }
    
    }