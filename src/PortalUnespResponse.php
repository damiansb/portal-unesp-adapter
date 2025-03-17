<?php

namespace Damiansb\PortalUnespAdapter;

class PortalUnespResponse
{

    private ?string $body            = null;
    private string|bool|null $title  = null;
    private array $breadcrumbs_stack = [];

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getTitle(): string|bool|null
    {
        return $this->title;
    }

    public function setTitle(string|bool $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function output(): string
    {
        $output = [];

        if ($this->getBody() !== null) {
            $output['corpo'] = $this->getBody();
        }

        if ($this->getTitle() !== null) {
            $output['titulo'] = $this->getTitle();
        }

        if (sizeof($this->breadcrumbs_stack) > 0) {
            $output['breadcrumbs']['push'] = $this->breadcrumbs_stack;
        };

        return json_encode($output);
    }

    public function pushBreadcrumb(string $label, ?string $url = null): self
    {
        $this->breadcrumbs_stack[] = [
            'label' => $label,
            'url'   => $url
        ];
        return $this;
    }
}
