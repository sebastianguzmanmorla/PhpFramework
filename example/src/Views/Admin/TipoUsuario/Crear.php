<?php

namespace Views\Admin\TipoUsuario;

use Controllers\Admin\TipoUsuario as AdminTipoUsuario;
use Database\Framework\TipoUsuario;
use PhpFramework\Attributes\Singleton;
use PhpFramework\Html\Enums\ButtonType;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\Enums\FormMethod;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormButton;
use PhpFramework\Html\FormInput;
use PhpFramework\Html\FormLink;
use PhpFramework\Layout\Section\Toolbar;
use PhpFramework\Response\Html\ViewResponse;

#[Form(Method: FormMethod::POST)]
class Crear extends ViewResponse implements Toolbar
{
    #[Singleton]
    public \Database\Framework $Database;

    public TipoUsuario $TipoUsuario;

    public FormInput $tus_nombre;

    public FormLink $Volver;

    public FormButton $Crear;

    public function Initialize(): void
    {
        $this->tus_nombre = new FormInput(
            Field: $this->Database->Schema->TipoUsuario->tus_nombre,
            Value: fn () => $this->TipoUsuario->tus_nombre ?? ''
        );

        $this->Volver = new FormLink(
            Href: fn (AdminTipoUsuario $x) => $x->Index(),
            Icon: 'fa fa-arrow-left',
            Label: 'Volver',
            Color: Color::Light
        );

        $this->Crear = new FormButton(
            Label: 'Crear',
            Icon: 'fa fa-plus',
            Color: Color::Primary,
            Type: ButtonType::Submit
        );
    }

    public function Toolbar(): void
    {
        ?>
<div class="btn-group">
    <?= $this->Volver ?>
    <?= $this->Crear ?>
</div>
<?php
    }

    public function Body(): void
    {
        ?>
<div class="card shadow">
    <div class="card-body">
        <?= $this->tus_nombre ?>
    </div>
</div>
<?php
    }
}
