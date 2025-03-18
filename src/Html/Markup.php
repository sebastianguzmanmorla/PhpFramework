<?php

namespace PhpFramework\Html;

use BackedEnum;
use Closure;
use DateTime;
use DOMDocument;
use DOMElement;
use JsonSerializable;
use PhpFramework\Html\Enums\ButtonType;
use PhpFramework\Html\Enums\InputType;
use PhpFramework\Url;
use ReflectionClass;

class Markup implements JsonSerializable
{
    public ?DOMDocument $DOMDocument = null;

    public function __construct(
        //Framework attributes
        public string $Dom = 'div',
        public ?string $Icon = null,
        public Markup|Closure|array|string|null $Content = null,

        //HTML attributes
        #[MarkupAttribute('id')]
        public ?string $Id = null,
        #[MarkupAttribute('name')]
        public ?string $Name = null,
        #[MarkupAttribute('for')]
        public ?string $For = null,
        #[MarkupAttribute('type')]
        public ButtonType|InputType|string|null $Type = null,
        #[MarkupAttribute('multiple')]
        public Closure|bool|null $Multiple = null,
        #[MarkupAttribute('value')]
        public mixed $Value = null,
        #[MarkupAttribute('max')]
        public mixed $Max = null,
        #[MarkupAttribute('maxlength')]
        public mixed $MaxLength = null,
        #[MarkupAttribute('placeholder')]
        public ?string $PlaceHolder = null,
        #[MarkupAttribute('disabled')]
        public Closure|bool|null $Disabled = null,
        #[MarkupAttribute('readonly')]
        public Closure|bool|null $ReadOnly = null,
        #[MarkupAttribute('selected')]
        public ?bool $Selected = null,
        #[MarkupAttribute('class')]
        public Closure|string|null $Class = null,
        #[MarkupAttribute('style')]
        public ?string $Style = null,
        #[MarkupAttribute('title')]
        public ?string $Title = null,
        #[MarkupAttribute('href')]
        public Url|Closure|string|null $Href = null,
        #[MarkupAttribute('target')]
        public Url|Closure|string|null $Target = null,
        #[MarkupAttribute('src')]
        public Url|Closure|string|null $Src = null,
        #[MarkupAttribute('rel')]
        public ?string $Rel = null,
        #[MarkupAttribute('onclick')]
        public Closure|string|null $OnClick = null,

        //Bootstrap attributes
        #[MarkupAttribute('role')]
        public ?string $Role = null,
        #[MarkupAttribute('data-bs-dismiss')]
        public ?string $DataBsDismiss = null,
        #[MarkupAttribute('data-bs-toggle')]
        public ?string $DataBsToggle = null,
        #[MarkupAttribute('data-bs-target')]
        public ?string $DataBsTarget = null,
        #[MarkupAttribute('data-bs-placement')]
        public ?string $DataBsPlacement = null,
        #[MarkupAttribute('aria-current')]
        public ?string $AriaCurrent = null,
        #[MarkupAttribute('aria-controls')]
        public ?string $AriaControls = null,
        #[MarkupAttribute('aria-expanded')]
        public ?string $AriaExpanded = null,
        #[MarkupAttribute('aria-described-by')]
        public ?string $AriaDescribedBy = null,
        #[MarkupAttribute('aria-label')]
        public ?string $AriaLabel = null
    ) {
    }

    public function __toString()
    {
        if ($this->DOMDocument === null) {
            $this->DOMDocument = new DOMDocument();
        }

        $Element = $this->Generate();

        $Fragment = $this->DOMDocument->createDocumentFragment();

        $Fragment->appendChild($Element);

        return $this->DOMDocument->saveHTML($Fragment);
    }

    public function Generate(): DOMElement
    {
        $DOMElement = $this->DOMDocument->createElement($this->Type === InputType::Textarea ? 'textarea' : $this->Dom);

        if ($this->Type === InputType::Textarea) {
            $this->Type = null;
            $this->Content = $this->Value;
            $this->Value = null;
        }

        if ($this->Icon !== null) {
            $IconNode = $this->DOMDocument->createElement('i');
            $IconNode->setAttribute('class', $this->Icon . ($this->Content !== null ? ' me-1' : ''));
            $DOMElement->appendChild($IconNode);
        }

        if ($this->Content !== null) {
            $Contents = is_array($this->Content) ? $this->Content : [$this->Content];
            foreach ($Contents as $Content) {
                if ($Content instanceof Closure) {
                    $Content = $Content->__invoke();
                }
                if ($Content instanceof self) {
                    $Content->DOMDocument = &$this->DOMDocument;

                    $Content = $Content->Generate();

                    $DOMElement->appendChild($Content);
                }
                if ($Content instanceof BackedEnum) {
                    $Content = $Content->value;
                }
                if ($Content instanceof Url) {
                    $Content = $Content->Url;
                }
                if (is_string($Content)) {
                    $Content = $this->DOMDocument->createTextNode($Content);
                    $DOMElement->appendChild($Content);
                }
            }
        }

        $Reflection = new ReflectionClass($this);

        foreach ($Reflection->getProperties() as $Property) {
            $MarkupAttribute = $Property->getAttributes(MarkupAttribute::class);
            $MarkupAttribute = !empty($MarkupAttribute) ? $MarkupAttribute[0]->newInstance() : null;

            if ($MarkupAttribute !== null) {
                $Value = $Property->getValue($this);

                if ($Value instanceof Closure && in_array($MarkupAttribute->name, ['href', 'src'])) {
                    $Value = new Url($Value);
                }

                if ($Value instanceof Closure) {
                    $Value = $Value->__invoke();
                }

                if ($Value instanceof BackedEnum) {
                    $Value = $Value->value;
                }

                if ($Value instanceof Url) {
                    $Value = $Value->Url;
                }

                if ($Value instanceof DateTime && $this->Type == InputType::Date) {
                    $Value = $Value->format('Y-m-d');
                }

                if ($Value instanceof DateTime && $this->Type == InputType::DateTime) {
                    $Value = $Value->format('Y-m-d H:i');
                }

                if ($Value !== null && $Value !== false) {
                    $DOMElement->setAttribute($MarkupAttribute->name, $Value);
                }
            }
        }

        return $DOMElement;
    }

    public function jsonSerialize(): mixed
    {
        return $this->__toString();
    }
}
