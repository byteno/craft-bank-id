<?php
/**
 * BankID plugin for Craft CMS 3.x
 *
 * BankID
 *
 * @link      https://byte.no
 * @copyright Copyright (c) 2019 Byte AS
 */

namespace byteas\bankid;

use byteas\bankid\services\OicdService;
use byteas\bankid\services\SessionService;
use byteas\bankid\services\SettingsService;
use byteas\bankid\services\SetupService;
use byteas\bankid\services\UserService;
use byteas\bankid\variables\BankIDVariable;

use Craft;
use craft\base\Plugin;
use craft\events\UserEvent;
use craft\services\Users;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

class BankID extends Plugin
{

    public static $plugin;

    public $hasCpSection = true;
    public $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->_setComponents();
        $this->_registerUserEvents();
        $this->_registerCPEvent();
        $this->_registerFrontEndEvent();
        $this->_registerVariableEvent();

        //Craft::$app->view->registerTwigExtension(new BankIDTwigExtension());

        Craft::info(
            Craft::t(
                'bank-id',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    private function _registerUserEvents()
    {
        // Event before verify email/set password
        Event::on(Users::class, Users::EVENT_BEFORE_VERIFY_EMAIL, function(UserEvent $event){
            if($event->user->admin === "0"){
                $result = self::getInstance()->user->handleBeforeVerifyEmail($event);
                if($result["error"] === true){
                    echo $result["message"];
                    exit;
                }
            }
        });
    }

    private function _registerCPEvent()
    {
        // Admin CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['bank-id'] = 'bank-id/settings/index';
                $event->rules['bank-id/settings'] = 'bank-id/settings/index';
                $event->rules['bank-id/setup'] = 'bank-id/setup/index';
                $event->rules['bank-id/help'] = 'bank-id/help/index';
            }
        );
    }

    private function _registerFrontEndEvent()
    {
        // Front-end routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['bank-id/callback'] = "bank-id/oicd/callback";
                $event->rules['bank-id/bank-id-login'] = "bank-id/oicd/bank-id-login";
                $event->rules['bank-id/user/registration'] = "bank-id/user/registration";
            }
        );
    }

    private function _registerVariableEvent()
    {
        // Variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('bankID', BankIDVariable::class);
            }
        );
    }

    private function _setComponents()
    {
        $this->setComponents([
            'settings'=>SettingsService::class,
            'setup'=>SetupService::class,
            'oicd'=>OicdService::class,
            'user'=>UserService::class,
            'session'=>SessionService::class
        ]);
    }

    protected function settingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('bank-id/index');
    }

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['badgeCount'] = 3;
        $item['subnav'] = [
            'settings' => ['label' => 'Settings', 'url' => 'bank-id/settings'],
            'setup' => ['label' => 'Setup', 'url' => 'bank-id/setup'],
            'help' => ['label' => 'Help', 'url' => 'bank-id/help'],
        ];
        return $item;
    }

}
