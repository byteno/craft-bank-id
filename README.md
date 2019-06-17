# BankID plugin for Craft CMS 3.x

BankID

![Screenshot](src/icon.svg)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require /bank-id

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for BankID.

## BankID Overview

Easy implementation of BankID for login and registration for non-admin users. Uses Craft's owner user system.

## Configuring BankID

Follow the tutorial found in the side menu bar after installation.

## Using BankID

All you need is a button/link pointing to "/bank-id/bank-id-login" for login. You can use {{ craft.bankID.loginButton }} in Twig for easy implementation.

BankID Plugin is based on the Craft login system and login check or logout are done with Craft's own functions.

The plugin requires credentials recieved from BankID. Follow the tutorial from the admin panel for a step by step guide.

Brought to you by [Byte AS](https://byte.no)
