<?php

declare(strict_types=1);

namespace Apm\Widget\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

final class Config extends AbstractHelper
{
    public const CONFIG_WIDGET_CONTACTS_WHATS_APP_PATH = 'apm_widget/contacts/whats_app';
    public const CONFIG_WIDGET_CONTACTS_TELEGRAM_PATH = 'apm_widget/contacts/telegram';
    public const CONFIG_WIDGET_CONTACTS_VIBER_PATH = 'apm_widget/contacts/viber';

    public function getWhatsAppContact(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_WIDGET_CONTACTS_WHATS_APP_PATH, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getTelegramContact(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_WIDGET_CONTACTS_TELEGRAM_PATH, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getViberContact(): string
    {
        return (string)$this->scopeConfig->getValue(self::CONFIG_WIDGET_CONTACTS_VIBER_PATH, ScopeInterface::SCOPE_WEBSITE);
    }
}
