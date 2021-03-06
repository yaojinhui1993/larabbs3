<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

if (! function_exists('route_class')) {
    function route_class()
    {
        return str_replace('.', '-', Route::currentRouteName());
    }
}

if (! function_exists('category_nav_active')) {
    function category_nav_active($categoryId)
    {
        return active_class(
            (if_route('categories.show') && if_route_param('category', $categoryId))
        );
    }
}

if (! function_exists('make_excerpt')) {
    function make_excerpt($value, $length = 200)
    {
        $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));

        return Str::limit($excerpt, $length);
    }
}

if (! function_exists('model_admin_link')) {
    function model_admin_link($title, $model)
    {
        return model_link($title, $model, 'admin');
    }
}

if (! function_exists('model_link')) {
    function model_link($title, $model, $prefix = '')
    {
        $model_name = model_plural_name($model);

        $prefix = $prefix ? "/{$prefix}/": "/";

        $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

        return "<a href=\"{$url}\" target=\"_blank\">{$title}</a>";
    }
}

if (! function_exists('model_plural_name')) {
    function model_plural_name($model)
    {
        // eg: App\Models\User
        $full_class_name = get_class($model);

        // eg: User
        $class_name = class_basename($full_class_name);

        // FooBar => foo_bar
        $snake_case_name = Str::snake($class_name);

        return Str::plural($snake_case_name);
    }
}
