<?php

namespace PhpFramework\Layout\Bootstrap;

use PhpFramework\Html\Components\Script as ComponentsScript;
use PhpFramework\Html\Components\Stylesheet;
use PhpFramework\Html\Html;
use PhpFramework\Layout\Layout;
use PhpFramework\Layout\Section\Script;
use PhpFramework\Response\HtmlResponse;

class Login extends Layout
{
    public static function Render(HtmlResponse $Context): void
    {
        $Context->Stylesheets->Add(
            new Stylesheet(
                Href: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                Integrity: 'sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN'
            ),
            new Stylesheet(
                Href: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css',
                Integrity: 'sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=='
            )
        );

        $Context->Scripts->Add(
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
<?= new Html(Dom: 'title', Content: $Context->Title ?? '') ?>
<?= $Context->Stylesheets ?>
</head>
<body class="bg-dark">
<div class="row align-items-center g-0 vh-100">
    <div class="col-md-6 col-lg-8 col-xxl-9 d-none d-md-block text-center">
        <i class="fa fa-fw fa-10x fa-cog text-white"></i>
    </div>
    <div class="col p-3 align-self-start">
        <div class="card">
            <div class="card-body">
                <?= $Context->Form?->Open() ?>
                <?= $Context->Body() ?>
                <?= $Context->Form?->Close() ?>
            </div>
        </div>
    </div>
</div>
<div class="fixed-bottom d-md-none text-center pb-4" style="z-index:-1;">
    <i class="fa fa-fw fa-5x fa-cog text-white"></i>
</div>
<?php
        foreach ($Context->Scripts() as $Script) {
            echo $Script;
        }
        ?>
<?= $Context instanceof Script ? $Context->Script() : null ?>
</body>
</html>
<?php
    }
}
