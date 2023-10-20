<?php

declare(strict_types=1);

namespace Apm\Widget\ViewModel;

use Apm\Widget\Helper\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class WidgetContactViewModel extends Template implements ArgumentInterface
{
    public function __construct(
        Template\Context $context,
        private Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getWhatsAppContact(): string
    {
        return $this->config->getWhatsAppContact();
    }

    public function getTelegramContact(): string
    {
        return $this->config->getTelegramContact();
    }

    public function getViberContact(): string
    {
        return $this->config->getViberContact();
    }
}
