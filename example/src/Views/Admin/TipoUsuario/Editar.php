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
use PhpFramework\Response\HtmlResponse;

#[Form(Method: FormMethod::POST)]
class Editar extends HtmlResponse implements Toolbar
{
    #[Singleton]
    public \Database\Framework $Database;

    public TipoUsuario $TipoUsuario;

    public FormInput $tus_nombre;

    public FormLink $Volver;

    public FormButton $Guardar;

    public function Init(): void
    {
        $this->tus_nombre = new FormInput(
            Field: $this->Database->Definition->TipoUsuario->tus_nombre,
            Value: fn () => $this->TipoUsuario->tus_nombre ?? ''
        );

        $this->Volver = new FormLink(
            Href: fn (AdminTipoUsuario $x) => $x->Index(),
            Icon: 'fa fa-arrow-left',
            Label: 'Volver',
            Color: Color::Light
        );

        $this->Guardar = new FormButton(
            Label: 'Guardar',
            Icon: 'fa fa-save',
            Color: Color::Success,
            Type: ButtonType::Submit
        );
    }

    public function Toolbar(): void
    {
        ?>
<div class="btn-group">
    <?= $this->Volver ?>
    <?= $this->Guardar ?>
</div>
<?php
    }

    public function Body(): void
    {
        ?>
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center text-dark bg-secondary-subtle">
        <h5 class="m-0">Datos del Tipo Usuario</h5>
        <div class="btn-group btn-group-sm" role="group">
            <span class="btn btn-light">ID</span>
            <span class="btn btn-success"><?= $this->TipoUsuario->id_tipousuario ?></span>
        </div>
    </div>
    <div class="card-body">
        <?= $this->tus_nombre ?>
    </div>
</div>
<?php
    }
}
