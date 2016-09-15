<?php

use Illuminate\Support\Str;

if (! function_exists('get_real_ip')) {

    function get_real_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

if (! function_exists('get_base_url')) {

    function get_base_url()
    {
        $base_url  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
        $base_url .= $_SERVER["SERVER_NAME"];
        $base_url .= ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);

        return $base_url;
    }
}

if (! function_exists('get_current_url')) {

    function get_current_url()
    {
        return get_base_url().$_SERVER["REQUEST_URI"];
    }
}

if (! function_exists('avatar')) {

    function avatar(App\Models\User $user, $size)
    {
        $fname = base64_encode($user->email).".png";

        if (Option::get('avatar_query_string') == "1") {
            $fname .= '?v='.$user->getAvatarId();
        }

        return url("avatar/$size/$fname");
    }
}

if (! function_exists('assets')) {

    function assets($relative_uri)
    {
        // add query string to fresh cache
        if (Str::startsWith($relative_uri, 'css') || Str::startsWith($relative_uri, 'js')) {
            return url("resources/dist/$relative_uri")."?v=".config('app.version');
        } else {
            return url("resources/$relative_uri");
        }
    }
}

if (! function_exists('json')) {

    function json()
    {
        @header('Content-type: application/json; charset=utf-8');
        $args = func_get_args();

        if (count($args) == 1 && is_array($args[0])) {
            return Response::json($args[0]);
        } elseif(count($args) == 2) {
            return Response::json([
                'errno' => $args[1],
                'msg'   => $args[0]
            ]);
        }
    }
}

if (! function_exists('bs_footer')) {

    function bs_footer($page_identification = "")
    {
        $scripts = [
            assets('js/app.min.js'),
            assets('lang/'.session('locale', config('app.locale')).'/locale.js'),
            assets('js/general.js')
        ];

        if ($page_identification !== "") {
            $scripts[] = assets("js/$page_identification.js");
        }

        foreach ($scripts as $script) {
            echo "<script type=\"text/javascript\" src=\"$script\"></script>";
        }

        if (Session::has('msg')): ?>
        <script>
            toastr.info('{{ Session::pull('msg') }}');
        </script>
        <?php endif;

        echo '<script>'.Option::get("custom_js").'</script>';
    }
}

if (! function_exists('bs_header')) {

    function bs_header($page_identification = "")
    {
        $styles = [
            assets('css/app.min.css'),
            assets('vendor/skins/'.Option::get('color_scheme').'.min.css')
        ];

        if ($page_identification !== "") {
            $styles[] = assets("css/$page_identification.css");
        }

        foreach ($styles as $style) {
            echo "<link rel=\"stylesheet\" href=\"$style\">";
        }

        echo '<style>'.Option::get("custom_css").'</style>';
    }
}
