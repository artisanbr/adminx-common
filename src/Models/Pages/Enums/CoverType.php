<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Enums;

enum CoverType: string
{
    case image = 'image';
    case youtube = 'youtube';
    case soundcloud = 'soundcloud';
    case color = 'color';
}
