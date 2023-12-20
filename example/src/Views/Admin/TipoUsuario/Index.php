<?php

namespace Views\Admin\TipoUsuario;

use Controllers\Admin\TipoUsuario;
use Model\Layout\HtmlResponse;
use PhpFramework\Html\Enums\ButtonType;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\Enums\FormMethod;
use PhpFramework\Html\Enums\InputType;
use PhpFramework\Html\Enums\ModalDialog;
use PhpFramework\Html\Form;
use PhpFramework\Html\FormButton;
use PhpFramework\Html\FormInput;
use PhpFramework\Html\FormLink;
use PhpFramework\Html\FormModal;
use PhpFramework\Layout\Section\Filters;
use PhpFramework\Layout\Section\Script;
use PhpFramework\Layout\Section\Toolbar;

#[Form(AutoComplete: false, Method: FormMethod::GET, Id: 'ListadoTipoUsuario')]
class Index extends HtmlResponse implements Filters, Script, Toolbar
{
    public FormInput $id_tipousuario;

    public FormInput $tus_nombre;

    public FormLink $Limpiar;

    public FormButton $Buscar;

    public FormModal $Borrar;

    public bool $FiltersOpen = false;

    public function FiltersOpen(): bool
    {
        return $this->FiltersOpen;
    }

    public function Init(): void
    {
        $this->id_tipousuario = new FormInput(
            Label: 'ID',
            Id: 'id_tipousuario',
            Name: 'id_tipousuario',
            Type: InputType::Number
        );

        $this->tus_nombre = new FormInput(
            Label: 'Nombre',
            Id: 'tus_nombre',
            Name: 'tus_nombre'
        );

        $this->Limpiar = new FormLink(
            Href: fn (TipoUsuario $x) => $x->Index(),
            Label: 'Limpiar',
            Icon: 'fa fa-refresh',
            Color: Color::Light
        );

        $this->Buscar = new FormButton(
            Label: 'Buscar',
            Icon: 'fa fa-search',
            Color: Color::Primary,
            Type: ButtonType::Submit
        );

        $this->Borrar = new FormModal(
            Id: 'BorrarTipoUsuario',
            ModalTitle: 'Borrar Tipo de Usuario',
            ModalDialog: ModalDialog::Small,
            ModalBody: '¿Está seguro que desea borrar el Tipo de Usuario?'
        );
    }

    public function Filters(): void
    {
        ?>
<div class="col">
    <?= $this->id_tipousuario ?>
</div>
<div class="col">
    <?= $this->tus_nombre ?>
</div>
<?php
    }

    public function Toolbar(): void
    {
        ?>
<div class="btn-group">
    <?= $this->Limpiar ?>
    <?= $this->Buscar ?>
</div>
<?php
    }

    public function Body(): void
    {
        ?>
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table id="ListadoTipoUsuarioDataTable" class="table table-rounded table-striped table-hover border gy-7 gs-7">
                <thead>
                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                        <th>ID</th>
                        <th>Nombre</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php
    }

    public function Script(): void
    {
        ?>
<script>
    const form = document.getElementById('ListadoTipoUsuario');
    form.addEventListener('keypress', function(e) {
    if (e.keyCode === 13) {
        $('#ListadoTipoUsuarioDataTable').DataTable().ajax.reload();
        e.preventDefault();
    }
    });

    $(function(){
        $('#ListadoTipoUsuarioDataTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[ 1, "asc" ]],
            columns: [
                { data: 'id_tipousuario', width: '10%' },
                { data: 'tus_nombre' },
                { data: 'Acciones', orderable: false, searchable: false, width: '5%' }
            ],
            ajax: {
                url: '?route=Admin/TipoUsuario/Listado',
                type: 'POST',
                data: function (params) {
                    params.id_tipousuario = $('#id_tipousuario').val();
                    params.tus_nombre = $('#tus_nombre').val();
                    return params;
                }
            }
        });

        $('#id_tipousuario').change(function () {
            $('#ListadoTipoUsuarioDataTable').DataTable().ajax.reload();
        });
        $('#tus_nombre').change(function () {
            $('#ListadoTipoUsuarioDataTable').DataTable().ajax.reload();
        });
    });
</script>
<?php
    }
}
