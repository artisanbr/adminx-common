<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Abstract\CustomListItemAbstract;

use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Models\Traits\HasSEO;

abstract class CustomListItemFrontendAbstract extends CustomListItemAbstract
{
    use HasSEO;

    public function prepareFrontendBuild($buildMeta = false): FrontendBuildObject
    {
        $frontendBuild = new FrontendBuildObject();


        //Inicio do body
        $frontendBuild->body->id = "list-item-{$this->public_id}";
        $frontendBuild->body->class .= " list-item-{$this->public_id}";
        //$frontendBuild->body->addBefore($this->assets->js->before_body_html ?? '');

        //Fim do body
        //$frontendBuild->body->addAfter($this->assets->js->after_body_html ?? '');


        /*$frontendBuild->seo->fill([
                                      ...($this->data->seo?->toArray() ?? []),
                                      'title'       => $this->getTitle(),
                                      'description' => $this->getDescription(),
                                      'keywords'    => $this->getKeywords(),
                                      'image_url'   => $this->seoImage(),
                                  ]);*/


        $frontendBuild->seo->fill([
                                      'title'         => $this->getTitle(),
                                      'title_prefix'  => "{{ site.getTitle() }} - {{ page.getTitle() }}",
                                      'description'   => $this->getDescription(),
                                      'keywords'      => $this->getKeywords(),
                                      'image_url'     => $this->seoImage(),
                                      'published_at'  => $this->created_at->toIso8601String(),
                                      'updated_at'    => $this->updated_at->toIso8601String(),
                                      'canonical_uri' => $this->uri,
                                      'document_type' => 'page',
                                      'html'          => '',
                                  ]);

        /*if ($buildMeta) {
            $frontendBuild->meta->reset();
            $frontendBuild->meta->registerSeoForPageInternal($this->list->page_internal, $this);
            //$frontendBuild->head->addBefore($frontendBuild->meta->toHtml());
            $frontendBuild->seo->html = $frontendBuild->meta->toHtml();
        }*/

        return $frontendBuild;
    }

    //region Attributes
    //region GET's
    //protected function getAttribute(){return $this->attributes[""];}
    protected function getSeoAttribute(): Seo
    {
        return $this->data->seo;
    }
    //endregion

    //region SET's
    //protected function setAttribute($value){}

    protected function setSeoAttribute($value): static
    {
        if (is_array($value)) {
            $this->data->seo->fill($value);
        }
        else /*if(get_class($value) === Seo::class)*/ {
            $this->data->seo = $value;
        }

        return $this;
    }

    //endregion
    //endregion

    //region OVERRIDE
    public function save(array $options = [])
    {
        if (parent::save($options)) {
            $this->data->frontend_build = $this->prepareFrontendBuild();

            return parent::save($options);
        }


        return false;
    }
    //endregion
}
