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
    use RuntimeException;
    
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
            $this->initConfig();
            $this->initLanguage();
            $this->initConfigComments();
            $this->logManager = new LogManager($this);
            $this->registerEvents();
            $this->registerCommands();
            $this->sendStartupMessage();
        }
    
        private function initConfig(): void
        {
            $filePath = $this->getDataFolder() . "config.yml";
            if (DebugInfo::CONFIG_FORCE_RENEW && file_exists($filePath)) {
                unlink($filePath);
            }
            if (!file_exists($filePath)) {
                $this->saveResource("config.yml");
                $this->initConfigValues();
                $this->getLogger()->notice("Created config.yml!");
                return;
            }
            $this->reloadConfig();
            $oldVersion = $this->getConfig()->get("version");
            $configResource = null;
            foreach ($this->getResources() as $resource) {
                if ($resource->getFilename() === "config.yml") {
                    $configResource = $resource;
                }//TODO: パスも検証する
            }
            if ($configResource === null) {
                throw new RuntimeException("\"config.yml\" does not exist in resources");
            }
            $get = yaml_parse(file_get_contents($configResource->getRealPath()));
            if (!isset($get["version"])) {
                throw new RuntimeException("\"version\" does not exist in {$configResource->getRealPath()}");
            }
            $newVersion = $get["version"];
            if (!version_compare($oldVersion, $newVersion, "<")) {
                //config.yml is latest
                $this->initConfigValues();
                $this->getLogger()->info("config.yml is latest version ({$oldVersion})!");
                return;
            }
            $data = $this->getConfig()->getAll();
            $this->saveResource("config.yml", true);
            $this->initConfigValues($data);
            $this->getLogger()->notice("Updated config.yml from {$oldVersion} to {$newVersion}!");
        }
    
        private function initConfigValues(array $data = []): void
        {
            $filePath = $this->getDataFolder() . "config.yml";
            $search = [
                "{language}",
                "{cleanup}",
            ];
            $replace = [
                $data["language"] ?? (DebugInfo::CONFIG_FORCE_LANGUAGE ?? $this->getServer()->getLanguage()->getLang()),
                $data["cleanup"] ?? 30,
            ];
            file_put_contents($filePath, str_replace($search, $replace, file_get_contents($filePath)));
            $this->reloadConfig();
        }
    
        private function initConfigComments(): void
        {
            $filePath = $this->getDataFolder() . "config.yml";
            /*$search = [
                "{file-description}",
                "{version-description1}",
                "{version-description2}",
                "{language-description1}",
                "{language-description2}",
                "{language-description3}",
                "{cleanup-description1}",
                "{cleanup-description2}",
                "{cleanup-description3}",
                "{cleanup-description4}",
            ];
            $replace = [
                $this->lang->translateString("file.description"),
                $this->lang->translateString("version.description1"),
                $this->lang->translateString("version.description2"),
                $this->lang->translateString("language.description1"),
                $this->lang->translateString("language.description2"),
                $this->lang->translateString("language.description3"),
                $this->lang->translateString("cleanup.description1"),
                $this->lang->translateString("cleanup.description2"),
                $this->lang->translateString("cleanup.description3"),
                $this->lang->translateString("cleanup.description4"),
            ];*/
            $file = file_get_contents($filePath);
            //preg_match_all('#(?<={).*?(?=})#', $file, $matches);
            preg_match_all('{{(.*)}}', $file, $matches);
            //print_r($matches);
            $search = [];
            foreach ($matches[0] as $match) {
                $search[] = $match;
            }
            //print_r($search);
            $replace = [];
            foreach ($search as $code) {
                $code = str_replace(["-", "{", "}"], [".", "", ""], $code);
                $replace[] = $this->lang->translateString($code);
            }
            file_put_contents($filePath, str_replace($search, $replace, $file));
        }
    
        private function initLanguage(): void
        {
            foreach ($this->getResources() as $resource) {
                $path = explode(DIRECTORY_SEPARATOR, $resource->getPath());
                $last = end($path);
                if ($last !== "locale" || $resource->getExtension() !== "ini") {
                    continue;
                }
                $oldLangFile = $this->getDataFolder() . "locale/" . $resource->getFilename();
                $newLangFile = $last . DIRECTORY_SEPARATOR . $resource->getFilename();
                if (!file_exists($oldLangFile)) {
                    $this->saveResource($newLangFile);
                    continue;
                }
                $old = array_map('\stripcslashes', parse_ini_file($oldLangFile, false, INI_SCANNER_RAW));
                if (!isset($old["version"]) || !isset($old["auto-update"])) {
                    $this->getLogger()->warning("Failed to load language file: {$oldLangFile}");
                    $this->getLogger()->warning("A required parameter is missing.");
                    continue;
                }
                $new = array_map('\stripcslashes', parse_ini_file($resource->getRealPath(), false, INI_SCANNER_RAW));
                if (!isset($new["version"]) || !isset($new["auto-update"])) {
                    $this->getLogger()->warning("Failed to load language file (resources): {$resource->getRealPath()}");
                    $this->getLogger()->warning("A required parameter is missing.");
                    continue;
                }
                $autoUpdate = $old["auto-update"];
                $oldVersion = $old["version"];
                $newVersion = $new["version"];
                if (DebugInfo::LANGUAGE_FORCE_UPDATE || version_compare($oldVersion, $newVersion, "<")) {
                    if (DebugInfo::LANGUAGE_FORCE_UPDATE || $autoUpdate === "yes") {
                        $this->saveResource($newLangFile, true);
                        $this->getLogger()->notice("Updated language file {$oldLangFile} from {$old["version"]} to {$new["version"]}!");
                    }
                }
            }
            $this->lang = new BaseLang((string)$this->getConfig()->get("language"), $this->getDataFolder() . "locale/");
        }
    
        private function registerEvents(): void
        {
            /** @var $listeners Listener[] */
            $listeners = [
                //
            ];
            foreach ($listeners as $listener) {
                $this->getServer()->getPluginManager()->registerEvents($listener, $this);
            }
        }
    
        private function registerCommands(): void
        {
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