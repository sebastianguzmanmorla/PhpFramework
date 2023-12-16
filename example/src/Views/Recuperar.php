<?php

namespace Views;

use Controllers\Index;
use PhpFramework\Html\Enums\ButtonType;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\Enums\InputType;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormButton;
use PhpFramework\Html\FormInput;
use PhpFramework\Html\FormLink;
use PhpFramework\Layout\Bootstrap\Login as BootstrapLogin;
use PhpFramework\Layout\Section\Toolbar;
use PhpFramework\Layout\UseLayout;
use PhpFramework\Response\HtmlResponse;

#[Form(AutoComplete: false), UseLayout(BootstrapLogin::class)]
class Recuperar extends HtmlResponse implements Toolbar
{
    public FormInput $usu_mail;

    public FormButton $Recuperar;

    public FormLink $Volver;

    public function Init(): void
    {
        $this->Title = 'Recuperar Contraseña';

        $this->usu_mail = new FormInput(
            Label: 'Correo',
            Id: 'usu_mail',
            Name: 'usu_mail',
            Type: InputType::Email,
            Format: FormInput::Floating
        );

        $this->Recuperar = new FormButton(
            Label: 'Recuperar Contraseña',
            Icon: 'fa fa-sign-in',
            Type: ButtonType::Submit,
            Color: Color::Success
        );

        $this->Volver = new FormLink(
            Label: 'Volver',
            Icon: 'fa fa-arrow-left',
            Color: Color::Secondary,
            Href: fn (Index $x) => $x->Login()
        );
    }

    public function Body(): void
    {
        ?>
<?= $this->usu_mail ?>
<?php
    }

    public function Toolbar(): void
    {
        ?>
<?= $this->Recuperar ?>
<?= $this->Volver ?>
<?php
    }
}
