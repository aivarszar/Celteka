<?php
/**
 * FormHelper
 * Formu palīgfunkcijas
 */

class FormHelper {
    /**
     * Ģenerē CSRF input lauku
     */
    public static function csrfField() {
        $token = SecurityHelper::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . SecurityHelper::escape($token) . '">';
    }

    /**
     * Ģenerē method spoofing lauku (PUT, DELETE, etc.)
     */
    public static function methodField($method) {
        return '<input type="hidden" name="_method" value="' . SecurityHelper::escape(strtoupper($method)) . '">';
    }

    /**
     * Iegūst vecās vērtības (pēc validācijas kļūdas)
     */
    public static function old($key, $default = '') {
        $oldInput = Session::get('old_input', []);
        return $oldInput[$key] ?? $default;
    }

    /**
     * Ģenerē select options
     */
    public static function selectOptions(array $options, $selected = null, $placeholder = null) {
        $html = '';

        if ($placeholder) {
            $html .= '<option value="">' . SecurityHelper::escape($placeholder) . '</option>';
        }

        foreach ($options as $value => $label) {
            $selectedAttr = ($value == $selected) ? ' selected' : '';
            $html .= '<option value="' . SecurityHelper::escape($value) . '"' . $selectedAttr . '>';
            $html .= SecurityHelper::escape($label);
            $html .= '</option>';
        }

        return $html;
    }

    /**
     * Ģenerē checkbox
     */
    public static function checkbox($name, $value = '1', $checked = false, $attributes = []) {
        $html = '<input type="checkbox" name="' . SecurityHelper::escape($name) . '"';
        $html .= ' value="' . SecurityHelper::escape($value) . '"';

        if ($checked) {
            $html .= ' checked';
        }

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';

        return $html;
    }

    /**
     * Ģenerē radio button
     */
    public static function radio($name, $value, $checked = false, $attributes = []) {
        $html = '<input type="radio" name="' . SecurityHelper::escape($name) . '"';
        $html .= ' value="' . SecurityHelper::escape($value) . '"';

        if ($checked) {
            $html .= ' checked';
        }

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';

        return $html;
    }

    /**
     * Ģenerē text input
     */
    public static function text($name, $value = '', $attributes = []) {
        $html = '<input type="text" name="' . SecurityHelper::escape($name) . '"';
        $html .= ' value="' . SecurityHelper::escape($value) . '"';

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';

        return $html;
    }

    /**
     * Ģenerē password input
     */
    public static function password($name, $attributes = []) {
        $html = '<input type="password" name="' . SecurityHelper::escape($name) . '"';

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';

        return $html;
    }

    /**
     * Ģenerē email input
     */
    public static function email($name, $value = '', $attributes = []) {
        $html = '<input type="email" name="' . SecurityHelper::escape($name) . '"';
        $html .= ' value="' . SecurityHelper::escape($value) . '"';

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';

        return $html;
    }

    /**
     * Ģenerē textarea
     */
    public static function textarea($name, $value = '', $attributes = []) {
        $html = '<textarea name="' . SecurityHelper::escape($name) . '"';

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';
        $html .= SecurityHelper::escape($value);
        $html .= '</textarea>';

        return $html;
    }

    /**
     * Ģenerē hidden input
     */
    public static function hidden($name, $value = '') {
        return '<input type="hidden" name="' . SecurityHelper::escape($name) . '" value="' . SecurityHelper::escape($value) . '">';
    }

    /**
     * Ģenerē submit button
     */
    public static function submit($label, $attributes = []) {
        $html = '<button type="submit"';

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';
        $html .= SecurityHelper::escape($label);
        $html .= '</button>';

        return $html;
    }

    /**
     * Ģenerē button
     */
    public static function button($label, $attributes = []) {
        $html = '<button type="button"';

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';
        $html .= SecurityHelper::escape($label);
        $html .= '</button>';

        return $html;
    }

    /**
     * Atver formu
     */
    public static function open($action, $method = 'POST', $attributes = []) {
        $html = '<form action="' . SecurityHelper::escape($action) . '" method="';

        // PUT, DELETE, PATCH methods tiek emulēti ar POST
        $realMethod = strtoupper($method);
        $formMethod = in_array($realMethod, ['GET', 'POST']) ? $realMethod : 'POST';

        $html .= $formMethod . '"';

        foreach ($attributes as $attr => $attrValue) {
            $html .= ' ' . SecurityHelper::escape($attr) . '="' . SecurityHelper::escape($attrValue) . '"';
        }

        $html .= '>';

        // Pievienot CSRF token POST formām
        if ($formMethod === 'POST') {
            $html .= self::csrfField();
        }

        // Pievienot method spoofing, ja nepieciešams
        if (!in_array($realMethod, ['GET', 'POST'])) {
            $html .= self::methodField($realMethod);
        }

        return $html;
    }

    /**
     * Aizver formu
     */
    public static function close() {
        return '</form>';
    }

    /**
     * Parāda validācijas kļūdas
     */
    public static function errors($field = null) {
        $errors = Session::flash('errors', []);

        if (empty($errors)) {
            return '';
        }

        // Ja ir norādīts konkrēts lauks
        if ($field !== null) {
            if (isset($errors[$field])) {
                return '<span class="error-message">' . SecurityHelper::escape($errors[$field]) . '</span>';
            }
            return '';
        }

        // Atgriež visas kļūdas
        $html = '<div class="alert alert-error"><ul>';
        foreach ($errors as $error) {
            if (is_array($error)) {
                foreach ($error as $msg) {
                    $html .= '<li>' . SecurityHelper::escape($msg) . '</li>';
                }
            } else {
                $html .= '<li>' . SecurityHelper::escape($error) . '</li>';
            }
        }
        $html .= '</ul></div>';

        return $html;
    }

    /**
     * Pārbauda, vai ir kļūda konkrētam laukam
     */
    public static function hasError($field) {
        $errors = Session::get('errors', []);
        return isset($errors[$field]);
    }

    /**
     * Ģenerē error class, ja ir kļūda
     */
    public static function errorClass($field, $class = 'is-invalid') {
        return self::hasError($field) ? $class : '';
    }
}
