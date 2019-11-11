# LoggerPM
Records the actions performed by the player!

プレイヤーが行った動作を記録する！

[![](https://img.shields.io/badge/license-GNU%20General%20Public%20License%20v3.0-yellow)](https://github.com/PJZ9n/LoggerPM/blob/master/LICENSE)

<!--- [![](https://poggit.pmmp.io/ci.badge/PJZ9n/LoggerPM/LoggerPM)](https://poggit.pmmp.io/ci/PJZ9n/LoggerPM) --->
[![](https://poggit.pmmp.io/ci.shield/PJZ9n/LoggerPM/LoggerPM)](https://poggit.pmmp.io/ci/PJZ9n/LoggerPM)

<!--- ## License
Copyright (c) 2019 PJZ9n.

[![](https://www.gnu.org/graphics/gplv3-with-text-136x68.png "GNU General Public License")](https://www.gnu.org/licenses/gpl.html) --->

## Overview
Language:
[日本語](#日本語)
,
[English](#english)

## 日本語
プレイヤーの動作ログを記録するプラグインです。

### 保存できるログの種類
* ブロック系
  * 破壊した時
  * 設置した時
  * タッチした時
* プレイヤー系
  * ログインした時
  * サーバーに入ってきた時
  * サーバーから去った時
  * チャットをした時
  * プレイヤーに攻撃した時
  * プレイヤーから攻撃を受けた時
  * コマンドを実行した時
  
### コマンド
|コマンド|説明|使い方|権限|
|---|---|---|---|
|`/log form`|ログを確認するフォームを開く|`/log form`|op|

### 設定ファイル
[こちらを参照してください](https://github.com/PJZ9n/LoggerPM/blob/master/resources/config.yml "config.yml")

## English
A plug-in that records player action logs.

### Supported log types
* Block
  * Break
  * Place
  * Touch
* Player
  * Login
  * Join
  * Quit
  * Chat
  * Attack
  * Damage
  * Command

### Commands
|Label|Description|Usage|Permission|
|---|---|---|---|
|`/log form`|Open a UI Form.|`/log form`|op|

### Configuration file
[See here](https://github.com/PJZ9n/LoggerPM/blob/master/resources/config.yml "config.yml")