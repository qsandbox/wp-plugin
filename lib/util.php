<?php

class qSandbox_Util {
    /**
     * generates HTML select
     * qSandbox_Util::html_select()
     * @param type $name
     * @param type $sel
     * @param type $options
     * @param type $attr
     * @return string
     */
    public static function html_select($name = '', $sel = null, $options = array(), $attr = '') {
        // if ID is not supplied we'll
        if (stripos($attr, 'id=') === false) {
            $id = strtolower($name);
            $id = preg_replace('#[^\w-]#si', '-', $id);
            $id = trim($id, '-');
            $attr .= sprintf(" id='%s' ", esc_attr($name));
        }

        $html = "\n" . '<select name="' . esc_attr($name) . '" ' . $attr . '>' . "\n";

        foreach ( $options as $key => $label ) {
            $selected = $sel == $key ? ' selected="selected"' : '';
            $html .= "\t<option value='$key' $selected>$label</option>\n";
        }

        $html .= '</select>';
        $html .= "\n";

        return $html;
    }
}
