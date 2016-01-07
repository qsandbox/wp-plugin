<?php

class qSandbox_Util {
    /**
     * qSandbox_Util::array2dropdown_array();
     * ID -> Label
     * @param array $recordsArray
     * @return array
     */
    public static function array2dropdown_array( $records, $flags = 0 ) {
        $dropdown_elements = array();

        foreach ( $records as $item ) {
            // @todo if the id field doesn't exist preg_grep on keys to see what looks like an id and then use it's value.
            // cases such as d_id, or u_id etc.
            $id = empty( $item['id'] ) ? 0 : (int) $item['id'];
            $title = 'n/a';

            if ( ! empty( $item[ 'title' ] ) ) {
                $title = $item[ 'title' ];
            } elseif ( ! empty( $item[ 'label' ] ) ) {
                $title = $item[ 'label' ];
            } else {
                $title = $item['id'];
            }

            $title .= sprintf( ' [ID: %d]', $item['id'] );

            $dropdown_elements[ $id ] = $title;
        }

        return $dropdown_elements;
    }

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
