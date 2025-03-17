<?php

namespace Damiansb\PortalUnespAdapter;

class PortalUnespAdapter
{
    private static ?PortalUnespAdapter $adapter = null;
    private string $site_url = '';

    private function __construct() {}

    public static function getInstance(): PortalUnespAdapter
    {
        if (self::$adapter === null)
            self::$adapter = new self();
        return self::$adapter;
    }

    public function patchUrl($url, $args = [], $params = [])
    {
        extract(array_merge([
            'query_string' => null, //caso não queira usar o GET 
            'dbo_route' => null, //nova rota do dbo, caso não seja a original
        ], $params));

        //chaves que serão removidas e adicionadas na URL
        $delete = [];
        $add = [];

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

        //remove as chaves do $delete do array de query_string.
        $query_string = array_diff_key($query_string, array_flip($delete));

        //fazendo o merge do query_string com as coisas novas
        $query_string = array_merge($query_string, $add);

        $query_string = array_filter($query_string);

        ksort($query_string);

        $url_padrao = $url . (sizeof($query_string) ? '?' : '') . http_build_query($query_string, '', '&', PHP_QUERY_RFC3986);

        $url_portal_unesp = str_replace(['?', '=', '&'], ['/v/', '::', '/'], $url_padrao);

        return $url_portal_unesp;
    }

    public function url($url, $query_string = [])
    {
        $site_url = $this->getSiteUrl();
        $url      = $site_url . $url;

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

    public function setSiteUrl(string $site_url): void
    {
        $this->site_url = $site_url;
    }

    private function __clone() {}

    private function __wakeup() {}
}
