<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * [php82] Remplaçant minimal de illuminate/html (Form::...), qui n'existe plus pour Laravel 12.
 * Couvre ce qu'utilisent les vues du WebPanel : open, model, close, token, label, text, textarea,
 * hidden, file, select, checkbox, submit. Les valeurs viennent, dans l'ordre : de l'ancienne saisie
 * (après une erreur de validation), de la valeur donnée, puis du modèle passé à Form::model().
 */
class Form
{
    /** @var mixed modèle lié par Form::model() */
    protected static $model = null;

    public static function open(array $options = [])
    {
        $method = strtoupper($options['method'] ?? 'POST');
        $action = $options['url'] ?? null;
        if (isset($options['route'])) {
            $r = (array) $options['route'];
            $action = route(array_shift($r), $r);
        }
        if ($action === null) {
            $action = url()->current();
        }

        $attrs = array_diff_key($options, array_flip(['method', 'url', 'route', 'files', 'model']));
        $attrs['method'] = $method === 'GET' ? 'GET' : 'POST';
        $attrs['action'] = $action;
        $attrs['accept-charset'] = 'UTF-8';
        if (!empty($options['files'])) {
            $attrs['enctype'] = 'multipart/form-data';
        }

        $html = '<form' . self::attributes($attrs) . '>';
        if (!in_array($method, ['GET', 'POST'])) {
            $html .= '<input name="_method" type="hidden" value="' . e($method) . '">';
        }
        if ($method !== 'GET') {
            $html .= self::token()->toHtml();
        }
        return new HtmlString($html);
    }

    public static function model($model, array $options = [])
    {
        self::$model = $model;
        return self::open($options);
    }

    public static function close()
    {
        self::$model = null;
        return new HtmlString('</form>');
    }

    public static function token()
    {
        return new HtmlString('<input name="_token" type="hidden" value="' . e(csrf_token()) . '">');
    }

    public static function label($name, $value = null, array $options = [])
    {
        $value = $value ?? ucwords(str_replace('_', ' ', $name));
        return new HtmlString('<label for="' . e($name) . '"' . self::attributes($options) . '>' . e($value) . '</label>');
    }

    public static function input($type, $name, $value = null, array $options = [])
    {
        if (!isset($options['id']) && $type !== 'hidden' && $type !== 'submit') {
            $options['id'] = $name;
        }
        if ($type !== 'file' && $type !== 'password') {
            $value = self::value($name, $value);
        } else {
            $value = null;
        }
        $attrs = array_merge(['name' => $name, 'type' => $type], $value !== null ? ['value' => $value] : [], $options);
        return new HtmlString('<input' . self::attributes($attrs) . '>');
    }

    public static function text($name, $value = null, array $options = [])
    {
        return self::input('text', $name, $value, $options);
    }

    public static function number($name, $value = null, array $options = [])
    {
        return self::input('number', $name, $value, $options);
    }

    public static function email($name, $value = null, array $options = [])
    {
        return self::input('email', $name, $value, $options);
    }

    public static function password($name, array $options = [])
    {
        return self::input('password', $name, null, $options);
    }

    public static function hidden($name, $value = null, array $options = [])
    {
        return self::input('hidden', $name, $value, $options);
    }

    public static function file($name, array $options = [])
    {
        return self::input('file', $name, null, $options);
    }

    public static function textarea($name, $value = null, array $options = [])
    {
        $options['id'] = $options['id'] ?? $name;
        $options = array_merge(['name' => $name, 'cols' => 50, 'rows' => 10], $options);
        return new HtmlString('<textarea' . self::attributes($options) . '>' . e((string) self::value($name, $value)) . '</textarea>');
    }

    public static function select($name, $list = [], $selected = null, array $options = [])
    {
        if ($list instanceof \Illuminate\Support\Collection) {
            $list = $list->all();
        }
        $selected = self::value($name, $selected);
        if ($selected instanceof \Illuminate\Support\Collection) {
            $selected = $selected->all();
        }
        $selected = array_map('strval', (array) $selected);
        $options['id'] = $options['id'] ?? rtrim($name, '[]');
        $options['name'] = $name;

        $html = '<select' . self::attributes($options) . '>';
        foreach ((array) $list as $value => $display) {
            $sel = in_array((string) $value, $selected, true) ? ' selected="selected"' : '';
            $html .= '<option value="' . e($value) . '"' . $sel . '>' . e($display) . '</option>';
        }
        return new HtmlString($html . '</select>');
    }

    public static function checkbox($name, $value = 1, $checked = null, array $options = [])
    {
        $current = self::value($name, null);
        if (session()->hasOldInput()) {
            $isChecked = (string) old($name) === (string) $value;
        } elseif ($checked !== null) {
            $isChecked = (bool) $checked;
        } else {
            $isChecked = $current !== null && (string) $current === (string) $value;
        }
        $attrs = array_merge(['name' => $name, 'type' => 'checkbox', 'value' => $value], $options);
        if ($isChecked) {
            $attrs['checked'] = 'checked';
        }
        return new HtmlString('<input' . self::attributes($attrs) . '>');
    }

    public static function submit($value = null, array $options = [])
    {
        $attrs = array_merge(['type' => 'submit', 'value' => $value], $options);
        return new HtmlString('<input' . self::attributes($attrs) . '>');
    }

    /** Valeur d'un champ : ancienne saisie, puis valeur donnée, puis modèle lié. */
    protected static function value($name, $value)
    {
        $key = rtrim(str_replace(['[', ']'], ['.', ''], $name), '.');
        if (session()->hasOldInput($key)) {
            return old($key);
        }
        if ($value !== null) {
            return $value;
        }
        if (self::$model !== null) {
            $m = self::$model;
            if (is_array($m)) {
                return $m[$key] ?? null;
            }
            return data_get($m, $key);
        }
        return null;
    }

    protected static function attributes(array $attrs)
    {
        $html = '';
        foreach ($attrs as $k => $v) {
            if ($v === null || $v === false) {
                continue;
            }
            if (is_int($k)) {
                // attribut sans valeur, ex. 'multiple' -> multiple="multiple" (comme illuminate/html)
                $html .= ' ' . e($v) . '="' . e($v) . '"';
                continue;
            }
            $html .= ' ' . e($k) . '="' . e($v) . '"';
        }
        return $html;
    }
}
