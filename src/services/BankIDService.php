<?php
/**
 * BankID plugin for Craft CMS 3.x
 *
 * BankID
 *
 * @link      https://byte.no
 * @copyright Copyright (c) 2019 Byte AS
 */

namespace byteas\bankid\services;

use Craft;
use craft\base\Component;

/**
 * BankIDService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Byte AS
 * @package   BankID
 * @since     1.0.0
 */
class BankIDService extends Component
{

    public function getLoginButton()
    {
        $view    = Craft::$app->getView();
        $oldMode = $view->getTemplateMode();
        $view->setTemplateMode($view::TEMPLATE_MODE_CP);

        $html = $view->renderTemplate('/bank-id/_components/loginButton', [
            'test'=>'Test'
        ]);
        $view->setTemplateMode($oldMode);
        return $html;
    }

}
