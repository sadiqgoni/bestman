<?php

namespace App\Filament\Staff\Widgets;

use Filament\Widgets\Widget;

class StaffWorkspace extends Widget
{
    protected string $view = 'filament.staff.widgets.staff-workspace';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;
}
