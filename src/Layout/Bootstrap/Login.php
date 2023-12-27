<?php

namespace PhpFramework\Layout\Bootstrap;

use PhpFramework\Html\Components\Script as ComponentsScript;
use PhpFramework\Html\Components\Stylesheet;
use PhpFramework\Html\Markup;
use PhpFramework\Layout\ILayout;
use PhpFramework\Layout\Section\Script;
use PhpFramework\Layout\Section\Toolbar;
use PhpFramework\Response\Html\ViewResponse;

class Login implements ILayout
{
    public function Render(ViewResponse $ViewResponse): void
    {
        $ViewResponse->Stylesheets->Add(
            new Stylesheet(
                Href: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                Integrity: 'sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN'
            ),
            new Stylesheet(
                Href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css',
                Integrity: 'sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=='
            )
        );

        $ViewResponse->Scripts->Add(
            new ComponentsScript(
                Src: 'https://code.jquery.com/jquery-3.7.1.min.js',
                Integrity: 'sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo='
            ),
            new ComponentsScript(
                Src: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
                Integrity: 'sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL'
            ),
            new ComponentsScript(
                Src: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js',
                Integrity: 'sha512-uKQ39gEGiyUJl4AI6L+ekBdGKpGw4xJ55+xyJG7YFlJokPNYegn9KwQ3P8A7aFQAUtUsAQHep+d/lrGqrbPIDQ=='
            )
        );
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= new Markup(Dom: 'title', Content: $ViewResponse->Response->Project . ' - ' . $ViewResponse->Response->Title) ?>
<?= $ViewResponse->Stylesheets ?>
</head>
<body class="bg-dark">
<div class="row align-items-center g-0 vh-100">
    <div class="col-md-6 col-lg-8 col-xxl-9 d-none d-md-block text-center">
        <i class="fa fa-fw fa-10x fa-cog text-white"></i>
    </div>
    <div class="col p-3 align-self-start">
        <?= $ViewResponse->Form?->Open() ?>
        <div class="card">
            <div class="card-header text-bg-primary">
                <h5 class="card-title my-0 text-center"><?= $ViewResponse->Response->Title ?></h5>
            </div>
            <div class="card-body">
                <?= $ViewResponse->Alerts ?>
                <?= $ViewResponse->Body() ?>
            </div>
<?php
            if ($ViewResponse instanceof Toolbar) {
                ?>
            <div class="card-footer p-0">
                <div class="btn-group-vertical d-flex" role="group" aria-label="Login Actions">
                    <?= $ViewResponse->Toolbar() ?>
                </div>
            </div>
<?php
            }
        ?>
        </div>
        <?= $ViewResponse->Form?->Close() ?>
    </div>
</div>
<div class="fixed-bottom d-md-none text-center pb-4" style="z-index:-1;">
    <i class="fa fa-fw fa-5x fa-cog text-white"></i>
</div>
<?php
                foreach ($ViewResponse->Scripts() as $Script) {
                    echo $Script;
                }
        ?>
<?= $ViewResponse instanceof Script ? $ViewResponse->Script() : null ?>
</body>
</html>
<?php
    }
}
