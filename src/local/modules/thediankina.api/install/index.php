<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class thediankina_api extends CModule
{
    public $MODULE_ID = 'thediankina.api';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        include __DIR__ . '/version.php';

        if (isset($arModuleVersion['VERSION'], $arModuleVersion['VERSION_DATE']))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('THEDIANKINA_API_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('THEDIANKINA_API_MODULE_DESCRIPTION');
    }

    /**
     * @return void
     */
    public function DoInstall(): void
    {
        if (!CurrentUser::get()->isAdmin()) {
            return;
        }

        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * @return void
     */
    public function DoUninstall(): void
    {
        if (!CurrentUser::get()->isAdmin()) {
            return;
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
