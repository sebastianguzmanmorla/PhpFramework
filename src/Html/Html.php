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

class Html implements JsonSerializable
{
    public ?DOMDocument $DOMDocument = null;

    public function __construct(
        //Framework attributes
        public string $Dom = 'div',
        public ?string $Icon = null,
        public Html|Closure|array|string|null $Content = null,

        //HTML attributes
        #[HtmlAttribute('id')]
        public ?string $Id = null,
        #[HtmlAttribute('name')]
        public ?string $Name = null,
        #[HtmlAttribute('for')]
        public ?string $For = null,
        #[HtmlAttribute('type')]
        public ButtonType|InputType|null $Type = null,
        #[HtmlAttribute('value')]
        public mixed $Value = null,
        #[HtmlAttribute('max')]
        public mixed $Max = null,
        #[HtmlAttribute('maxlength')]
        public mixed $MaxLength = null,
        #[HtmlAttribute('placeholder')]
        public ?string $PlaceHolder = null,
        #[HtmlAttribute('disabled')]
        public Closure|bool|null $Disabled = null,
        #[HtmlAttribute('readonly')]
        public Closure|bool|null $ReadOnly = null,
        #[HtmlAttribute('selected')]
        public ?bool $Selected = null,
        #[HtmlAttribute('class')]
        public Closure|string|null $Class = null,
        #[HtmlAttribute('style')]
        public ?string $Style = null,
        #[HtmlAttribute('title')]
        public ?string $Title = null,
        #[HtmlAttribute('href')]
        public Url|Closure|string|null $Href = null,
        #[HtmlAttribute('target')]
        public Url|Closure|string|null $Target = null,
        #[HtmlAttribute('src')]
        public Url|Closure|string|null $Src = null,
        #[HtmlAttribute('rel')]
        public ?string $Rel = null,
        #[HtmlAttribute('onclick')]
        public Closure|string|null $OnClick = null,

        //Bootstrap attributes
        #[HtmlAttribute('role')]
        public ?string $Role = null,
        #[HtmlAttribute('data-bs-dismiss')]
        public ?string $DataBsDismiss = null,
        #[HtmlAttribute('data-bs-toggle')]
        public ?string $DataBsToggle = null,
        #[HtmlAttribute('data-bs-target')]
        public ?string $DataBsTarget = null,
        #[HtmlAttribute('data-bs-placement')]
        public ?string $DataBsPlacement = null,
        #[HtmlAttribute('aria-current')]
        public ?string $AriaCurrent = null,
        #[HtmlAttribute('aria-expanded')]
        public ?string $AriaExpanded = null,
        #[HtmlAttribute('aria-described-by')]
        public ?string $AriaDescribedBy = null,
        #[HtmlAttribute('aria-label')]
        public ?string $AriaLabel = null
    ) {
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
            $HtmlAttribute = $Property->getAttributes(HtmlAttribute::class);
            $HtmlAttribute = !empty($HtmlAttribute) ? $HtmlAttribute[0]->newInstance() : null;

            if ($HtmlAttribute !== null) {
                $Value = $Property->getValue($this);

                if ($Value instanceof Closure && $HtmlAttribute->name === 'href') {
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

                if ($Value instanceof DateTime) {
                    $Value = $Value->format('Y-m-d');
                }

                if ($Value !== null && $Value !== false) {
                    $DOMElement->setAttribute($HtmlAttribute->name, $Value);
                }
            }
        }

        return $DOMElement;
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

    public function jsonSerialize(): mixed
    {
        return $this->__toString();
    }
}
