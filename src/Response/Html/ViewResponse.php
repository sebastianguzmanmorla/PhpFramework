<?php

namespace PhpFramework\Response\Html;

use Closure;
use Generator;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Html\Components\Alerts;
use PhpFramework\Html\Components\Scripts;
use PhpFramework\Html\Components\Stylesheets;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormModal;
use PhpFramework\Html\Markup;
use PhpFramework\Html\Validation\IValidation;
use PhpFramework\Layout\UseLayout;
use PhpFramework\Response\Enum\StatusCode;
use PhpFramework\Response\Interface\IResponse;
use ReflectionClass;
use ReflectionNamedType;

abstract class ViewResponse implements IResponse
{
    public Response $Response;

    public ?string $Title;

    public ?string $Icon;

    public Stylesheets $Stylesheets;

    public Scripts $Scripts;

    public Alerts $Alerts;

    public ?Form $Form = null;

    public StatusCode $StatusCode = StatusCode::Ok;

    public function __construct()
    {
        $this->Response = Response::Instance();

        $this->Title = &$this->Response->Title;
        $this->Icon = &$this->Response->Icon;

        $this->Stylesheets = &$this->Response->Stylesheets;
        $this->Scripts = &$this->Response->Scripts;
        $this->Alerts = &$this->Response->Alerts;

        $ReflectionClass = new ReflectionClass(static::class);

        $Form = $ReflectionClass->getAttributes(Form::class);
        $this->Form = isset($Form[0]) ? $Form[0]->newInstance() : null;

        $UseLayout = $ReflectionClass->getAttributes(UseLayout::class);

        if (!empty($UseLayout)) {
            Response::Instance()->Layout = $UseLayout[0]->newInstance()->Layout;
        }

        foreach ($ReflectionClass->getProperties() as $Property) {
            $Singleton = $Property->getAttributes(Singleton::class);
            if (!empty($Singleton)) {
                $PropertyType = $Property->getType();
                $PropertyValue = Singleton::Get($PropertyType->getName());
                $Property->setValue($this, $PropertyValue);
            }

            $Attributes = $Property->getAttributes();
            foreach ($Attributes as $Attribute) {
                $Value = $Attribute->newInstance();
                if ($Value instanceof Markup) {
                    $Property->setValue($this, $Value);
                }
            }
        }

        $this->Initialize();
    }

    abstract public function Initialize(): void;

    abstract public function Body(): void;

    final public function Response(?StatusCode $StatusCode = null): ?string
    {
        http_response_code($StatusCode?->value ?? $this->StatusCode->value);

        header('Content-Type: text/html; charset=utf-8');

        ob_start();
        Response::Instance()->Render($this);
        $Body = ob_get_contents();
        ob_end_clean();

        return $Body;
    }

    final public function Scripts(): Generator
    {
        yield $this->Response->Scripts;
        $Reflection = new ReflectionClass($this);
        foreach ($Reflection->getProperties() as $Property) {
            $PropertyType = $Property->getType();
            if ($PropertyType !== null && $PropertyType instanceof ReflectionNamedType && $PropertyType->getName() == FormModal::class) {
                yield $Property->getValue($this)->LoadModal();
                yield $Property->getValue($this)->PrintModal();
            }
        }
    }

    final public function Validate(mixed $Context = null): bool
    {
        $Valid = true;

        $Reflection = new ReflectionClass($this);

        foreach ($Reflection->getProperties() as $Property) {
            if ($Property->isInitialized($this)) {
                $Value = $Property->getValue($this);

                if ($Value instanceof Markup && $Value->Disabled !== null) {
                    $Disabled = $Value->Disabled;

                    if ($Disabled instanceof Closure) {
                        $Disabled = $Disabled->__invoke();
                    }

                    if ($Disabled) {
                        continue;
                    }
                }

                if ($Value !== null && $Value instanceof IValidation) {
                    if (!$Value->Validation()->Validate($Context)) {
                        $Valid = false;
                    }
                }
            }
        }

        return $Valid;
    }
}
