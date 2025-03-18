<?php

namespace Damiansb\PortalUnespAdapter;

class PortalUnespAdapter
{
    private static ?PortalUnespAdapter $adapter = null;
    private string $site_url                    = '';
    private string $ajax_hash                   = '#!/';
    private array $patch_url_blacklist          = [];

    private function __construct() {}

    public function setAjaxHash(string $ajax_hash): self
    {
        $this->ajax_hash = $ajax_hash;
        return $this;
    }

    public static function getInstance(): PortalUnespAdapter
    {
        if (self::$adapter === null)
            self::$adapter = new self();
        return self::$adapter;
    }

    public function addToPatchUrlBlacklist(string $var): self
    {
        $this->patch_url_blacklist[] = $var;
        return $this;
    }

    public function patchUrl(string $url, array|string|null $args = [], array|string|null $params = [], ?string $anchor = ''): string
    {
        extract(array_merge([
            'query_string' => null,   //caso não queira usar o GET 
            'dbo_route'    => null,   //nova rota do dbo, caso não seja a original
        ], $params ?? []));

        //chaves que serão removidas e adicionadas na URL
        $delete = [];
        $add    = [];

        //implodindo argumentos, caso seja passado como array.
        $args = implode('&', (array)$args);

        //pegando or originais e novos
        $query_string = $query_string ? $query_string : $_GET;

        parse_str($args, $new);

        //montando os arrays de adição e remoção de variaveis.
        foreach ($new as $key => $value) {
            if (strpos($key, '!') === 0) {
                $delete[] = ltrim($key, '!');
            } else {
                $add[$key] = $value;
            }
        }

        //removendo os itens do patch_url_blacklist
        foreach ($this->patch_url_blacklist as $var)
            $delete[] = $var;

        //remove as chaves do $delete do array de query_string.
        $query_string = array_diff_key($query_string, array_flip($delete));

        //fazendo o merge do query_string com as coisas novas
        $query_string = array_merge($query_string, $add);

        $query_string = array_filter($query_string);

        ksort($query_string);

        $url_padrao = $url . (sizeof($query_string) ? '?' : '') . http_build_query($query_string, '', '&', PHP_QUERY_RFC3986);

        $url_portal_unesp = str_replace(['?', '=', '&'], ['/v/', '::', '/'], $url_padrao);

        return $url_portal_unesp . ($anchor ? '/a/' . $anchor : '');
    }

    public function addAjaxHashToUrl(string $url): string
    {
        $parts    = parse_url($url);
        $path     = $parts['path'] ?? '';
        $query    = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        if (strpos($path, $this->ajax_hash) !== false)
            return $url;

        $baseUrl = $parts['scheme'] . '://' . $parts['host'];
        if (isset($parts['port']))
            $baseUrl .= ':' . $parts['port'];

        return $baseUrl . '/' . $this->ajax_hash . ltrim($path, '/') . $query . $fragment;
    }

    public function url(string $url, ?array $query_string = [], bool $ajax = true): string
    {
        $site_url = $this->getSiteUrl();
        $site_url = $ajax ? $this->addAjaxHashToUrl($site_url) : str_replace(['portal#!/', '#!/'], '', $site_url);
        $site_url = rtrim($site_url, "/ \t\n\r\0\x0B");
        $url      = $site_url . '/' . ltrim($url, "/ \t\n\r\0\x0B");

        $query_string = array_filter($query_string);

        ksort($query_string);

        $url_padrao = preg_replace('/\/$/is', '', $url) . (sizeof($query_string) ? '?' : '') . http_build_query($query_string, '', '&', PHP_QUERY_RFC3986);

        $url_portal_unesp = str_replace(['?', '=', '&'], ['/v/', '::', '/'], $url_padrao);

        return $url_portal_unesp;
    }

    public function convertSubmitAction()
    {
        return "const formData = new FormData(this); const params = new URLSearchParams(formData); let target = this.getAttribute('action') + '/v/' + params.toString(); target = target.replace(/=/g, '::').replace(/&/g, '/'); window.location.href = target; return false;";
    }

    public function getSiteUrl(): string
    {
        return $this->site_url;
    }

    public function setSiteUrl(string $site_url): self
    {
        $this->site_url = $site_url;
        return $this;
    }

    private function __clone() {}

    private function __wakeup() {}
}
