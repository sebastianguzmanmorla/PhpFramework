<?php

namespace PhpFramework\Html;

use Closure;
use PhpFramework\Html\Enums\Color;
use PhpFramework\Html\Enums\EncType;
use PhpFramework\Html\Enums\FormMethod;
use PhpFramework\Html\Enums\InputType;
use PhpFramework\Html\Enums\ModalDialog;
use PhpFramework\Layout\Section\Script;
use PhpFramework\Layout\Section\Toolbar;
use PhpFramework\Url;

class FormModal extends Form
{
    public function __construct(
        string $Id,
        Url|Closure|string|null $Action = null,
        FormMethod $Method = FormMethod::POST,
        EncType $EncType = EncType::Default,
        ?bool $AutoComplete = null,
        ?bool $NoValidate = null,
        public ModalDialog $ModalDialog = ModalDialog::Default,
        public string|Markup|null $ModalTitle = '',
        public string|Markup|null $ModalBody = null,
        public string|Markup|false|null $ModalFooter = null,
        public ?HtmlResponse $ModalView = null,
        public array $Values = [],
        public ?Closure $Init = null
    ) {
        parent::__construct(
            Id: $Id,
            Class: 'modal fade',
            Action: $Action,
            Method: $Method,
            EncType: $EncType,
            AutoComplete: $AutoComplete,
            NoValidate: $NoValidate,
            TabIndex: -1,
            Role: 'dialog',
            AriaHidden: true
        );
    }

    public function ModalLink(
        Url|Closure|string|null $Action = null,
        ?string $Id = null,
        ?string $Class = null,
        ?Color $Color = null,
        ?string $Style = null,
        ?string $Icon = null,
        ?string $Title = null,
        ?string $Label = null,
        array $Values = []
    ): FormModalLink {
        return new FormModalLink(
            Action: $Action ?? $this->Action,
            Modal: $this->Id,
            Id: $Id,
            Class: $Class,
            Color: $Color,
            Style: $Style,
            Icon: $Icon,
            Title: $Title,
            Label: $Label,
            Values: $Values
        );
    }

    public static function Script(): void
    {
        ?>
<script>
    $(function() {
        jQuery.fn.extend({
            loadModal: function(get){
                $(this).trigger('loadModal', get);
            }
        });
    });
</script>
<?php
    }

    public function LoadModal(): void
    {
        $this->Init?->__invoke($this->ModalView);

        if ($this->ModalView !== null && $this->ModalView instanceof Script) {
            echo $this->ModalView->Script();
        }
        ?>
<script>
$(function() {
    $('#<?= $this->Id ?>').on('loadModal', function(event, get){
<?php
                /*
                if($modal['url']!==false)
                {
        ?>
                $.get('<?= $modal['url'] ?>', get, function(data)
                    {

                        $('#<?= $this->Id ?> .modal-body').html(data);
                        $.each(get,function(key, value){
                            if(typeof value === 'object'){
                                $.each(value, function(key2, value2){
                                    if(key2=='text'){
                                        $('#<?= $this->Id ?> #'+key).text(value2);
                                    }else{
                                        $('#<?= $this->Id ?> #'+key).attr(key2, value2);
                                    }
                                });
                            }else{
                                $('#<?= $this->Id ?> #'+key).val(value);
                            }
                        });
                        $('#<?= $this->Id ?>').modal('show');
                        $('#<?= $this->Id ?>').attr('route', '<?= $modal['url'] ?>&'+$.param(get));
                    }
                );
        <?php
                }
                else
                {
                */
        ?>
        $.each(get,function(key, value){
            if(key=='action'){
                $('form#<?= $this->Id ?>').attr('action', value);
            }else if(typeof value === 'object'){
                $.each(value, function(key2, value2){
                    if(key2=='text'){
                        $('#<?= $this->Id ?> #'+key).text(value2);
                    }else{
                        $('#<?= $this->Id ?> #'+key).attr(key2, value2);
                    }
                });
            }else{
                $('#<?= $this->Id ?> #'+key).val(value);
                $('#<?= $this->Id ?> #'+key).change();
            }
        });
        $('#<?= $this->Id ?>').modal('show');
<?php
                //}
        ?>
    });
});
</script>
<?php
    }

    public function PrintModal(): void
    {
        echo $this->Open();
        foreach ($this->Values as $Id => $Value) {
            echo new Markup(Dom: 'input', Id: $Id, Name: $Id, Type: InputType::Hidden, Value: $Value);
        }
        ?>
        <div class="modal-dialog <?= $this->ModalDialog->value ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $this->ModalTitle ?></h5>
                    <?= new FormLink(Class: 'close', Href: '#', DataBsDismiss: 'modal', AriaLabel: 'Close', Icon: 'fa fa-close fa-lg') ?>
                </div>
                <div class="modal-body">
<?= $this->ModalView?->Body() ?? $this->ModalBody ?? '';
        ?>
                </div>
                <div class="modal-footer">
<?php
                        if ($this->ModalView !== null && $this->ModalView instanceof Toolbar) {
                            echo $this->ModalView->Toolbar();
                        } elseif ($this->ModalFooter !== null) {
                            echo $this->ModalFooter !== false ? $this->ModalFooter : '';
                        } else {
                            ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Aceptar</button>
                    </div>
<?php
                        }
        ?>
                </div>
            </div>
        </div>
<?php
                echo $this->Close();
    }
}
