<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Interfaces;

/**
 * @property string $built_html
 * @method builtHtml()
 */
interface BuildableModel
{
    public function getBuildViewPath($append = null): string;
    public function getBuildViewData(array $merge_data = []): array;
}
