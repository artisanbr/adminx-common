<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine;


use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Sites\Site;

class FrontendRouteTools extends FrontendEngineBase
{
    public function __construct(
        protected ?Page $currentPage = null,
        protected ?Site $currentSite = null,
    )
    {
        if (!$this->currentSite) {
            $this->currentSite = FrontendSite::current();
        }

        if (!$this->currentPage) {
            $this->currentPage = FrontendPage::current();
        }
    }

    public function getCategorySlugFromUrl(...$slugs){
        $slugCollection = collect($slugs);

        $categoryCheckKey = $slugCollection->search('category');

        if($categoryCheckKey !== false){

            $categoryKey = $categoryCheckKey + 1;
            return $slugCollection->get($categoryKey);
        }

        return false;

        //if($slugCollection->contains())
        //dd($slugs, $slugCollection->contains('category'));
    }
}
