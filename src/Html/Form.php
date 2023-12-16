<?php

namespace PhpFramework\Html;

use Attribute;
use Closure;
use PhpFramework\Html\Enums\EncType;
use PhpFramework\Html\Enums\FormMethod;
use PhpFramework\Html\Enums\Target;
use PhpFramework\Router;
use PhpFramework\Url;

#[Attribute(Attribute::TARGET_CLASS)]
class Form
{
    public function __construct(
        public ?string $Id = null,
        public Url|Closure|string|null $Action = null,
        public ?string $Class = null,
        public FormMethod $Method = FormMethod::POST,
        public ?Target $Target = null,
        public EncType $EncType = EncType::Default,
        public ?bool $AutoComplete = null,
        public ?bool $NoValidate = null,
        public ?int $TabIndex = null,
        public ?string $Role = null,
        public ?bool $AriaHidden = null
    ) {
    }

    public function Open(): string
    {
        $Route = null;

        if ($this->Action instanceof Closure) {
            $this->Action = new Url($this->Action);
        }
        if ($this->Action instanceof Url) {
            $this->Action = $this->Action->Url;
        }

        if ($this->Action === null && $this->Method == FormMethod::GET) {
            $Route = $_GET[Router::Route] ?? null;
        }

        return '<form'
            . ($this->Id ? ' id="' . $this->Id . '"' : '')
            . ($this->Id ? ' name="' . $this->Id . '"' : '')
            . ($this->Class ? ' class="' . $this->Class . '"' : '')
            . ($this->Action ? ' action="' . $this->Action . '"' : '')
            . ' method="' . $this->Method->value . '"'
            . ($this->Target ? ' target="' . $this->Target->value . '"' : '')
            . ' enctype="' . $this->EncType->value . '"'
            . ($this->AutoComplete ? ' autocomplete="on"' : ' autocomplete="off"' ?? '')
            . ($this->NoValidate ? ' novalidate' : '' ?? '')
            . ($this->TabIndex ? ' tabindex="' . $this->TabIndex . '"' : '')
            . ($this->Role ? ' role="' . $this->Role . '"' : '')
            . ($this->AriaHidden ? ' aria-hidden="' . ($this->AriaHidden ? 'true' : 'false') . '"' : '')
            . '>'
            . ($Route !== null ? '<input type="hidden" name="route" value="' . $Route . '">' : '');
    }

    public function Close(): string
    {
        return '</form>';
    }
}
